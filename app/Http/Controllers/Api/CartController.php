<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Country;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\ProductColorSize;
use App\Models\Setting;
use App\Models\ShippingType;
use App\Models\City;
use App\Models\Governorate;
use App\Services\CartServiceApi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CartController extends Controller
{
    /**
     * Get nested cart data (countries, shipping types, currency)
     */
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

    /**
     * Add item to cart
     */
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

        // Make shipping_type_id required if shipping is enabled
        if (Setting::isShippingEnabled()) {
            $rules['shipping_type_id'] = ['nullalbe', 'exists:shipping_types,id'];
        } else {
            $rules['shipping_type_id'] = ['nullable', 'exists:shipping_types,id'];
        }

        $request->validate($rules);

        $product = Product::findOrFail($request->product_id);

        // Custom validation: check if the product must be ordered in quantity >= 2
        if ($product->must_be_collection && $request->quantity < 2) {
            return response()->json([
                'message' => 'This product must be ordered in a quantity of 2 or more.'
            ], 422);
        }

        // Validate color and size
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

        // Check stock availability
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

        // Get or create cart
        $cart = app(CartServiceApi::class)->getCart();

        // Add item to cart
        app(CartServiceApi::class)->addItemToCart(
            $cart,
            $request->product_id,
            $request->quantity,
            $request->color_id,
            $request->size_id
        );

        // Update cart with location and shipping type
        $cart->update([
            'shipping_type_id' => $request->shipping_type_id ?? $cart->shipping_type_id,
            'country_id' => $request->country_id ?? $cart->country_id,
            'governorate_id' => $request->governorate_id ?? $cart->governorate_id,
            'city_id' => $request->city_id ?? $cart->city_id,
        ]);

        // Recalculate shipping cost and totals
        $this->recalculateCartTotals($cart);

        return response()->json([
            'message' => 'Product added to cart successfully.',
            'cart_id' => $cart->id,
            'cart' => $this->formatCartResponse($cart),
        ], 200);
    }

    /**
     * Update cart item quantity
     */
    public function updateQuantity(Request $request, $itemId)
    {
        $rules = [
            'quantity' => ['required', 'integer', 'min:1', 'max:10'],
            'country_id' => ['nullable', 'exists:countries,id'],
            'governorate_id' => ['nullable', 'exists:governorates,id'],
            'city_id' => ['nullable', 'exists:cities,id'],
        ];

        if (Setting::isShippingEnabled()) {
            $rules['shipping_type_id'] = ['required', 'exists:shipping_types,id'];
        } else {
            $rules['shipping_type_id'] = ['nullable', 'exists:shipping_types,id'];
        }

        $request->validate($rules);

        $item = CartItem::find($itemId);
        if (!$item) {
            return response()->json(['message' => 'Item not found.'], 404);
        }

        // Check stock availability
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

        // Update item quantity
        $item->update([
            'quantity' => $request->quantity,
            'subtotal' => $request->quantity * $item->price_per_unit,
        ]);

        // Get cart
        $cart = $item->cart;

        // Update cart with location and shipping type
        $cart->update([
            'shipping_type_id' => $request->shipping_type_id ?? $cart->shipping_type_id,
            'country_id' => $request->country_id ?? $cart->country_id,
            'governorate_id' => $request->governorate_id ?? $cart->governorate_id,
            'city_id' => $request->city_id ?? $cart->city_id,
        ]);

        // Recalculate shipping cost and totals
        $this->recalculateCartTotals($cart);

        return response()->json([
            'message' => 'Cart item updated successfully.',
            'cart' => $this->formatCartResponse($cart),
        ], 200);
    }

    /**
     * Remove item from cart
     */
    public function destroy($itemId)
    {
        $item = CartItem::find($itemId);
        if (!$item) {
            return response()->json(['message' => 'Item not found.'], 404);
        }

        $cart = $item->cart;
        $item->delete();

        // Recalculate shipping cost and totals
        $this->recalculateCartTotals($cart);

        return response()->json([
            'message' => 'Item removed from cart.',
            'cart' => $this->formatCartResponse($cart),
        ], 200);
    }

    /**
     * Recalculate cart totals (subtotal, shipping, tax, total)
     */
    private function recalculateCartTotals(Cart $cart)
    {
        $items = $cart->items()->with('product')->get()->map(function ($item) {
            // Ensure price_per_unit is correct
            $pricePerUnit = (float) ($item->product->discount_price_for_current_country ?? $item->product->price ?? 0);
            $subtotal = $pricePerUnit * $item->quantity;

            // Update item if price or subtotal is incorrect
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

        // Calculate shipping cost
        $shippingCost = $this->updateShippingCost(
            $cart->shipping_type_id,
            $items,
            $cart->city_id,
            $cart->governorate_id,
            $cart->country_id
        );

        // Calculate totals
        $subTotal = array_sum(array_column($items, 'subtotal'));
        $taxPercentage = $this->getTaxPercentage();
        $taxAmount = ($subTotal * $taxPercentage) / 100;
        $total = $subTotal + $taxAmount + $shippingCost;

        // Update cart
        $cart->update([
            'subtotal' => $subTotal,
            'shipping_cost' => $shippingCost,
            'tax_percentage' => $taxPercentage,
            'tax_amount' => $taxAmount,
            'total' => $total,
        ]);

        Log::info('Cart totals recalculated', [
            'cart_id' => $cart->id,
            'subtotal' => $subTotal,
            'shipping_cost' => $shippingCost,
            'tax_percentage' => $taxPercentage,
            'tax_amount' => $taxAmount,
            'total' => $total,
            'shipping_type_id' => $cart->shipping_type_id,
            'country_id' => $cart->country_id,
            'governorate_id' => $cart->governorate_id,
            'city_id' => $cart->city_id,
        ]);
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

        // If all products have free shipping, return 0
        if (!$hasChargeableItems) {
            Log::info('All items have free shipping', ['cart_items' => $items]);
            return 0.0;
        }

        // If shipping locations are disabled, return 0
        if (!Setting::isShippingLocationsEnabled()) {
            Log::info('Shipping locations disabled');
            return 0.0;
        }

        // Add shipping type cost if provided
        if ($shippingTypeId) {
            $shippingType = ShippingType::find($shippingTypeId);
            $totalShippingCost += $shippingType?->shipping_cost ?? 0.0;
        } else {
            Log::warning('No shipping type selected for cart', [
                'city_id' => $cityId,
                'governorate_id' => $governorateId,
                'country_id' => $countryId,
            ]);
        }

        // Get the highest shipping cost per item
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

        $finalShippingCost = $totalShippingCost + $highestShippingCost;
        Log::info('Shipping cost calculated', [
            'shipping_type_cost' => $totalShippingCost,
            'highest_product_cost' => $highestShippingCost,
            'final_shipping_cost' => $finalShippingCost,
        ]);

        return $finalShippingCost;
    }

    /**
     * Calculate shipping cost for a single product
     */
    private function calculateProductShippingCost(Product $product, $cityId, $governorateId, $countryId): float
    {
        if ($product->is_free_shipping) {
            return 0.0;
        }

        if (!Setting::isShippingLocationsEnabled()) {
            return 0.0;
        }

        if (!$cityId && !$governorateId && !$countryId) {
            return $product->cost ?? 0.0;
        }

        return $this->getProductShippingCost($product, $cityId, $governorateId, $countryId)
            ?? $this->getLocationBasedShippingCost($cityId, $governorateId, $countryId)
            ?? $product->cost
            ?? 0.0;
    }

    /**
     * Get product-specific shipping cost based on location
     */
    private function getProductShippingCost(Product $product, $cityId, $governorateId, $countryId): ?float
    {
        if (!Setting::isShippingLocationsEnabled()) {
            return 0.0;
        }

        if ($cityId) {
            $cost = $product->shippingCosts()
                ->where('city_id', $cityId)
                ->where('country_id', $countryId)
                ->value('cost');
            if ($cost !== null) return $cost;
        }

        if ($governorateId) {
            $cost = $product->shippingCosts()
                ->where('governorate_id', $governorateId)
                ->where('country_id', $countryId)
                ->whereNull('city_id')
                ->value('cost');
            if ($cost !== null) return $cost;
        }

        if ($countryId) {
            return $product->shippingCosts()
                ->where('country_id', $countryId)
                ->whereNull('city_id')
                ->whereNull('governorate_id')
                ->value('cost');
        }

        return null;
    }

    /**
     * Get location-based shipping cost
     */
    private function getLocationBasedShippingCost($cityId, $governorateId, $countryId): ?float
    {
        if (!Setting::isShippingLocationsEnabled()) {
            return 0.0;
        }

        if ($cityId) {
            $city = City::find($cityId);
            if ($city?->cost > 0) return (float) $city->cost;
        }

        if ($governorateId) {
            $governorate = Governorate::find($governorateId);
            if ($governorate?->cost > 0) return (float) $governorate->cost;

            $zone = $governorate->shippingZones()->first();
            if ($zone?->cost > 0) return (float) $zone->cost;
        }

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
     * Format cart response data
     */
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
}
