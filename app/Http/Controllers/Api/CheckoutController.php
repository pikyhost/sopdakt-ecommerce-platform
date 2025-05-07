<?php

namespace App\Http\Controllers\Api;

use App\Enums\OrderStatus;
use App\Enums\TransactionType;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCheckoutRequest;
use App\Mail\GuestInvitationMail;
use App\Mail\OrderStatusMail;
use App\Models\Cart;
use App\Models\Contact;
use App\Models\Invitation;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Coupon;
use App\Services\StockLevelNotifier;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class CheckoutController extends Controller
{
    /**
     * Process the checkout and place an order
     *
     * This endpoint handles the checkout process, validating user or guest contact details, processing payments,
     * and creating an order. It supports both authenticated users and guests, with optional account creation for guests.
     * Payment methods include Paymob (returns a payment iframe URL) and Cash on Delivery (creates the order directly).
     * The endpoint updates user/contact information, manages cart items, updates stock, sends email/WhatsApp notifications,
     * and integrates with JT Express for shipping. It also validates and applies any coupons associated with the cart.
     * The response includes the order details or payment URL on success, or an error message for failures (e.g., empty cart, invalid payment, invalid coupon).
     *
     * @group Checkout
     * @bodyParam payment_method_id integer required The ID of the payment method (1 for COD, 2 for Paymob). Example: 1
     * @bodyParam name string required The name of the customer. Example: John Doe
     * @bodyParam email string required The email address of the customer. Must be unique if creating an account. Example: john.doe@example.com
     * @bodyParam phone string required The primary phone number of the customer (min 10 characters). Example: 01025263865
     * @bodyParam second_phone string required A secondary phone number, different from the primary (min 10 characters). Example: 01125263865
     * @bodyParam address string required The shipping address. Example: 123 Main St, Cairo
     * @bodyParam notes string|null Optional notes for the order. Example: Please deliver after 5 PM
     * @bodyParam create_account boolean Whether to create a user account for guests. Example: true
     * @bodyParam password string|null Required if create_account is true (min 6 characters). Example: password123
     * @response 201 {
     *     "data": {
     *         "order_id": 1,
     *         "total": 150.00,
     *         "status": "shipping",
     *         "tracking_number": null,
     *         "created_at": "2025-04-30T12:00:00.000000Z"
     *     },
     *     "message": "Order placed successfully"
     * }
     * @response 200 {
     *     "data": {
     *         "payment_url": "https://paymob.com/iframe/123456"
     *     },
     *     "message": "Payment initiated successfully"
     * }
     * @response 422 {
     *     "message": "The given data was invalid.",
     *     "errors": {
     *         "email": ["The email has already been taken."],
     *         "phone": ["The phone number is blocked. Please contact support."],
     *         "payment_method_id": ["The selected payment method is invalid."]
     *     }
     * }
     * @response 422 {
     *     "error": "Invalid or expired coupon."
     * }
     * @response 404 {
     *     "error": "Cart is empty or not found",
     *     "support_link": "https://your-domain.com/contact-us"
     * }
     * @response 500 {
     *     "error": "An unexpected error occurred during checkout. Please try again.",
     *     "support_link": "https://your-domain.com/contact-us"
     * }
     * @param StoreCheckoutRequest $request
     * @return JsonResponse
     */
    public function store(StoreCheckoutRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $checkoutToken = (string) Str::uuid();
            $sessionId = session()->getId();

            // Block inactive users
            if (Auth::check() && !Auth::user()->is_active) {
                Log::warning('Inactive user attempted checkout', ['user_id' => Auth::id()]);
                return response()->json([
                    'error' => 'Your account is not active. Please contact support.',
                    'support_link' => route('contact.us'),
                ], 403);
            }

            // Check for duplicate submission
            if (Order::where('checkout_token', $checkoutToken)->exists()) {
                Log::info('Duplicate checkout attempt', ['checkout_token' => $checkoutToken]);
                return response()->json([
                    'error' => 'Your order is already being processed. Please wait.',
                ], 409);
            }

            // Get cart
            $cart = Cart::where(function ($query) use ($sessionId) {
                if (Auth::check()) {
                    $query->where('user_id', Auth::id());
                } else {
                    $query->where('session_id', $sessionId);
                }
            })->with('items.product')->latest()->first();

            if (!$cart || $cart->items->isEmpty()) {
                Log::info('Empty cart during checkout', ['user_id' => Auth::id(), 'session_id' => $sessionId]);
                return response()->json([
                    'error' => 'Cart is empty or not found',
                    'support_link' => route('contact.us'),
                ], 404);
            }

            // Validate coupon if applied
            if ($cart->coupon_id) {
                $coupon = Coupon::where('id', $cart->coupon_id)
                    ->where('is_active', true)
                    ->whereHas('discount', function ($q) {
                        $q->where('is_active', true)
                            ->where(function ($q2) {
                                $q2->whereNull('starts_at')->orWhere('starts_at', '<=', now());
                            })
                            ->where(function ($q2) {
                                $q2->whereNull('ends_at')->orWhere('ends_at', '>=', now());
                            });
                    })
                    ->first();

                if (!$coupon) {
                    Log::warning('Invalid or expired coupon during checkout', ['coupon_id' => $cart->coupon_id]);
                    return response()->json([
                        'error' => 'Invalid or expired coupon.',
                        'support_link' => route('contact.us'),
                    ], 422);
                }

                // Check coupon usage limits
                if ($coupon->total_usage_limit) {
                    $totalUsages = $coupon->usages()->count();
                    if ($totalUsages >= $coupon->total_usage_limit) {
                        Log::warning('Coupon usage limit reached', ['coupon_id' => $cart->coupon_id]);
                        return response()->json([
                            'error' => 'Coupon usage limit reached.',
                            'support_link' => route('contact.us'),
                        ], 422);
                    }
                }

                if ($coupon->usage_limit_per_user && Auth::check()) {
                    $userUsages = $coupon->usages()->where('user_id', Auth::id())->count();
                    if ($userUsages >= $coupon->usage_limit_per_user) {
                        Log::warning('Coupon usage limit per user reached', ['coupon_id' => $cart->coupon_id, 'user_id' => Auth::id()]);
                        return response()->json([
                            'error' => 'Coupon usage limit per user reached.',
                            'support_link' => route('contact.us'),
                        ], 422);
                    }
                }
            }

            // Save contact data
            $contact = $this->saveContact($data, $cart);
            Log::info('Contact info saved', ['contact_id' => $contact->id ?? null, 'user_id' => Auth::id()]);

            // Store checkout session data
            session([
                'pending_checkout' => [
                    'user_id' => Auth::id(),
                    'contact_id' => Auth::check() ? null : $contact->id,
                    'cart_id' => $cart->id,
                    'notes' => $data['notes'] ?? null,
                    'checkout_token' => $checkoutToken,
                    'payment_method_id' => $data['payment_method_id'],
                ]
            ]);
            Log::info('Checkout session stored', ['checkout_token' => $checkoutToken]);

            // Handle Paymob payment
            if ($data['payment_method_id'] == 2) {
                return $this->processPaymobPayment($cart, $contact, $checkoutToken);
            }

            // Handle COD or other methods
            return $this->createOrderManually($cart, $contact, $data, $checkoutToken);

        } catch (\Exception $e) {
            Log::critical('Unexpected error in checkout', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'error' => 'An unexpected error occurred during checkout. Please try again.',
                'support_link' => route('contact.us'),
            ], 500);
        }
    }

    /**
     * Save or update contact/user information
     */
    private function saveContact(array $data, Cart $cart): User|Contact
    {
        if (Auth::check()) {
            // Update authenticated user
            $user = Auth::user();

            if ($user->email !== $data['email'] && User::where('email', $data['email'])->exists()) {
                throw new \Exception('This email is already in use by another user.');
            }

            $user->update([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'second_phone' => $data['second_phone'],
            ]);

            $primaryAddress = $user->addresses()->where('is_primary', true)->first();

            if ($primaryAddress) {
                $primaryAddress->update([
                    'address' => $data['address'],
                    'country_id' => $cart->country_id ?? null,
                    'governorate_id' => $cart->governorate_id ?? null,
                    'city_id' => $cart->city_id ?? null,
                ]);
            } else {
                $user->addresses()->create([
                    'address' => $data['address'],
                    'address_name' => 'home',
                    'country_id' => $cart->country_id ?? null,
                    'governorate_id' => $cart->governorate_id ?? null,
                    'city_id' => $cart->city_id ?? null,
                    'is_primary' => true,
                ]);
            }

            return $user;
        } else {
            $sessionId = session()->getId();

            if ($data['create_account']) {
                // Create new user
                $user = User::create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'phone' => $data['phone'],
                    'second_phone' => $data['second_phone'],
                    'address' => $data['address'],
                    'password' => bcrypt($data['password']),
                    'country_id' => $cart->country_id,
                    'governorate_id' => $cart->governorate_id,
                    'city_id' => $cart->city_id,
                ]);

                Auth::login($user);

                $user->addresses()->create([
                    'address' => $data['address'],
                    'country_id' => $cart->country_id ?? null,
                    'governorate_id' => $cart->governorate_id ?? null,
                    'city_id' => $cart->city_id ?? null,
                    'address_name' => 'home',
                    'is_primary' => true,
                ]);

                Contact::where('session_id', $sessionId)->delete();

                return $user;
            } else {
                // Update or create guest contact
                $guestContact = Contact::where('session_id', $sessionId)->first();

                $contactData = [
                    'session_id' => $sessionId,
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'phone' => $data['phone'],
                    'second_phone' => $data['second_phone'],
                    'address' => $data['address'],
                    'country_id' => $cart->country_id,
                    'governorate_id' => $cart->governorate_id,
                    'city_id' => $cart->city_id,
                ];

                if ($guestContact) {
                    $guestContact->update($contactData);
                } else {
                    $guestContact = Contact::create($contactData);
                }

                return $guestContact;
            }
        }
    }

    /**
     * Process Paymob payment
     */
    private function processPaymobPayment(Cart $cart, User|Contact $contact, string $checkoutToken): JsonResponse
    {
        try {
            if (!is_numeric($cart->total)) {
                Log::error('Invalid cart total', ['total' => $cart->total]);
                return response()->json([
                    'error' => 'Invalid cart total.',
                ], 400);
            }

            $response = Http::post(url('/api/payment/process'), [
                'amount_cents' => (int) ($cart->total * 100),
                'contact_email' => $contact->email,
                'name' => $contact->name,
            ]);

            Log::info('Payment API Response', [
                'status' => $response->status(),
                'body' => $response->json(),
            ]);

            $data = $response->json();

            if (isset($data['success']) && $data['success'] === true && isset($data['iframe_url'])) {
                return response()->json([
                    'data' => [
                        'payment_url' => $data['iframe_url'],
                        'checkout_token' => $checkoutToken,
                    ],
                    'message' => 'Payment initiated successfully',
                ], 200);
            }

            Log::error('Payment response invalid', ['response' => $data]);
            return response()->json([
                'error' => 'Failed to initiate payment. Invalid response.',
            ], 400);

        } catch (\Exception $e) {
            Log::error('Payment exception', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'error' => 'Unexpected error during payment: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Create order manually (e.g., for COD)
     */
    private function createOrderManually(Cart $cart, User|Contact $contact, array $data, string $checkoutToken): JsonResponse
    {
        DB::beginTransaction();

        try {
            $orderData = [
                'payment_method_id' => $data['payment_method_id'],
                'user_id' => Auth::id(),
                'shipping_type_id' => $cart->shipping_type_id,
                'coupon_id' => $cart->coupon_id,
                'shipping_cost' => $cart->shipping_cost,
                'country_id' => $cart->country_id,
                'governorate_id' => $cart->governorate_id,
                'city_id' => $cart->city_id,
                'tax_percentage' => $cart->tax_percentage,
                'tax_amount' => $cart->tax_amount,
                'subtotal' => $cart->subtotal,
                'total' => $cart->total,
                'status' => OrderStatus::Shipping,
                'notes' => $data['notes'] ?? null,
                'checkout_token' => $checkoutToken,
                'tracking_number' => null, // Explicitly set to null
            ];

            if (!Auth::check() && $contact instanceof Contact) {
                $orderData['contact_id'] = $contact->id;
            }

            $order = Order::create($orderData);

            // Record coupon usage if applicable
            if ($cart->coupon_id && Auth::check()) {
                Coupon::find($cart->coupon_id)->usages()->create([
                    'user_id' => Auth::id(),
                    'order_id' => $order->id,
                ]);
            }

            foreach ($cart->items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'bundle_id' => $item->bundle_id,
                    'size_id' => $item->size_id,
                    'color_id' => $item->color_id,
                    'quantity' => $item->quantity,
                    'price_per_unit' => $item->price_per_unit,
                    'subtotal' => $item->subtotal,
                ]);

                if ($item->product_id) {
                    $product = Product::find($item->product_id);

                    if ($product) {
                        $product->decrement('quantity', $item->quantity);

                        $variant = $product->productColors()
                            ->where('color_id', $item->color_id)
                            ->first()
                            ?->productColorSizes()
                            ->where('size_id', $item->size_id)
                            ->first();

                        if ($variant) {
                            $variant->decrement('quantity', $item->quantity);
                        }

                        $product->inventory()?->decrement('quantity', $item->quantity);

                        Transaction::create([
                            'product_id' => $item->product_id,
                            'type' => TransactionType::SALE,
                            'quantity' => $item->quantity,
                            'notes' => "Sale of {$item->quantity} units for Order #{$order->id}",
                        ]);
                    }
                }
            }

            // Notify admins of low stock
            $productIds = $order->items->pluck('product_id')->filter()->unique();
            $products = Product::whereIn('id', $productIds)->get();
            StockLevelNotifier::notifyAdminsForLowStock($products);

            // Clear cart
            $cart->items()->delete();
            $cart->delete();

            // Send email notification
            $recipientEmail = Auth::check() ? Auth::user()->email : ($contact->email ?? null);
            $language = Auth::check() ? Auth::user()->preferred_language : (request()->getPreferredLanguage(['en', 'ar']) ?? 'en');

            if ($recipientEmail) {
                Mail::to($recipientEmail)->locale($language)->send(new OrderStatusMail($order, $order->status));
            }

            // Send guest invitation
            if (!Auth::check() && $contact instanceof Contact) {
                $locale = request()->getPreferredLanguage(['en', 'ar']) ?? 'en';
                $invitation = Invitation::create([
                    'email' => $contact->email,
                    'name' => $contact->name ?? null,
                    'phone' => $contact->phone ?? null,
                    'preferred_language' => $locale,
                    'role_id' => Role::where('name', UserRole::Client->value)->first()->id,
                ]);

                Mail::to($contact->email)->locale($locale)->send(new GuestInvitationMail($invitation));
            }

            // JT Express integration (prepare data but do not assign tracking number yet)
            $jtExpressData = $this->prepareJtExpressOrderData($order);
            $jtExpressResponse = $this->sendJtExpressRequest($jtExpressData); // Implement as needed
            $this->updateJtExpressOrder($order, 'pending', $jtExpressData, $jtExpressResponse);

            DB::commit();

            return response()->json([
                'data' => [
                    'order_id' => $order->id,
                    'total' => $order->total,
                    'status' => $order->status,
                    'tracking_number' => $order->tracking_number, // Should be null
                    'created_at' => $order->created_at->toIso8601String(),
                ],
                'message' => 'Order placed successfully',
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order creation failed', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'error' => 'We encountered an issue: ' . $e->getMessage(),
                'support_link' => route('contact.us'),
            ], 500);
        }
    }

    /**
     * Prepare JT Express order data
     */
    private function prepareJtExpressOrderData(Order $order): array
    {
        $data = [
            // Do not generate tracking_number here to keep it null in the order
            'weight' => 1.0, // Dynamic calculation needed
            'quantity' => $order->items->sum('quantity'),
            'remark' => implode(' , ', array_filter([
                'Notes: ' . ($order->notes ?? 'No notes'),
                $order->user?->name ? 'User: ' . $order->user->name : null,
                'Email: ' . ($order->user?->email ?? $order->contact?->email ?? null),
                'Phone: ' . ($order->user?->phone ?? $order->contact?->phone ?? null),
                'Address: ' . ($order->user?->address ?? $order->contact?->address ?? null),
            ])),
            'item_name' => $order->items->pluck('product.name')->implode(', '),
            'item_quantity' => $order->items->count(),
            'item_value' => $order->total,
            'item_currency' => 'EGP',
            'item_description' => $order->notes ?? 'No description provided',
        ];

        $data['sender'] = [
            'name' => 'Your Company Name',
            'company' => 'Your Company',
            'city' => 'Your City',
            'address' => 'Your Full Address',
            'mobile' => 'Your Contact Number',
            'countryCode' => 'Your Country Code',
            'prov' => 'Your Prov',
            'area' => 'Your Area',
            'town' => 'Your Town',
            'street' => 'Your Street',
            'addressBak' => 'Your Address Bak',
            'postCode' => 'Your Post Code',
            'phone' => 'Your Phone',
            'mailBox' => 'Your Mail Box',
            'areaCode' => 'Your Area Code',
            'building' => 'Your Building',
            'floor' => 'Your Floor',
            'flats' => 'Your Flats',
            'alternateSenderPhoneNo' => 'Your Alternate Sender Phone No',
        ];

        $data['receiver'] = [
            'name' => $order->user?->name ?? $order->contact?->name ?? 'Customer',
            'prov' => $order->governorate?->name ?? 'Unknown',
            'city' => $order->city?->name ?? 'Unknown',
            'address' => $order->user?->address ?? $order->contact?->address ?? 'Unknown',
            'mobile' => $order->user?->phone ?? $order->contact?->phone ?? 'Unknown',
            'company' => 'N/A',
            'countryCode' => $order->country?->code ?? 'EGY',
            'area' => 'Unknown',
            'town' => 'Unknown',
            'addressBak' => 'N/A',
            'street' => 'Unknown',
            'postCode' => 'Unknown',
            'phone' => $order->user?->phone ?? $order->contact?->phone ?? 'Unknown',
            'mailBox' => $order->user?->email ?? $order->contact?->email ?? 'N/A',
            'areaCode' => 'Unknown',
            'building' => 'Unknown',
            'floor' => 'Unknown',
            'flats' => 'Unknown',
            'alternateReceiverPhoneNo' => $order->user?->second_phone ?? $order->contact?->second_phone ?? 'Unknown',
        ];

        return $data;
    }

    /**
     * Send JT Express request (placeholder)
     */
    private function sendJtExpressRequest(array $data): array
    {
        // Implement actual JT Express API call here
        // This is a placeholder returning a mock response
        // Assume the API returns a tracking number if successful
        return [
            'code' => 1,
            'message' => 'JT Express order created',
            'tracking_number' => '#JT' . time() . rand(1000, 9999), // Example tracking number from API
        ];
    }

    /**
     * Update order with JT Express data
     */
    private function updateJtExpressOrder(Order $order, string $shippingStatus, array $jtExpressData, array $jtExpressResponse): void
    {
        if (isset($jtExpressResponse['code']) && $jtExpressResponse['code'] == 1) {
            $order->update([
                // Only update shipping_status and shipping_response, not tracking_number
                'shipping_status' => $shippingStatus,
                'shipping_response' => json_encode($jtExpressResponse),
            ]);
        }
    }
}
?>
