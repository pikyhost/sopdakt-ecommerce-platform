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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class CheckoutController extends Controller
{
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
                Log::warning('Inactive user attempted checkout', ['user_id' => Auth::guard('sanctum')->id()]);
                return response()->json([
                    'error' => 'Your account is not active. Please contact support.',
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
                if (Auth::guard('sanctum')->check()) {
                    $query->where('user_id', Auth::guard('sanctum')->id());
                } else {
                    $query->where('session_id', $sessionId);
                }
            })->with('items.product')->latest()->first();

            if (!$cart || $cart->items->isEmpty()) {
                Log::info('Empty cart during checkout', ['user_id' => Auth::id(), 'session_id' => $sessionId]);
                return response()->json([
                    'error' => 'Cart is empty or not found',
                ], 404);
            }

            // Save contact data
            $contact = $this->saveContact($data, $cart);

            // Calculate subtotal
            $subTotal = $cart->items->sum(fn ($item) => $item->subtotal);
            Log::info('Subtotal Calculated', ['subtotal' => $subTotal, 'cart_id' => $cart->id]);

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
                    Log::info('Coupon Applied', [
                        'coupon_id' => $coupon->id,
                        'type' => $coupon->type,
                        'value' => $coupon->value,
                        'discount' => $discount,
                        'shipping_cost' => $shippingCost,
                    ]);
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

            Log::info('Cart Updated', [
                'cart_id' => $cart->id,
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
            Log::critical('Unexpected error in checkout', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }

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

                Auth::guard('sanctum')->login($user);

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

    /**
     * Calculate shipping cost for the cart
     */
    private function updateShippingCost($shippingTypeId, array $items, $cityId, $governorateId, $countryId): float
    {
        Log::info('updateShippingCost Inputs', [
            'shipping_type_id' => $shippingTypeId,
            'city_id' => $cityId,
            'governorate_id' => $governorateId,
            'country_id' => $countryId,
            'items' => $items,
        ]);

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

        Log::info('Has Chargeable Items', ['has_chargeable_items' => $hasChargeableItems]);

        // If all products have free shipping, set cost to 0
        if (!$hasChargeableItems) {
            Log::info('Returning 0 due to no chargeable items');
            return 0.0;
        }

        // If shipping locations are disabled, force cost to 0
        if (!Setting::isShippingLocationsEnabled()) {
            Log::info('Returning 0 due to disabled shipping locations');
            return 0.0;
        }

        // Add shipping type cost (only if there are chargeable items and shipping is enabled)
        if ($shippingTypeId && Setting::isShippingEnabled()) {
            $shippingType = ShippingType::find($shippingTypeId);
            $totalShippingCost += $shippingType?->shipping_cost ?? 0.0;
            Log::info('Shipping Type Cost', ['shipping_type_id' => $shippingTypeId, 'cost' => $shippingType?->shipping_cost ?? 0]);
        }

        // Get the highest shipping cost per item instead of summing them
        $highestShippingCost = 0.0;
        if (!empty($items) && ($cityId || $governorateId || $countryId)) {
            foreach ($items as $item) {
                $product = Product::find($item['product_id'] ?? null);
                if ($product && !$product->is_free_shipping) {
                    $cost = $this->calculateProductShippingCost($product, $cityId, $governorateId, $countryId);
                    Log::info('Product Shipping Cost', ['product_id' => $item['product_id'], 'cost' => $cost]);
                    if ($cost > $highestShippingCost) {
                        $highestShippingCost = $cost;
                    }
                }
            }
        }

        $finalCost = $totalShippingCost + $highestShippingCost;
        Log::info('Final Shipping Cost', ['total' => $finalCost]);

        return $finalCost;
    }

    /**
     * Calculate shipping cost for a single product
     */
    private function calculateProductShippingCost(Product $product, $cityId, $governorateId, $countryId): float
    {
        Log::info('calculateProductShippingCost Inputs', [
            'product_id' => $product->id,
            'city_id' => $cityId,
            'governorate_id' => $governorateId,
            'country_id' => $countryId,
        ]);

        // Check if product has free shipping
        if ($product->is_free_shipping) {
            Log::info('Returning 0 due to product free shipping', ['product_id' => $product->id]);
            return 0.0;
        }

        // If shipping locations are disabled, force cost to 0
        if (!Setting::isShippingLocationsEnabled()) {
            Log::info('Returning 0 due to disabled shipping locations', ['product_id' => $product->id]);
            return 0.0;
        }

        // If no location is provided, fall back to product cost
        if (!$cityId && !$governorateId && !$countryId) {
            $cost = $product->cost ?? 0.0;
            Log::info('Returning product cost due to no location', ['product_id' => $product->id, 'cost' => $cost]);
            return $cost;
        }

        // Get product-specific shipping cost
        $cost = $this->getProductShippingCost($product, $cityId, $governorateId, $countryId);

        if ($cost !== null) {
            Log::info('Product Shipping Cost Found', ['product_id' => $product->id, 'cost' => $cost]);
            return $cost;
        }

        // Fall back to location-based cost or product cost
        $cost = $this->getLocationBasedShippingCost($cityId, $governorateId, $countryId) ?? $product->cost ?? 0.0;

        Log::info('Final Product Shipping Cost', [
            'product_id' => $product->id,
            'cost' => $cost,
            'city_id' => $cityId,
            'governorate_id' => $governorateId,
            'country_id' => $countryId,
        ]);

        return $cost;
    }

    private function getProductShippingCost(Product $product, $cityId, $governorateId, $countryId): ?float
    {
        if (!Setting::isShippingLocationsEnabled()) {
            Log::info('Returning 0 due to disabled shipping locations in getProductShippingCost', ['product_id' => $product->id]);
            return 0.0;
        }

        $shippingCosts = $product->shippingCosts()->get();

        Log::info('All Shipping Costs Retrieved', [
            'product_id' => $product->id,
            'shipping_costs' => $shippingCosts->map(function ($cost) {
                return [
                    'id' => $cost->id,
                    'priority' => $cost->priority,
                    'cost' => $cost->cost,
                    'city_id' => $cost->city_id,
                    'governorate_id' => $cost->governorate_id,
                    'country_id' => $cost->country_id,
                    'shipping_zone_id' => $cost->shipping_zone_id,
                    'country_group_id' => $cost->country_group_id,
                ];
            })->toArray()
        ]);

        if ($shippingCosts->isEmpty()) {
            Log::info('No Shipping Costs Found for Product', ['product_id' => $product->id]);
            return null;
        }

        // Priority 1: Check City
        foreach ($shippingCosts as $shippingCost) {
            if ($shippingCost->priority === 'City' && $cityId && $shippingCost->city_id == $cityId) {
                Log::info('City Shipping Cost Selected', ['product_id' => $product->id, 'cost' => $shippingCost->cost]);
                return (float) $shippingCost->cost;
            }
        }

        // Priority 2: Check Governorate
        foreach ($shippingCosts as $shippingCost) {
            if ($shippingCost->priority === 'Governorate' && $governorateId && $shippingCost->governorate_id == $governorateId) {
                Log::info('Governorate Shipping Cost Selected', ['product_id' => $product->id, 'cost' => $shippingCost->cost]);
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
                        Log::info('Governorate Shipping Cost Preferred Over Zone', ['product_id' => $product->id, 'cost' => $governorateCost->cost]);
                        return (float) $governorateCost->cost;
                    }
                    Log::info('Shipping Zone Shipping Cost Selected', ['product_id' => $product->id, 'cost' => $shippingCost->cost]);
                    return (float) $shippingCost->cost;
                }
            }
        }

        // Priority 4: Check Country
        foreach ($shippingCosts as $shippingCost) {
            if ($shippingCost->priority === 'Country' && $countryId && $shippingCost->country_id == $countryId) {
                Log::info('Country Shipping Cost Selected', ['product_id' => $product->id, 'cost' => $shippingCost->cost]);
                return (float) $shippingCost->cost;
            }
        }

        // Priority 5: Check Country Group
        foreach ($shippingCosts as $shippingCost) {
            if ($shippingCost->priority === 'Country Group' && $countryId && $shippingCost->country_group_id) {
                $country = Country::find($countryId);
                if ($country && $country->countryGroups()->where('id', $shippingCost->country_group_id)->exists()) {
                    Log::info('Country Group Shipping Cost Selected', ['product_id' => $product->id, 'cost' => $shippingCost->cost]);
                    return (float) $shippingCost->cost;
                }
            }
        }

        Log::info('No Matching Shipping Cost', ['product_id' => $product->id]);
        return null;
    }

    /**
     * Get location-based shipping cost
     */
    private function getLocationBasedShippingCost($cityId, $governorateId, $countryId): ?float
    {
        Log::info('getLocationBasedShippingCost Inputs', [
            'city_id' => $cityId,
            'governorate_id' => $governorateId,
            'country_id' => $countryId,
        ]);

        // If shipping locations are disabled, return 0
        if (!Setting::isShippingLocationsEnabled()) {
            Log::info('Returning 0 due to disabled shipping locations in getLocationBasedShippingCost');
            return 0.0;
        }

        // Check city cost
        if ($cityId) {
            $city = City::find($cityId);
            Log::info('City Cost', ['city_id' => $cityId, 'cost' => $city?->cost ?? 0]);
            if ($city?->cost > 0) return (float) $city->cost;
        }

        // Check governorate or shipping zone cost
        if ($governorateId) {
            $governorate = Governorate::find($governorateId);
            Log::info('Governorate Cost', ['governorate_id' => $governorateId, 'cost' => $governorate?->cost ?? 0]);
            if ($governorate?->cost > 0) return (float) $governorate->cost;

            $zone = $governorate->shippingZones()->first();
            Log::info('Shipping Zone Cost', ['governorate_id' => $governorateId, 'zone_cost' => $zone?->cost ?? 0]);
            if ($zone?->cost > 0) return (float) $zone->cost;
        }

        // Check country cost
        if ($countryId) {
            $country = Country::find($countryId);
            Log::info('Country Cost', ['country_id' => $countryId, 'cost' => $country?->cost ?? 0]);
            if ($country?->cost > 0) return (float) $country->cost;
        }

        Log::info('No Location-Based Cost Found');
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
                'status' => OrderStatus::Shipping,
                'notes' => $data['notes'] ?? null,
                'checkout_token' => $checkoutToken,
                'tracking_number' => null,
            ];

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
            Log::error('Order creation failed', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'error' => 'We encountered an issue: ' . $e->getMessage(),
            ], 500);
        }
    }
}
