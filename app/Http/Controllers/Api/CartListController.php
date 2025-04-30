<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CartItemResource;
use App\Http\Resources\CartResource;
use App\Http\Resources\ComplementaryProductResource;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Country;
use App\Models\Governorate;
use App\Models\City;
use App\Models\Product;
use App\Models\Setting;
use App\Models\ShippingType;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

/**
 * @group Cart Management
 *
 * APIs for managing the shopping cart, including viewing cart contents, updating shipping details, modifying item quantities, removing items, and proceeding to checkout.
 */
class CartListController extends Controller
{
    /**
     * Get the user's cart
     *
     * Retrieves the current user's cart or a session-based cart if the user is not authenticated. Includes cart items, totals, shipping options, and complementary products.
     *
     * @authenticated
     * @response 200 {
     *   "cart": {
     *     "id": 1,
     *     "user_id": 1,
     *     "session_id": null,
     *     "subtotal": 99.99,
     *     "total": 109.99,
     *     "tax_percentage": 5,
     *     "tax_amount": 5.00,
     *     "shipping_cost": 5.00,
     *     "country_id": 1,
     *     "governorate_id": 1,
     *     "city_id": 1,
     *     "shipping_type_id": 1
     *   },
     *   "cartItems": [
     *     {
     *       "id": 1,
     *       "cart_id": 1,
     *       "product_id": 1,
     *       "quantity": 2,
     *       "subtotal": 49.98,
     *       "product": {
     *         "id": 1,
     *         "name": "Sample Product",
     *         "slug": "sample-product",
     *         "discount_price_for_current_country": "24.99 USD"
     *       }
     *     }
     *   ],
     *   "totals": {
     *     "subtotal": 99.99,
     *     "shipping_cost": 5.00,
     *     "tax": 5.00,
     *     "total": 109.99,
     *     "currency": "USD"
     *   },
     *   "countries": [
     *     {"id": 1, "name": "USA", "cost": 5.00},
     *     {"id": 2, "name": "Canada", "cost": 7.00}
     *   ],
     *   "governorates": [
     *     {"id": 1, "name": "California", "country_id": 1},
     *     {"id": 2, "name": "Ontario", "country_id": 2}
     *   ],
     *   "cities": [
     *     {"id": 1, "name": "Los Angeles", "governorate_id": 1},
     *     {"id": 2, "name": "Toronto", "governorate_id": 2}
     *   ],
     *   "shipping_types": [
     *     {"id": 1, "name": "Standard Shipping", "cost": 5.00, "status": true},
     *     {"id": 2, "name": "Express Shipping", "cost": 10.00, "status": true}
     *   ],
     *   "complementary_products": [
     *     {
     *       "id": 2,
     *       "name": "Complementary Product",
     *       "slug": "complementary-product",
     *       "discount_price_for_current_country": "19.99 USD"
     *     }
     *   ]
     * }
     * @response 401 {
     *   "message": "Unauthenticated."
     * }
     */
    public function index(Request $request): JsonResponse
    {
        $cart = $this->getOrCreateCart();
        $cartItems = $this->loadCartItems($cart);

        if ($cartItems->isEmpty()) {
            return response()->json([
                'message' => __('Cart is empty'),
            ]);
        }

        $totals = $this->calculateTotals($cart, $cartItems);
        $productIds = collect($cartItems)->pluck('product.id')->filter()->unique();

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
        $nameField = 'name_' . $locale;

        return response()->json([
            'cart' => new CartResource($cart),
            'cartItems' => CartItemResource::collection($cartItems),
            'totals' => $totals,
            'countries' => Country::select(['id', "$nameField as name"])->get(),
            'governorates' => $cart->country_id
                ? Governorate::where('country_id', $cart->country_id)->select(['id', "$nameField as name"])->get()
                : [],
            'cities' => $cart->governorate_id
                ? City::where('governorate_id', $cart->governorate_id)->select(['id', "$nameField as name"])->get()
                : [],
            'shipping_types' => Setting::isShippingEnabled()
                ? ShippingType::where('status', true)->select(['id', "$nameField as name"])->get()
                : [],
            'complementary_products' => ComplementaryProductResource::collection($complementaryProducts),
        ]);
    }


    /**
     * Update cart shipping and location details
     *
     * Updates the cart's shipping and location details (country, governorate, city, and shipping type). Returns updated cart details and dependent location data.
     *
     * @authenticated
     * @bodyParam country_id integer nullable The ID of the country. Example: 1
     * @bodyParam governorate_id integer nullable The ID of the governorate. Example: 1
     * @bodyParam city_id integer nullable The ID of the city. Example: 1
     * @bodyParam shipping_type_id integer nullable The ID of the shipping type. Example: 1
     * @response 200 {
     *   "cart": {
     *     "id": 1,
     *     "user_id": 1,
     *     "session_id": null,
     *     "subtotal": 99.99,
     *     "total": 109.99,
     *     "tax_percentage": 5,
     *     "tax_amount": 5.00,
     *     "shipping_cost": 5.00,
     *     "country_id": 1,
     *     "governorate_id": 1,
     *     "city_id": 1,
     *     "shipping_type_id": 1
     *   },
     *   "cartItems": [
     *     {
     *       "id": 1,
     *       "cart_id": 1,
     *       "product_id": 1,
     *       "quantity": 2,
     *       "subtotal": 49.98,
     *       "product": {
     *         "id": 1,
     *         "name": "Sample Product",
     *         "slug": "sample-product",
     *         "discount_price_for_current_country": "24.99 USD"
     *       }
     *     }
     *   ],
     *   "totals": {
     *     "subtotal": 99.99,
     *     "shipping_cost": 5.00,
     *     "tax": 5.00,
     *     "total": 109.99,
     *     "currency": "USD"
     *   },
     *   "governorates": [
     *     {"id": 1, "name": "California", "country_id": 1}
     *   ],
     *   "cities": [
     *     {"id": 1, "name": "Los Angeles", "governorate_id": 1}
     *   ]
     * }
     * @response 422 {
     *   "errors": {
     *     "country_id": ["The selected country id is invalid."]
     *   }
     * }
     * @response 401 {
     *   "message": "Unauthenticated."
     * }
     */
    public function updateShipping(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'country_id' => 'nullable|exists:countries,id',
            'governorate_id' => 'nullable|exists:governorates,id',
            'city_id' => 'nullable|exists:cities,id',
            'shipping_type_id' => 'nullable|exists:shipping_types,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $cart = $this->getOrCreateCart();
        $cart->update([
            'country_id' => Setting::isShippingLocationsEnabled() ? $request->country_id : null,
            'governorate_id' => Setting::isShippingLocationsEnabled() ? $request->governorate_id : null,
            'city_id' => Setting::isShippingLocationsEnabled() ? $request->city_id : null,
            'shipping_type_id' => $request->shipping_type_id,
        ]);

        $cartItems = $this->loadCartItems($cart);
        $totals = $this->calculateTotals($cart, $cartItems);

        return response()->json([
            'cart' => new CartResource($cart),
            'cartItems' => CartItemResource::collection($cartItems),
            'totals' => $totals,
            'governorates' => $request->country_id ? Governorate::where('country_id', $request->country_id)->get() : [],
            'cities' => $request->governorate_id ? City::where('governorate_id', $request->governorate_id)->get() : [],
        ]);
    }

    /**
     * Update cart item quantity
     *
     * Increases or decreases the quantity of a specific cart item. If the quantity reaches 0, the item is removed.
     *
     * @authenticated
     * @urlParam cartItemId integer required The ID of the cart item. Example: 1
     * @bodyParam action string required Must be "increase" or "decrease". Example: increase
     * @response 200 {
     *   "cart": {
     *     "id": 1,
     *     "user_id": 1,
     *     "session_id": null,
     *     "subtotal": 74.97,
     *     "total": 82.47,
     *     "tax_percentage": 5,
     *     "tax_amount": 3.75,
     *     "shipping_cost": 5.00
     *   },
     *   "cartItems": [
     *     {
     *       "id": 1,
     *       "cart_id": 1,
     *       "product_id": 1,
     *       "quantity": 3,
     *       "subtotal": 74.97,
     *       "product": {
     *         "id": 1,
     *         "name": "Sample Product",
     *         "slug": "sample-product",
     *         "discount_price_for_current_country": "24.99 USD"
     *       }
     *     }
     *   ],
     *   "totals": {
     *     "subtotal": 74.97,
     *     "shipping_cost": 5.00,
     *     "tax": 3.75,
     *     "total": 82.47,
     *     "currency": "USD"
     *   }
     * }
     * @response 404 {
     *   "error": "Cart item not found"
     * }
     * @response 422 {
     *   "errors": {
     *     "action": ["The action must be increase or decrease."]
     *   }
     * }
     * @response 401 {
     *   "message": "Unauthenticated."
     * }
     */
    public function updateQuantity(Request $request, $cartItemId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'action' => 'required|in:increase,decrease',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $cartItem = CartItem::find($cartItemId);
        if (!$cartItem) {
            return response()->json(['error' => 'Cart item not found'], 404);
        }

        if ($request->action === 'increase') {
            $cartItem->increment('quantity');
        } elseif ($request->action === 'decrease' && $cartItem->quantity > 1) {
            $cartItem->decrement('quantity');
        } else {
            $cartItem->delete();
        }

        $priceString = $cartItem->product ? $cartItem->product->discount_price_for_current_country : '0 USD';
        $price = $this->extractPrice($priceString);
        $subtotal = $price * $cartItem->quantity;
        $cartItem->update(['subtotal' => $subtotal]);

        $cart = $this->getOrCreateCart();
        $cartItems = $this->loadCartItems($cart);
        $totals = $this->calculateTotals($cart, $cartItems);

        return response()->json([
            'cart' => new CartResource($cart),
            'cartItems' => CartItemResource::collection($cartItems),
            'totals' => $totals,
        ]);
    }

    /**
     * Remove a cart item
     *
     * Removes a specific cart item from the cart. If the item is part of a bundle, the entire bundle is removed.
     *
     * @authenticated
     * @urlParam cartItemId integer required The ID of the cart item to remove. Example: 1
     * @response 200 {
     *   "cart": {
     *     "id": 1,
     *     "user_id": 1,
     *     "session_id": null,
     *     "subtotal": 0.00,
     *     "total": 0.00,
     *     "tax_percentage": 5,
     *     "tax_amount": 0.00,
     *     "shipping_cost": 0.00
     *   },
     *   "cartItems": [],
     *   "totals": {
     *     "subtotal": 0.00,
     *     "shipping_cost": 0.00,
     *     "tax": 0.00,
     *     "total": 0.00,
     *     "currency": "USD"
     *   }
     * }
     * @response 404 {
     *   "error": "Cart item not found"
     * }
     * @response 401 {
     *   "message": "Unauthenticated."
     * }
     */
    public function removeItem($cartItemId): JsonResponse
    {
        $cart = $this->getOrCreateCart();
        $cartItem = CartItem::where('cart_id', $cart->id)->find($cartItemId);

        if (!$cartItem) {
            return response()->json(['error' => 'Cart item not found'], 404);
        }

        if ($cartItem->bundle_id) {
            CartItem::where('bundle_id', $cartItem->bundle_id)->delete();
        } else {
            $cartItem->delete();
        }

        $cartItems = $this->loadCartItems($cart);
        $totals = $this->calculateTotals($cart, $cartItems);

        return response()->json([
            'cart' => new CartResource($cart),
            'cartItems' => CartItemResource::collection($cartItems),
            'totals' => $totals,
        ]);
    }

    /**
     * Proceed to checkout
     *
     * Validates the cart and prepares it for checkout. Ensures valid quantities and shipping details, then returns a checkout URL.
     *
     * @authenticated
     * @response 200 {
     *   "message": "Cart ready for checkout",
     *   "checkout_url": "http://example.com/checkout",
     *   "cart": {
     *     "id": 1,
     *     "user_id": 1,
     *     "session_id": null,
     *     "subtotal": 99.99,
     *     "total": 109.99,
     *     "tax_percentage": 5,
     *     "tax_amount": 5.00,
     *     "shipping_cost": 5.00,
     *     "country_id": 1,
     *     "governorate_id": 1,
     *     "city_id": 1,
     *     "shipping_type_id": 1
     *   }
     * }
     * @response 422 {
     *   "error": "The maximum quantity allowed per product is 10. Need more? Contact us via our support page.",
     *   "support_link": "http://example.com/contact"
     * }
     * @response 422 {
     *   "errors": {
     *     "country_id": ["The country id field is required."]
     *   }
     * }
     * @response 401 {
     *   "message": "Unauthenticated."
     * }
     */
    public function checkout(Request $request): JsonResponse
    {
        $cart = $this->getOrCreateCart();
        $cartItems = $this->loadCartItems($cart);

        // Validate cart items
        foreach ($cartItems as $item) {
            if ($item->quantity < 1) {
                return response()->json([
                    'error' => 'Please enter a valid quantity for all products. For help, visit our support page.',
                    'support_link' => route('contact.us'),
                ], 422);
            }
            if ($item->quantity > 10) {
                return response()->json([
                    'error' => 'The maximum quantity allowed per product is 10. Need more? Contact us via our support page.',
                    'support_link' => route('contact.us'),
                ], 422);
            }
        }

        // Validate shipping and location
        $validator = Validator::make([
            'selected_shipping' => $cart->shipping_type_id,
            'country_id' => $cart->country_id,
            'governorate_id' => $cart->governorate_id,
            'city_id' => $cart->city_id,
        ], [
            'selected_shipping' => Setting::isShippingEnabled() ? 'required' : 'nullable',
            'country_id' => 'required|exists:countries,id',
            'governorate_id' => 'required|exists:governorates,id',
            'city_id' => 'nullable|exists:cities,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Calculate totals
        $totals = $this->calculateTotals($cart, $cartItems);
        $taxPercentage = Setting::first()?->tax_percentage ?? 0;
        $taxAmount = ($taxPercentage > 0) ? ($totals['subtotal'] * $taxPercentage / 100) : 0;

        // Update cart
        $cart->update([
            'subtotal' => $totals['subtotal'],
            'total' => $totals['total'],
            'tax_percentage' => $taxPercentage,
            'tax_amount' => $taxAmount,
            'shipping_cost' => $totals['shipping_cost'],
        ]);

        // Return checkout URL or data
        return response()->json([
            'message' => 'Cart ready for checkout',
            'checkout_url' => route('checkout.index'),
            'cart' => new CartResource($cart),
        ]);
    }

    /**
     * Get or create cart for the user or session.
     */
    private function getOrCreateCart(): Cart
    {
        if (Auth::check()) {
            return Cart::firstOrCreate(['user_id' => Auth::id()], ['session_id' => null]);
        }

        $sessionId = Session::get('cart_session', Session::getId());
        Session::put('cart_session', $sessionId);
        return Cart::firstOrCreate(['session_id' => $sessionId], ['user_id' => null]);
    }

    /**
     * Load cart items with necessary data.
     */
    private function loadCartItems(Cart $cart): \Illuminate\Database\Eloquent\Collection
    {
        return CartItem::where('cart_id', $cart->id)
            ->with(['product', 'bundle', 'size', 'color'])
            ->get();
    }

    /**
     * Calculate totals (subtotal, shipping, tax, total).
     */
    private function calculateTotals(Cart $cart, $cartItems): array
    {
        $subtotal = 0.0;
        $seenBundles = [];
        $locationBasedShippingCosts = [];

        foreach ($cartItems as $item) {
            $priceString = $item->product ? $item->product->discount_price_for_current_country : '0 USD';
            $price = $this->extractPrice($priceString);

            if (!empty($item->bundle_id)) {
                if (!in_array($item->bundle_id, $seenBundles)) {
                    $subtotal += $item->quantity * $price;
                    $seenBundles[] = $item->bundle_id;
                }
            } else {
                $subtotal += $item->quantity * $price;
            }

            if ($item->product_id) {
                $product = Product::find($item->product_id);
                if ($product) {
                    $locationBasedShippingCosts[] = $this->calculateProductShippingCost($product, $cart);
                }
            } elseif ($item->bundle_id) {
                $bundleProducts = Product::where('bundle_id', $item->bundle_id)->get();
                foreach ($bundleProducts as $product) {
                    $locationBasedShippingCosts[] = $this->calculateProductShippingCost($product, $cart);
                }
            }
        }

        $isShippingLocationEnabled = Setting::isShippingLocationsEnabled();
        $locationBasedShippingCost = ($isShippingLocationEnabled && !empty($locationBasedShippingCosts))
            ? max($locationBasedShippingCosts)
            : 0.0;

        $shippingTypeCost = $cart->shipping_type_id
            ? ShippingType::find($cart->shipping_type_id)?->cost ?? 0.0
            : 0.0;

        if (count($cartItems) === 1 && $cartItems[0]->product && $cartItems[0]->product->isfabs_shipping) {
            $shippingTypeCost = 0.0;
        }

        $shippingCost = max($shippingTypeCost, $locationBasedShippingCost);
        $taxPercentage = Setting::first()?->tax_percentage ?? 0;
        $tax = ($taxPercentage > 0) ? ($subtotal * $taxPercentage / 100) : 0.0;
        $total = $subtotal + $shippingCost + $tax;

        return [
            'subtotal' => $subtotal,
            'shipping_cost' => $shippingCost,
            'tax' => $tax,
            'total' => $total,
            'currency' => $this->extractCurrency($cartItems[0]->product->discount_price_for_current_country ?? 'USD'),
        ];
    }

    /**
     * Extract numeric price from string.
     */
    private function extractPrice($priceString): float
    {
        return (float) preg_replace('/[^0-9.]/', '', $priceString);
    }

    /**
     * Extract currency from price string.
     */
    private function extractCurrency($priceString): string
    {
        return preg_replace('/[\d.]/', '', trim($priceString));
    }

    /**
     * Calculate product shipping cost.
     */
    private function calculateProductShippingCost(Product $product, Cart $cart): float
    {
        if ($product->is_free_shipping || !Setting::isShippingLocationsEnabled()) {
            return 0.0;
        }

        $shippingCosts = $product->shippingCosts()->get();

        if ($cart->city_id) {
            $cityCost = $shippingCosts->where('city_id', $cart->city_id)->first();
            if ($cityCost) {
                return $cityCost->cost;
            }
        }

        if ($cart->governorate_id) {
            $governorateCost = $shippingCosts
                ->where('governorate_id', $cart->governorate_id)
                ->whereNull('city_id')
                ->first();
            if ($governorateCost) {
                return $governorateCost->cost;
            }

            $zone = Governorate::find($cart->governorate_id)?->shippingZones()->first();
            if ($zone) {
                $zoneCost = $shippingCosts->where('shipping_zone_id', $zone->id)->first();
                if ($zoneCost) {
                    return $zoneCost->cost;
                }
            }
        }

        if ($cart->country_id) {
            $countryCost = $shippingCosts
                ->where('country_id', $cart->country_id)
                ->whereNull('governorate_id')
                ->whereNull('city_id')
                ->first();
            if ($countryCost) {
                return $countryCost->cost;
            }
        }

        return $this->getFallbackLocationBasedCost($cart);
    }

    /**
     * Get fallback location-based cost.
     */
    private function getFallbackLocationBasedCost(Cart $cart): float
    {
        if (!Setting::isShippingLocationsEnabled()) {
            return 0.0;
        }

        if ($cart->city_id) {
            $cityCost = City::where('id', $cart->city_id)->value('cost');
            if (!is_null($cityCost) && $cityCost > 0) {
                return $cityCost;
            }
        }

        if ($cart->governorate_id) {
            $governorateCost = Governorate::where('id', $cart->governorate_id)->value('cost');
            if (!is_null($governorateCost) && $governorateCost > 0) {
                return $governorateCost;
            }

            $zoneCost = Governorate::find($cart->governorate_id)?->shippingZones()->pluck('cost')->first();
            if (!is_null($zoneCost) && $zoneCost > 0) {
                return $zoneCost;
            }
        }

        if ($cart->country_id) {
            $countryCost = Country::where('id', $cart->country_id)->value('cost');
            if (!is_null($countryCost) && $countryCost > 0) {
                return $countryCost;
            }
        }

        return 0.0;
    }
}
