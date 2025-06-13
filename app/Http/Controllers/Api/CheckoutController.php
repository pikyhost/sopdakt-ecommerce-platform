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
use App\Models\City;
use App\Models\Governorate;
use App\Models\Country;
use App\Models\ShippingType;
use App\Models\Setting;
use App\Services\StockLevelNotifier;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class CheckoutController extends Controller
{
    /**
     * Save or update contact/user information
     */
    private function saveContact(array $data, Cart $cart): User|Contact
    {
        if (Auth::guard('sanctum')->check()) {
            // Update authenticated user
            $user = Auth::guard('sanctum')->user();

            if ($user->email !== $data['email'] && User::where('email', $data['email'])->exists()) {
                throw new \Exception('This email is already in use by another user.');
            }

            $user->update([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'second_phone' => $data['second_phone'] ?? null,
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

            // Handle shipping address if different from billing
            if (isset($data['ship_to_different_address']) && $data['ship_to_different_address']) {
                // Check if shipping address already exists
                $shippingAddress = $user->addresses()->where('address_name', 'shipping')->first();

                $shippingData = [
                    'address' => $data['shipping_address'],
                    'country_id' => $data['shipping_country_id'] ?? $cart->country_id,
                    'governorate_id' => $data['shipping_governorate_id'] ?? $cart->governorate_id,
                    'city_id' => $data['shipping_city_id'] ?? $cart->city_id,
                ];

                if ($shippingAddress) {
                    $shippingAddress->update($shippingData);
                } else {
                    $user->addresses()->create([
                        ...$shippingData,
                        'address_name' => 'shipping',
                        'is_primary' => false,
                    ]);
                }
            }

            return $user;
        } else {
            $sessionId = request()->header('x-session-id');

            if (isset($data['create_account']) && $data['create_account']) {
                // Create new user
                $user = User::create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'phone' => $data['phone'],
                    'second_phone' => $data['second_phone'] ?? null,
                    'password' => bcrypt($data['password']),
                    'country_id' => $cart->country_id,
                    'governorate_id' => $cart->governorate_id,
                    'city_id' => $cart->city_id,
                ]);

                $request->authenticate();
                $user = Auth::guard('sanctum')->user();

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
                    'second_phone' => $data['second_phone'] ?? null,
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

    private function createOrderManually(Cart $cart, User|Contact $contact, array $data, string $checkoutToken): JsonResponse
    {
        DB::beginTransaction();

        try {
            // Determine order address based on shipping preference
            $orderAddress = $data['address']; // Default to billing address

            // For authenticated users, check if they want to ship to different address
            if (Auth::guard('sanctum')->check() && isset($data['ship_to_different_address']) && $data['ship_to_different_address']) {
                $orderAddress = $data['shipping_address'];
            }

            $orderData = [
                'address' => $orderAddress,
                'payment_method_id' => $data['payment_method_id'],
                'user_id' => Auth::guard('sanctum')->id(),
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
                'status' => OrderStatus::Pending,
                'notes' => $data['notes'] ?? null,
                'checkout_token' => $checkoutToken,
                'tracking_number' => null,
            ];

            // Update order location data if shipping to different address
            if (Auth::guard('sanctum')->check() && isset($data['ship_to_different_address']) && $data['ship_to_different_address']) {
                $orderData['country_id'] = $data['shipping_country_id'] ?? $cart->country_id;
                $orderData['governorate_id'] = $data['shipping_governorate_id'] ?? $cart->governorate_id;
                $orderData['city_id'] = $data['shipping_city_id'] ?? $cart->city_id;
            }

            if (!Auth::guard('sanctum')->check() && $contact instanceof Contact) {
                $orderData['contact_id'] = $contact->id;
            }

            $order = Order::create($orderData);

            // Record coupon usage if applicable
            if ($cart->coupon_id && Auth::guard('sanctum')->check()) {
                Coupon::find($cart->coupon_id)->usages()->create([
                    'user_id' => Auth::guard('sanctum')->id(),
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
            $recipientEmail = Auth::guard('sanctum')->check() ? Auth::guard('sanctum')->user()->email : ($contact->email ?? null);
            $language = Auth::guard('sanctum')->check() ? Auth::guard('sanctum')->user()->preferred_language : (request()->getPreferredLanguage(['en', 'ar']) ?? 'en');

            if ($recipientEmail) {
                Mail::to($recipientEmail)->locale($language)->send(new OrderStatusMail($order, $order->status));
            }

            // Send guest invitation
            if (!Auth::guard('sanctum')->check() && $contact instanceof Contact) {
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

            DB::commit();

            return response()->json([
                'data' => [
                    'order_id' => $order->id,
                    'shipping_cost' => $order->shipping_cost,
                    'total' => $order->total,
                    'status' => $order->status,
                    'tracking_number' => $order->tracking_number,
                    'created_at' => $order->created_at->toIso8601String(),
                ],
                'message' => 'Order placed successfully',
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'We encountered an issue: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a new order
     */
    public function store(StoreCheckoutRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $checkoutToken = (string) Str::uuid();
            $sessionId = $request->header('x-session-id');

            // Block inactive users
            if (Auth::guard('sanctum')->check() && !Auth::guard('sanctum')->user()->is_active) {
                return response()->json([
                    'error' => 'Your account is not active. Please contact support.',
                ], 403);
            }

            // Check for duplicate submission
            if (Order::where('checkout_token', $checkoutToken)->exists()) {
                return response()->json([
                    'error' => 'Your order is already being processed. Please wait.',
                ], 409);
            }

            // Get cart
            $cart = Cart::where(function ($query) use ($sessionId) {
                if (Auth::guard('sanctum')->check()) {
                    $query->where('user_id', Auth::guard('sanctum')->id());
                } else {
                    $query->where('session_id', $sessionId);
                }
            })->with('items.product')->latest()->first();

            if (!$cart || $cart->items->isEmpty()) {
                return response()->json([
                    'error' => 'Cart is empty or not found',
                ], 404);
            }

            // Save contact data
            $contact = $this->saveContact($data, $cart);

            // Calculate subtotal
            $subTotal = $cart->items->sum(fn ($item) => $item->subtotal);

            // Calculate shipping cost
            $shippingCost = $this->updateShippingCost(
                $cart->shipping_type_id,
                $cart->items->map(function ($item) {
                    return [
                        'product_id' => $item->product_id,
                        'quantity' => $item->quantity,
                        'subtotal' => $item->subtotal,
                    ];
                })->toArray(),
                $cart->city_id,
                $cart->governorate_id,
                $cart->country_id
            );

            // Calculate discount if coupon is applied
            $discount = 0.0;
            if ($cart->coupon_id) {
                $coupon = Coupon::find($cart->coupon_id);
                if ($coupon) {
                    if ($coupon->type === 'discount_percentage') {
                        $discount = ($subTotal * $coupon->value) / 100;
                    } elseif ($coupon->type === 'discount_amount') {
                        $discount = $coupon->value;
                    } elseif ($coupon->type === 'free_shipping') {
                        $shippingCost = 0.0;
                    }
                }
            }

            // Update cart with shipping cost and recalculate total
            $taxPercentage = $this->getTaxPercentage();
            $taxAmount = ($subTotal * $taxPercentage) / 100;
            $total = max(0, $subTotal + $taxAmount + $shippingCost - $discount);

            $cart->update([
                'shipping_cost' => $shippingCost,
                'subtotal' => $subTotal,
                'tax_percentage' => $taxPercentage,
                'tax_amount' => $taxAmount,
                'total' => $total,
            ]);

            // Store checkout session data
            session([
                'pending_checkout' => [
                    'user_id' => Auth::guard('sanctum')->id(),
                    'contact_id' => $contact instanceof Contact ? $contact->id : null,
                    'cart_id' => $cart->id,
                    'notes' => $data['notes'] ?? null,
                    'checkout_token' => $checkoutToken,
                    'payment_method_id' => $data['payment_method_id'],
                ]
            ]);

            // Handle Paymob payment
            if ($data['payment_method_id'] == 100) {
                return $this->processPaymobPayment($cart, $contact, $checkoutToken);
            }

            // Handle COD or other methods
            return $this->createOrderManually($cart, $contact, $data, $checkoutToken);

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Calculate shipping cost for the cart
     */
    private function updateShippingCost($shippingTypeId, array $items, $cityId, $governorateId, $countryId): float
    {
        $totalShippingCost = 0.0;
        $hasChargeableItems = false;

        // Check if all items are free shipping
        foreach ($items as $item) {
            $product = Product::find($item['product_id'] ?? null);
            if ($product && !$product->is_free_shipping) {
                $hasChargeableItems = true;
                break;
            }
        }

        // If all products have free shipping, set cost to 0
        if (!$hasChargeableItems) {
            return 0.0;
        }

        // If shipping locations are disabled, force cost to 0
        if (!Setting::isShippingLocationsEnabled()) {
            return 0.0;
        }

        // Add shipping type cost (only if there are chargeable items and shipping is enabled)
        if ($shippingTypeId && Setting::isShippingEnabled()) {
            $shippingType = ShippingType::find($shippingTypeId);
            $totalShippingCost += $shippingType?->shipping_cost ?? 0.0;
        }

        // Get the highest shipping cost per item instead of summing them
        $highestShippingCost = 0.0;
        if (!empty($items) && ($cityId || $governorateId || $countryId)) {
            foreach ($items as $item) {
                $product = Product::find($item['product_id'] ?? null);
                if ($product && !$product->is_free_shipping) {
                    $cost = $this->calculateProductShippingCost($product, $cityId, $governorateId, $countryId);
                    if ($cost > $highestShippingCost) {
                        $highestShippingCost = $cost;
                    }
                }
            }
        }

        $finalCost = $totalShippingCost + $highestShippingCost;

        return $finalCost;
    }

    /**
     * Calculate shipping cost for a single product
     */
    private function calculateProductShippingCost(Product $product, $cityId, $governorateId, $countryId): float
    {
        // Check if product has free shipping
        if ($product->is_free_shipping) {
            return 0.0;
        }

        // If shipping locations are disabled, force cost to 0
        if (!Setting::isShippingLocationsEnabled()) {
            return 0.0;
        }

        // If no location is provided, fall back to product cost
        if (!$cityId && !$governorateId && !$countryId) {
            $cost = $product->cost ?? 0.0;
            return $cost;
        }

        // Get product-specific shipping cost
        $cost = $this->getProductShippingCost($product, $cityId, $governorateId, $countryId);

        if ($cost !== null) {
            return $cost;
        }

        // Fall back to location-based cost or product cost
        $cost = $this->getLocationBasedShippingCost($cityId, $governorateId, $countryId) ?? $product->cost ?? 0.0;

        return $cost;
    }

    private function getProductShippingCost(Product $product, $cityId, $governorateId, $countryId): ?float
    {
        if (!Setting::isShippingLocationsEnabled()) {
            return 0.0;
        }

        $shippingCosts = $product->shippingCosts()->get();

        if ($shippingCosts->isEmpty()) {
            return null;
        }

        // Priority 1: Check City
        foreach ($shippingCosts as $shippingCost) {
            if ($shippingCost->priority === 'City' && $cityId && $shippingCost->city_id == $cityId) {
                return (float) $shippingCost->cost;
            }
        }

        // Priority 2: Check Governorate
        foreach ($shippingCosts as $shippingCost) {
            if ($shippingCost->priority === 'Governorate' && $governorateId && $shippingCost->governorate_id == $governorateId) {
                return (float) $shippingCost->cost;
            }
        }

        // Priority 3: Check Shipping Zone
        foreach ($shippingCosts as $shippingCost) {
            if ($shippingCost->priority === 'Shipping Zone' && $governorateId && $shippingCost->shipping_zone_id) {
                $governorate = Governorate::find($governorateId);
                if ($governorate && $governorate->shippingZones()->where('shipping_zones.id', $shippingCost->shipping_zone_id)->exists()) {
                    // Check if there's a Governorate-specific cost (redundant but kept for safety)
                    $governorateCost = $shippingCosts->firstWhere(function ($cost) use ($governorateId) {
                        return $cost->priority === 'Governorate' && $cost->governorate_id == $governorateId;
                    });
                    if ($governorateCost) {
                        return (float) $governorateCost->cost;
                    }
                    return (float) $shippingCost->cost;
                }
            }
        }

        // Priority 4: Check Country
        foreach ($shippingCosts as $shippingCost) {
            if ($shippingCost->priority === 'Country' && $countryId && $shippingCost->country_id == $countryId) {
                return (float) $shippingCost->cost;
            }
        }

        // Priority 5: Check Country Group
        foreach ($shippingCosts as $shippingCost) {
            if ($shippingCost->priority === 'Country Group' && $countryId && $shippingCost->country_group_id) {
                $country = Country::find($countryId);
                if ($country && $country->countryGroups()->where('id', $shippingCost->country_group_id)->exists()) {
                    return (float) $shippingCost->cost;
                }
            }
        }

        return null;
    }

    /**
     * Get location-based shipping cost
     */
    private function getLocationBasedShippingCost($cityId, $governorateId, $countryId): ?float
    {
        // If shipping locations are disabled, return 0
        if (!Setting::isShippingLocationsEnabled()) {
            return 0.0;
        }

        // Check city cost
        if ($cityId) {
            $city = City::find($cityId);
            if ($city?->cost > 0) return (float) $city->cost;
        }

        // Check governorate or shipping zone cost
        if ($governorateId) {
            $governorate = Governorate::find($governorateId);
            if ($governorate?->cost > 0) return (float) $governorate->cost;

            $zone = $governorate->shippingZones()->first();
            if ($zone?->cost > 0) return (float) $zone->cost;
        }

        // Check country cost
        if ($countryId) {
            $country = Country::find($countryId);
            if ($country?->cost > 0) return (float) $country->cost;
        }

        return null;
    }

    /**
     * Get tax percentage
     */
    private static function getTaxPercentage(): float
    {
        return Setting::getTaxPercentage() ?? 0;
    }

    /**
     * Process Paymob payment
     */
    private function processPaymobPayment(Cart $cart, User|Contact $contact, string $checkoutToken): JsonResponse
    {
        try {
            if (!is_numeric($cart->total)) {
                return response()->json([
                    'error' => 'Invalid cart total.',
                ], 400);
            }

            $response = Http::post(url('/api/payment/process'), [
                'amount_cents' => (int) ($cart->total * 100),
                'contact_email' => $contact->email,
                'name' => $contact->name,
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

            return response()->json([
                'error' => 'Failed to initiate payment. Invalid response.',
            ], 400);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Unexpected error during payment: ' . $e->getMessage(),
            ], 500);
        }
    }
}
