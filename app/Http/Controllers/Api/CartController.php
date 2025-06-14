<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CartItemResource;
use App\Http\Resources\CartResource;
use App\Http\Resources\ComplementaryProductResource;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Country;
use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\ProductColorSize;
use App\Models\Setting;
use App\Models\ShippingType;
use App\Models\City;
use App\Models\Governorate;
use App\Services\CartServiceApi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'coupon_code' => ['nullable', 'string', 'exists:coupons,code'],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $cart = app(CartServiceApi::class)->getCart();
        Log::info('Cart Retrieved', ['cart_id' => $cart?->id, 'cart_data' => $cart?->toArray()]);

        if (!$cart) {
            return response()->json(['message' => __('Cart is empty')]);
        }

        $cartItems = $cart->items()->with('product')->get();

        if ($cartItems->isEmpty()) {
            return response()->json(['message' => __('Cart is empty')]);
        }

        // Recalculate shipping cost and totals
        $this->recalculateCartTotals($cart);

        $productIds = $cartItems->pluck('product.id')->filter()->unique();

        // Fetch complementary products
        $complementaryProductIds = Product::whereIn('id', $productIds)
            ->with('complementaryProducts:id')
            ->get()
            ->pluck('complementaryProducts.*.id')
            ->flatten()
            ->unique();

        $complementaryProducts = Product::whereIn('id', $complementaryProductIds)
            ->whereNotIn('id', $productIds)
            ->inRandomOrder()
            ->limit(6)
            ->get();

        $locale = app()->getLocale();

        return response()->json([
            'cart' => new CartResource($cart),
            'cartItems' => CartItemResource::collection($cartItems),
            'totals' => [
                'subtotal' => $cart->subtotal,
                'shipping_cost' => $cart->shipping_cost,
                'tax_amount' => $cart->tax_amount,
                'total' => $cart->total,
            ],
            'complementary_products' => ComplementaryProductResource::collection($complementaryProducts),
        ]);
    }

    public function applyCoupon(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            // Validate request
            $validator = Validator::make($request->all(), [
                'coupon_code' => ['required', 'string', 'exists:coupons,code'],
            ]);

            if ($validator->fails()) {
                DB::rollBack();
                return response()->json([
                    'error' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $cart = $this->getOrCreateCart();
            $cartItems = $this->loadCartItems($cart);

            // Validate cart is not empty
            if ($cartItems->isEmpty()) {
                DB::rollBack();
                return response()->json([
                    'error' => 'Your cart is empty. Please add products before applying a coupon.',
                    'cart_url' => route('cart.index'),
                ], 422);
            }

            // Validate coupon
            $coupon = Coupon::where('code', $request->coupon_code)
                ->where('is_active', true)
                ->where(function ($query) {
                    $query->whereNull('expires_at')
                        ->orWhere('expires_at', '>', now());
                })
                ->first();

            if (!$coupon) {
                DB::rollBack();
                return response()->json([
                    'error' => 'Invalid or expired coupon code.',
                ], 422);
            }

            // Calculate subtotal
            $subTotal = $cartItems->sum(function ($item) {
                return $item->quantity * $item->product->price;
            });

            // Check minimum order amount
            if ($coupon->min_order_amount && $subTotal < $coupon->min_order_amount) {
                DB::rollBack();
                return response()->json([
                    'error' => 'Minimum order amount for this coupon is $' . number_format($coupon->min_order_amount, 2),
                ], 422);
            }

            // Check global usage limit
            $totalUsages = CouponUsage::where('coupon_id', $coupon->id)->count();
            if ($coupon->usage_limit !== null && $totalUsages >= $coupon->usage_limit) {
                DB::rollBack();
                return response()->json([
                    'error' => 'This coupon has reached its total usage limit.',
                ], 422);
            }

            // Check per-user or per-session usage limit
            $userId = Auth::guard('sanctum')->id();
            $sessionId = session()->getId();
            $userUsageQuery = CouponUsage::where('coupon_id', $coupon->id)->whereNotNull('order_id');
            if ($userId) {
                $userUsageQuery->where('user_id', $userId);
            } else {
                $userUsageQuery->where('session_id', $sessionId);
            }

            $userUsages = $userUsageQuery->count();
            if ($coupon->usage_limit_per_user !== null && $userUsages >= $coupon->usage_limit_per_user) {
                DB::rollBack();
                return response()->json([
                    'error' => 'You have already used this coupon the maximum number of times.',
                ], 422);
            }

            // Calculate discount
            $discount = 0;
            $taxPercentage = Setting::getTaxPercentage();
            $taxAmount = $subTotal * ($taxPercentage / 100);
            $shippingCost = $this->calculateShippingCost($cart, $cartItems);

            if ($coupon->type === 'discount_percentage') {
                $discount = ($subTotal * $coupon->value) / 100;
            } elseif ($coupon->type === 'discount_amount') {
                $discount = $coupon->value;
            } elseif ($coupon->type === 'free_shipping') {
                $shippingCost = 0;
            }

            // Calculate total
            $total = max(0, $subTotal - $discount + $shippingCost + $taxAmount);

            // Store coupon intent in session
            session()->put('applied_coupon', [
                'code' => $coupon->code,
                'type' => $coupon->type,
                'value' => $coupon->value,
                'discount' => $discount,
            ]);

            // Update cart with new totals
            $cart->update([
                'subtotal' => $subTotal,
                'total' => $total,
                'tax_percentage' => $taxPercentage,
                'tax_amount' => $taxAmount,
                'shipping_cost' => $shippingCost,
            ]);

            // Record coupon usage if authenticated
            if (Auth::guard('sanctum')->check()) {
                $coupon->usages()->create([
                    'user_id' => $userId,
                    'session_id' => $sessionId,
                    'order_id' => null,
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Coupon applied successfully',
                'cart' => new CartResource($cart),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Apply coupon error: ' . $e->getMessage());
            return response()->json([
                'error' => 'An error occurred while applying the coupon.',
            ], 500);
        }
    }

    public function getNestedCartData(Request $request)
    {
        $locale = app()->getLocale();

        $countries = Country::with(['governorates.cities'])->get()->map(function ($country) use ($locale) {
            return [
                'id' => $country->id,
                'name' => $country->getTranslation('name', $locale),
                'governorates' => $country->governorates->map(function ($gov) use ($locale) {
                    return [
                        'id' => $gov->id,
                        'name' => $gov->getTranslation('name', $locale),
                        'cities' => $gov->cities->map(function ($city) use ($locale) {
                            return [
                                'id' => $city->id,
                                'name' => $city->getTranslation('name', $locale),
                            ];
                        }),
                    ];
                }),
            ];
        });

        $shippingTypes = Setting::isShippingEnabled()
            ? ShippingType::where('status', true)->get()->map(function ($type) use ($locale) {
                return [
                    'id' => $type->id,
                    'name' => $type->getTranslation('name', $locale),
                ];
            })
            : [];

        return response()->json([
            'countries' => $countries,
            'shipping_types' => $shippingTypes,
            'payment_method' => PaymentMethod::find(2),
            'currency' => Setting::getCurrency(),
        ]);
    }

    public function store(Request $request) 
    {
        $rules = [
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1', 'max:10'],
            'color_id' => ['nullable', 'exists:colors,id'],
            'size_id' => ['nullable', 'exists:sizes,id'],
            'country_id' => ['nullable', 'exists:countries,id'],
            'governorate_id' => ['nullable', 'exists:governorates,id'],
            'city_id' => ['nullable', 'exists:cities,id'],
        ];

        if (Setting::isShippingEnabled()) {
            $rules['shipping_type_id'] = ['nullable', 'exists:shipping_types,id'];
        } else {
            $rules['shipping_type_id'] = ['nullable', 'exists:shipping_types,id'];
        }

        $request->validate($rules);

        $product = Product::findOrFail($request->product_id);

        if ($product->must_be_collection && $request->quantity < 2) {
            return response()->json([
                'message' => 'This product must be ordered in a quantity of 2 or more.'
            ], 422);
        }
        $hasColors = $product->productColors()->exists();
        if ($hasColors && !$request->color_id) {
            return response()->json(['message' => 'Please select a color.'], 422);
        }

        $hasSizes = false;
        if ($request->color_id) {
            $color = $product->productColors()->where('color_id', $request->color_id)->first();
            $hasSizes = $color && $color->sizes()->exists();

            if ($hasSizes && !$request->size_id) {
                return response()->json(['message' => 'Please select a size.'], 422);
            }
        }
        $availableStock = $product->quantity;

        if ($request->color_id && $request->size_id) {
            $variant = ProductColorSize::whereHas('productColor', function ($query) use ($request) {
                $query->where('product_id', $request->product_id)
                    ->where('color_id', $request->color_id);
            })->where('size_id', $request->size_id)->first();

            if (!$variant) {
                return response()->json(['message' => 'Selected variant not available.'], 422);
            }

            $availableStock = $variant->quantity;
        }
        if ($availableStock <= 0) {
            return response()->json(['message' => 'This product is out of stock!'], 422);
        }

        if ($request->quantity > $availableStock) {
            return response()->json(['message' => 'Requested quantity exceeds stock!'], 422);
        }

        $cart = app(CartServiceApi::class)->getCart();

        $maxCartQuantity = Setting::first()->max_cart_quantity;
        $totalCartQuantity = $cart->items->sum('quantity');

        if (($totalCartQuantity + $request->quantity) > $maxCartQuantity) {
            return response()->json([
                'message' => 'Max quantity available to add to cart is ' . $maxCartQuantity
            ], 422);
        }

        app(CartServiceApi::class)->addItemToCart(
            $cart,
            $request->product_id,
            $request->quantity,
            $request->color_id,
            $request->size_id
        );

        $cart->update([
            'shipping_type_id' => $request->shipping_type_id ?? null,
            'country_id' => $request->country_id ?? $cart->country_id,
            'governorate_id' => $request->governorate_id ?? $cart->governorate_id,
            'city_id' => $request->city_id ?? $cart->city_id,
        ]);

        $this->recalculateCartTotals($cart);

        return response()->json([
            'message' => 'Product added to cart successfully.',
            'cart_id' => $cart->id,
            'cart' => $this->formatCartResponse($cart),
        ], 200);
    }

    public function updateQuantity(Request $request, $itemId)
    {
        $rules = [
            'quantity' => ['required', 'integer', 'min:1', 'max:10'],
            'country_id' => ['nullable', 'exists:countries,id'],
            'governorate_id' => ['nullable', 'exists:governorates,id'],
            'city_id' => ['nullable', 'exists:cities,id'],
        ];

        if (Setting::isShippingEnabled()) {
            $rules['shipping_type_id'] = ['nullable', 'exists:shipping_types,id'];
        } else {
            $rules['shipping_type_id'] = ['nullable', 'exists:shipping_types,id'];
        }

        $request->validate($rules);

        $item = CartItem::find($itemId);
        if (!$item) {
            return response()->json(['message' => 'Item not found.'], 404);
        }

        $availableStock = $item->product->quantity;

        if ($item->color_id && $item->size_id) {
            $variant = ProductColorSize::whereHas('productColor', function ($query) use ($item) {
                $query->where('product_id', $item->product_id)
                    ->where('color_id', $item->color_id);
            })->where('size_id', $item->size_id)->first();

            $availableStock = $variant->quantity ?? $availableStock;
        }

        if ($request->quantity > $availableStock) {
            return response()->json(['message' => 'Requested quantity exceeds stock!'], 422);
        }

        $item->update([
            'quantity' => $request->quantity,
            'subtotal' => $request->quantity * $item->price_per_unit,
        ]);

        $cart = $item->cart;
        Log::info('Update Quantity Request', ['item_id' => $itemId, 'request_data' => $request->all(), 'cart_id' => $cart->id]);

        $cart->update([
            'shipping_type_id' => $request->shipping_type_id ?? null,
            'country_id' => $request->country_id ?? $cart->country_id,
            'governorate_id' => $request->governorate_id ?? $cart->governorate_id,
            'city_id' => $request->city_id ?? $cart->city_id,
        ]);

        $this->recalculateCartTotals($cart);

        return response()->json([
            'message' => 'Cart item updated successfully.',
            'cart' => $this->formatCartResponse($cart),
        ], 200);
    }

    public function destroy($itemId)
    {
        $item = CartItem::find($itemId);
        if (!$item) {
            return response()->json(['message' => 'Item not found.'], 404);
        }

        $cart = $item->cart;
        $item->delete();

        $this->recalculateCartTotals($cart);

        return response()->json([
            'message' => 'Item removed from cart.',
            'cart' => $this->formatCartResponse($cart),
        ], 200);
    }

    private function recalculateCartTotals(Cart $cart)
    {
        $items = $cart->items()->with('product')->get()->map(function ($item) {
            $pricePerUnit = (float) ($item->product->discount_price_for_current_country ?? $item->product->price ?? 0);
            $subtotal = $pricePerUnit * $item->quantity;

            if ($item->price_per_unit != $pricePerUnit || $item->subtotal != $subtotal) {
                $item->update([
                    'price_per_unit' => $pricePerUnit,
                    'subtotal' => $subtotal,
                ]);
            }

            return [
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'subtotal' => $subtotal,
            ];
        })->toArray();

        $subTotal = array_sum(array_column($items, 'subtotal'));

        $shippingCost = $this->updateShippingCost(
            $cart->shipping_type_id,
            $items,
            $cart->city_id,
            $cart->governorate_id,
            $cart->country_id
        );

        $taxPercentage = $this->getTaxPercentage();
        $taxAmount = ($subTotal * $taxPercentage) / 100;

        $total = $subTotal + $taxAmount + $shippingCost;

        $cart->update([
            'subtotal' => $subTotal,
            'shipping_cost' => $shippingCost,
            'tax_percentage' => $taxPercentage,
            'tax_amount' => $taxAmount,
            'total' => $total,
        ]);
    }

    private function updateShippingCost($shippingTypeId, array $items, $cityId, $governorateId, $countryId): float
    {
        Log::info('updateShippingCost Inputs', [
            'shipping_type_id' => $shippingTypeId,
            'city_id' => $cityId,
            'governorate_id' => $governorateId,
            'country_id' => $countryId,
            'items' => $items
        ]);

        $totalShippingCost = 0.0;
        $hasChargeableItems = false;

        foreach ($items as $item) {
            $product = Product::find($item['product_id'] ?? null);
            if ($product && !$product->is_free_shipping) {
                $hasChargeableItems = true;
                break;
            }
        }

        Log::info('Has Chargeable Items', ['has_chargeable_items' => $hasChargeableItems]);

        if (!$hasChargeableItems) {
            Log::info('Returning 0 due to no chargeable items');
            return 0.0;
        }

        if (!Setting::isShippingLocationsEnabled()) {
            Log::info('Returning 0 due to disabled shipping locations');
            return 0.0;
        }

        if ($shippingTypeId && Setting::isShippingEnabled()) {
            $shippingType = ShippingType::find($shippingTypeId);
            $totalShippingCost += $shippingType?->shipping_cost ?? 0.0;
            Log::info('Shipping Type Cost', ['shipping_type_id' => $shippingTypeId, 'cost' => $shippingType?->shipping_cost ?? 0]);
        }

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

    private function calculateProductShippingCost(Product $product, $cityId, $governorateId, $countryId): float
    {
        if ($product->is_free_shipping) {
            Log::info('Returning 0 due to free shipping', ['product_id' => $product->id]);
            return 0.0;
        }

        if (!Setting::isShippingLocationsEnabled()) {
            Log::info('Returning 0 due to disabled shipping locations', ['product_id' => $product->id]);
            return 0.0;
        }

        if (!$cityId && !$governorateId && !$countryId) {
            $cost = $product->cost ?? 0.0;
            Log::info('Returning product cost due to no location', ['product_id' => $product->id, 'cost' => $cost]);
            return $cost;
        }

        $cost = $this->getProductShippingCost($product, $cityId, $governorateId, $countryId);

        if ($cost !== null) {
            Log::info('Product Shipping Cost Found', ['product_id' => $product->id, 'cost' => $cost]);
            return $cost;
        }

        $cost = $this->getLocationBasedShippingCost($cityId, $governorateId, $countryId)
            ?? $product->cost
            ?? 0.0;

        Log::info('Final Product Shipping Cost', [
            'product_id' => $product->id,
            'cost' => $cost,
            'city_id' => $cityId,
            'governorate_id' => $governorateId,
            'country_id' => $countryId
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

    private function getLocationBasedShippingCost($cityId, $governorateId, $countryId): ?float
    {
        if (!Setting::isShippingLocationsEnabled()) {
            Log::info('Returning 0 due to disabled shipping locations in getLocationBasedShippingCost');
            return 0.0;
        }

        if ($cityId) {
            $city = City::find($cityId);
            Log::info('City Cost', ['city_id' => $cityId, 'cost' => $city?->cost ?? 0]);
            if ($city?->cost > 0) return (float) $city->cost;
        }

        if ($governorateId) {
            $governorate = Governorate::find($governorateId);
            Log::info('Governorate Cost', ['governorate_id' => $governorateId, 'cost' => $governorate?->cost ?? 0]);
            if ($governorate?->cost > 0) return (float) $governorate->cost;

            $zone = $governorate->shippingZones()->first();
            Log::info('Shipping Zone Cost', ['governorate_id' => $governorateId, 'zone_cost' => $zone?->cost ?? 0]);
            if ($zone?->cost > 0) return (float) $zone->cost;
        }

        if ($countryId) {
            $country = Country::find($countryId);
            Log::info('Country Cost', ['country_id' => $countryId, 'cost' => $country?->cost ?? 0]);
            if ($country?->cost > 0) return (float) $country->cost;
        }

        Log::info('No Location-Based Cost Found');
        return null;
    }

    private static function getTaxPercentage(): float
    {
        return Setting::getTaxPercentage() ?? 0;
    }

    private function formatCartResponse(Cart $cart): array
    {
        $locale = app()->getLocale();
        $items = $cart->items()->with('product')->get()->map(function ($item) use ($locale) {
            return [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'price_per_unit' => $item->price_per_unit,
                'subtotal' => $item->subtotal,
                'currency' => Setting::getCurrency(),
                'product' => $item->product ? [
                    'id' => $item->product->id,
                    'name' => $item->product->getTranslation('name', $locale),
                    'price' => number_format($item->price_per_unit, 2) . ' ' . (Setting::getCurrency()['code'] ?? 'EGP'),
                ] : null,
            ];
        })->toArray();

        return [
            'id' => $cart->id,
            'items' => $items,
            'subtotal' => $cart->subtotal,
            'shipping_cost' => $cart->shipping_cost,
            'tax_percentage' => $cart->tax_percentage,
            'tax_amount' => $cart->tax_amount,
            'total' => $cart->total,
            'currency' => Setting::getCurrency(),
            'shipping_type_id' => $cart->shipping_type_id,
            'country_id' => $cart->country_id,
            'governorate_id' => $cart->governorate_id,
            'city_id' => $cart->city_id,
        ];
    }

    private function getOrCreateCart()
    {
        $cart = app(CartServiceApi::class)->getCart();
        Log::info('Cart Retrieved in getOrCreateCart', ['cart_id' => $cart->id, 'cart_data' => $cart->toArray()]);
        return $cart;
    }

    private function loadCartItems(Cart $cart)
    {
        return $cart->items()->with('product')->get();
    }

    private function calculateShippingCost(Cart $cart, $cartItems)
    {
        $items = $cartItems->map(function ($item) {
            return [
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'subtotal' => $item->subtotal,
            ];
        })->toArray();

        return $this->updateShippingCost(
            $cart->shipping_type_id,
            $items,
            $cart->city_id,
            $cart->governorate_id,
            $cart->country_id
        );
    }
}
