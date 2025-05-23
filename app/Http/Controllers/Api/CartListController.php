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
use App\Models\Discount;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
     * @bodyParam coupon_code string nullable Coupon code to apply. Example: SUMMER20
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
     *     "currency": "USD",
     *     "free_shipping_applied": false,
     *     "discount_applied": 10.00
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
     * @response 422 {
     *   "error": "Invalid or expired coupon."
     * }
     */
    public function index(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'coupon_code' => ['nullable', 'string', 'exists:coupons,code'],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $cart = $this->getCart(); // ✅ only get if it exists

        if (!$cart) {
            return response()->json(['message' => __('Cart is empty')]);
        }

        $cartItems = $this->loadCartItems($cart);

        if ($cartItems->isEmpty()) {
            return response()->json(['message' => __('Cart is empty')]);
        }

        $totals = $this->calculateTotals($cart, $cartItems, $request->coupon_code);
        $productIds = collect($cartItems)->pluck('product.id')->filter()->unique();

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
            'totals' => $totals,
//            'countries' => Country::all()->map(fn ($country) => [
//                'id' => $country->id,
//                'name' => $country->getTranslation('name', $locale),
//            ]),
//            'governorates' => $cart->country_id
//                ? Governorate::where('country_id', $cart->country_id)
//                    ->get()
//                    ->map(fn ($gov) => [
//                        'id' => $gov->id,
//                        'name' => $gov->getTranslation('name', $locale),
//                    ])
//                : [],
//            'cities' => $cart->governorate_id
//                ? City::where('governorate_id', $cart->governorate_id)
//                    ->get()
//                    ->map(fn ($city) => [
//                        'id' => $city->id,
//                        'name' => $city->getTranslation('name', $locale),
//                    ])
//                : [],
//            'shipping_types' => Setting::isShippingEnabled()
//                ? ShippingType::where('status', true)
//                    ->get()
//                    ->map(fn ($type) => [
//                        'id' => $type->id,
//                        'name' => $type->getTranslation('name', $locale),
//                    ])
//                : [],
            'complementary_products' => ComplementaryProductResource::collection($complementaryProducts),
        ]);
    }
    
      private function getOrCreateCart(): Cart
    {
        if (Auth::check()) {
            return Cart::firstOrCreate(
                ['user_id' => Auth::id()],
                [
                    'session_id' => session()->getId(),
                    'country_id' => request('country_id'),
                    'governorate_id' => request('governorate_id'),
                ]
            );
        }

        // Use consistent session ID for guest
        $sessionId = session()->getId();

        return Cart::firstOrCreate(
            ['session_id' => $sessionId],
            [
                'user_id' => null,
                'country_id' => request('country_id'),
                'governorate_id' => request('governorate_id'),
            ]
        );
    }


    public function getRelatedCartData(Request $request)
    {
        $locale = app()->getLocale(); // or use $request->input('locale')

        $countryId = $request->input('country_id');
        $governorateId = $request->input('governorate_id');

        return response()->json([
            'countries' => Country::all()->map(fn ($country) => [
                'id' => $country->id,
                'name' => $country->getTranslation('name', $locale),
            ]),
            'governorates' => $countryId
                ? Governorate::where('country_id', $countryId)
                    ->get()
                    ->map(fn ($gov) => [
                        'id' => $gov->id,
                        'name' => $gov->getTranslation('name', $locale),
                    ])
                : [],
            'cities' => $governorateId
                ? City::where('governorate_id', $governorateId)
                    ->get()
                    ->map(fn ($city) => [
                        'id' => $city->id,
                        'name' => $city->getTranslation('name', $locale),
                    ])
                : [],
            'shipping_types' => Setting::isShippingEnabled()
                ? ShippingType::where('status', true)
                    ->get()
                    ->map(fn ($type) => [
                        'id' => $type->id,
                        'name' => $type->getTranslation('name', $locale),
                    ])
                : [],
            'currency' => Setting::getCurrency(),
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
     * @bodyParam coupon_code string nullable Coupon code to apply. Example: SUMMER20
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
     *     "currency": "USD",
     *     "free_shipping_applied": false,
     *     "discount_applied": 10.00
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
     * @response 422 {
     *   "error": "Invalid or expired coupon."
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
            'coupon_code' => ['nullable', 'string', 'exists:coupons,code'],
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
        $totals = $this->calculateTotals($cart, $cartItems, $request->coupon_code);

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
     * @bodyParam coupon_code string nullable Coupon code to apply. Example: SUMMER20
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
     *     "currency": "USD",
     *     "free_shipping_applied": false,
     *     "discount_applied": 7.50
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
     * @response 422 {
     *   "error": "Invalid or expired coupon."
     * }
     * @response 401 {
     *   "message": "Unauthenticated."
     * }
     */
    public function updateQuantity(Request $request, $cartItemId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'action' => 'required|in:increase,decrease',
            'coupon_code' => ['nullable', 'string', 'exists:coupons,code'],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $cartItem = CartItem::with('product')->find($cartItemId);

        if (!$cartItem) {
            return response()->json(['error' => __('Cart item not found')], 404);
        }

        if ($request->action === 'increase') {
            $cartItem->increment('quantity');
        } elseif ($request->action === 'decrease') {
            if ($cartItem->quantity <= 1) {
                return response()->json([
                    'error' => __('Quantity cannot be less than 1'),
                    'cartItemId' => $cartItem->id,
                    'quantity' => $cartItem->quantity,
                ], 422);
            }

            $cartItem->decrement('quantity');
        }

        $cart = $this->getOrCreateCart();
        $cartItems = $this->loadCartItems($cart);
        $totals = $this->calculateTotals($cart, $cartItems, $request->coupon_code);

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
     * @bodyParam coupon_code string nullable Coupon code to apply. Example: SUMMER20
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
     *     "currency": "USD",
     *     "free_shipping_applied": false,
     *     "discount_applied": 0.00
     *   }
     * }
     * @response 404 {
     *   "error": "Cart item not found"
     * }
     * @response 422 {
     *   "error": "Invalid or expired coupon."
     * }
     * @response 401 {
     *   "message": "Unauthenticated."
     * }
     */
    public function removeItem(Request $request, $cartItemId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'coupon_code' => ['nullable', 'string', 'exists:coupons,code'],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

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
        $totals = $this->calculateTotals($cart, $cartItems, $request->coupon_code);

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
     * @bodyParam selected_shipping integer nullable ID of the selected shipping type. Example: 1
     * @bodyParam country_id integer required ID of the country. Example: 1
     * @bodyParam governorate_id integer required ID of the governorate. Example: 1
     * @bodyParam city_id integer nullable ID of the city. Example: 1
     * @bodyParam coupon_code string nullable Coupon code to apply. Example: SUMMER20
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
     * @response 422 {
     *   "error": "Invalid or expired coupon."
     * }
     * @response 401 {
     *   "message": "Unauthenticated."
     * }
     */
    public function checkout(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'selected_shipping' => Setting::isShippingEnabled() ? 'required|exists:shipping_types,id' : 'nullable',
                'country_id' => 'required|exists:countries,id',
                'governorate_id' => 'required|exists:governorates,id',
                'city_id' => 'nullable|exists:cities,id',
                'coupon_code' => ['nullable', 'string', 'exists:coupons,code'],
            ]);

            if ($validator->fails()) {
                DB::rollBack();
                return response()->json([
                    'error' => 'Validation failed',
                    'errors' => $validator->errors(),
                    'request_data' => $request->all(),
                ], 422);
            }

            $cart = $this->getOrCreateCart();
            $cartItems = $this->loadCartItems($cart);

            // Validate cart is not empty
            if ($cartItems->isEmpty()) {
                DB::rollBack();
                return response()->json([
                    'error' => 'Your cart is empty. Please add products before checkout.',
                    'cart_url' => route('cart.index'),
                ], 422);
            }

            // Validate cart items (quantity checks)
            foreach ($cartItems as $item) {
                if ($item->quantity < 1) {
                    DB::rollBack();
                    return response()->json([
                        'error' => 'Please enter a valid quantity for all products.',
                    ], 422);
                }

                if ($item->quantity > 10) {
                    DB::rollBack();
                    return response()->json([
                        'error' => 'The maximum quantity allowed per product is 10. Contact us via our support page.',
                    ], 422);
                }
            }

            // Validate all cart items
            $validationErrors = $this->validateCartItems($cartItems);
            if ($validationErrors) {
                DB::rollBack();
                return $validationErrors;
            }

            // Process shipping information
            $shippingValidation = $this->validateAndProcessShipping($request, $cart);
            if ($shippingValidation instanceof JsonResponse) {
                DB::rollBack();
                return $shippingValidation;
            }

            // Calculate totals with discounts and coupons
            $totals = $this->calculateTotals($cart, $cartItems, $request->coupon_code);
            if (!is_numeric($totals['subtotal']) || !is_numeric($totals['total'])) {
                DB::rollBack();
                throw new \Exception('Invalid totals calculation');
            }

            // Update cart with final details
            $cart->update([
                'subtotal' => $totals['subtotal'],
                'total' => $totals['total'],
                'tax_percentage' => $totals['tax_percentage'],
                'tax_amount' => $totals['tax'],
                'shipping_cost' => $totals['shipping_cost'],
                'shipping_type_id' => $request->selected_shipping ?? null,
                'country_id' => $request->country_id,
                'governorate_id' => $request->governorate_id,
                'city_id' => $request->city_id,
            ]);

            // Record coupon usage if applicable
            if ($totals['coupon'] && Auth::check()) {
                $totals['coupon']->usages()->create([
                    'user_id' => Auth::id(),
                    'order_id' => null, // Will be updated when order is created
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Cart ready for checkout',
                'cart' => new CartResource($cart),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Checkout error: ' . $e->getMessage());
            return response()->json([
                'error' => 'An unexpected error occurred during checkout. Please try again.',
            ], 500);
        }
    }

    /**
     * Validate all cart items
     */
    private function validateCartItems($cartItems): ?JsonResponse
    {
        foreach ($cartItems as $item) {
            // Validate product availability
            if (!$item->product && !$item->bundle) {
                return response()->json([
                    'error' => 'Some items in your cart are no longer available.',
                    'cart_url' => route('cart.index'),
                ], 422);
            }

            // Validate product price
            if ($item->product) {
                $priceString = $item->product->discount_price_for_current_country ?? '0 USD';
                $price = $this->extractPrice($priceString);
                if ($price <= 0) {
                    return response()->json([
                        'error' => 'Invalid price for product: ' . ($item->product->name ?? ''),
                        'cart_url' => route('cart.index'),
                    ], 422);
                }
            }
        }

        return null;
    }

    /**
     * Validate and process shipping information
     */
    private function validateAndProcessShipping(Request $request, Cart $cart): ?JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'selected_shipping' => Setting::isShippingEnabled() ? 'required|exists:shipping_types,id' : 'nullable',
                'country_id' => 'required|exists:countries,id',
                'governorate_id' => 'required|exists:governorates,id',
                'city_id' => 'nullable|exists:cities,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => 'Validation failed',
                    'errors' => $validator->errors(),
                    'request_data' => $request->all()
                ], 422);
            }

            if (Setting::isShippingEnabled() && $request->selected_shipping) {
                $shippingType = ShippingType::where('id', $request->selected_shipping)
                    ->where('status', true)
                    ->first();

                if (!$shippingType) {
                    $availableMethods = ShippingType::where('status', true)
                        ->get()
                        ->map(function ($method) {
                            return [
                                'id' => $method->id,
                                'name' => $method->name,
                                'cost' => $method->cost,
                                'supported_countries' => $method->countries->pluck('id')
                            ];
                        });

                    return response()->json([
                        'error' => 'Shipping method unavailable',
                        'details' => [
                            'selected_id' => $request->selected_shipping,
                            'available_methods' => $availableMethods,
                            'your_country_id' => $request->country_id
                        ],

                    ], 422);
                }
            }

            $updateResult = $cart->update([
                'country_id' => $request->country_id,
                'governorate_id' => $request->governorate_id,
                'city_id' => $request->city_id,
            ]);

            if (!$updateResult) {
                throw new \Exception("Failed to update cart shipping information");
            }

            return null;

        } catch (\Exception $e) {
            Log::error('Shipping Processing Error', [
                'error' => $e->getMessage(),
                'cart_id' => $cart->id,
                'request' => $request->all()
            ]);
            throw $e;
        }
    }

    /**
     * Get or create cart with enhanced validation
     */
    private function getCart(): ?Cart
    {
        if (Auth::check()) {
            return Cart::where('user_id', Auth::id())->first();
        }

        return Cart::where('session_id', session()->getId())->first();
    }


    /**
     * Load cart items with necessary data
     */
    private function loadCartItems(Cart $cart): \Illuminate\Database\Eloquent\Collection
    {
        // Verify the cart belongs to the current user/session
        if (Auth::check()) {
            if ($cart->user_id !== Auth::id()) {
                return new \Illuminate\Database\Eloquent\Collection();
            }
        } else {
            $sessionId = Session::get('cart_session');
            if ($cart->session_id !== $sessionId) {
                return new \Illuminate\Database\Eloquent\Collection();
            }
        }

        return $cart->items()
            ->with(['product', 'bundle', 'size', 'color'])
            ->get();
    }

    /**
     * Calculate totals (subtotal, shipping, tax, total) with discounts and coupons
     */
    private function calculateTotals(Cart $cart, $cartItems, ?string $couponCode = null): array
    {
        $subtotal = 0.0;
        $discountApplied = 0.0;
        $seenBundles = [];
        $locationBasedShippingCosts = [];
        $isFreeShipping = false;

        // Validate coupon if provided
        $coupon = null;
        if ($couponCode) {
            $coupon = Coupon::where('code', $couponCode)
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
                throw new \Exception('Invalid or expired coupon.');
            }

            // Check coupon usage limits
            if ($coupon->total_usage_limit) {
                $totalUsages = $coupon->usages()->count();
                if ($totalUsages >= $coupon->total_usage_limit) {
                    throw new \Exception('Coupon usage limit reached.');
                }
            }

            if ($coupon->usage_limit_per_user && Auth::check()) {
                $userUsages = $coupon->usages()->where('user_id', Auth::id())->count();
                if ($userUsages >= $coupon->usage_limit_per_user) {
                    throw new \Exception('Coupon usage limit per user reached.');
                }
            }
        }

        // Calculate item subtotals with discounts
        foreach ($cartItems as $item) {
            $priceString = $item->product ? $item->product->discount_price_for_current_country : '0 USD';
            $basePrice = $this->extractPrice($priceString);
            $discountAmount = 0.0;

            // Skip if already processed as part of a bundle
            if ($item->bundle_id && in_array($item->bundle_id, $seenBundles)) {
                continue;
            }

            // Find applicable item-level discounts
            $discounts = [];
            if ($item->product) {
                $product = $item->product;
                $discounts = Discount::where('is_active', true)
                    ->where(function ($q) {
                        $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
                    })
                    ->where(function ($q) {
                        $q->whereNull('ends_at')->orWhere('ends_at', '>=', now());
                    })
                    ->where(function ($q) use ($product) {
                        $q->where('applies_to', 'product')
                            ->whereIn('id', function ($q2) use ($product) {
                                $q2->select('discount_id')->from('discount_product')
                                    ->where('product_id', $product->id);
                            })
                            ->orWhere('applies_to', 'category')
                            ->whereIn('id', function ($q2) use ($product) {
                                $q2->select('discount_id')->from('discount_category')
                                    ->whereIn('category_id', $product->category()->pluck('categories.id'));
                            })
                            ->orWhere('applies_to', 'collection')
                            ->whereIn('id', function ($q2) use ($product) {
                                $q2->select('discount_id')->from('collection_discount')
                                    ->whereIn('collection_id', function ($q3) use ($product) {
                                        $q3->select('collection_id')->from('collection_product')
                                            ->where('product_id', $product->id);
                                    });
                            });
                    })
                    ->get();

                // Filter discounts based on coupon
                if ($coupon) {
                    $discounts = $discounts->filter(function ($discount) use ($coupon) {
                        return $discount->id === $coupon->discount_id && $discount->requires_coupon;
                    })->merge(
                        $discounts->where('requires_coupon', false)
                    );
                } else {
                    $discounts = $discounts->where('requires_coupon', false);
                }

                // Apply item-level discounts
                foreach ($discounts as $discount) {
                    if ($discount->min_order_value && $basePrice * $item->quantity < $discount->min_order_value) {
                        continue;
                    }
                    if ($discount->usage_limit) {
                        $usageCount = DB::table('coupon_usages')
                            ->whereIn('coupon_id', function ($q) use ($discount) {
                                $q->select('id')->from('coupons')->where('discount_id', $discount->id);
                            })->count();
                        if ($usageCount >= $discount->usage_limit) {
                            continue;
                        }
                    }
                    if ($discount->discount_type === 'percentage') {
                        $discountAmount += ($basePrice * $discount->value) / 100;
                    } elseif ($discount->discount_type === 'fixed') {
                        $discountAmount += $discount->value;
                    }
                }
            }

            // Calculate item subtotal
            $pricePerUnit = max(0, $basePrice - $discountAmount);
            $itemSubtotal = $pricePerUnit * $item->quantity;
            $discountApplied += $discountAmount * $item->quantity;

            // Update cart item
            $item->update([
                'price_per_unit' => $pricePerUnit,
                'subtotal' => $itemSubtotal,
            ]);

            if ($item->bundle_id) {
                $seenBundles[] = $item->bundle_id;
            }

            $subtotal += $itemSubtotal;

            // Calculate shipping costs
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

        // Apply cart-level discounts
        $cartDiscounts = Discount::where('is_active', true)
            ->where('applies_to', 'cart')
            ->where(function ($q) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', now());
            })
            ->get();

        if ($coupon) {
            $cartDiscounts = $cartDiscounts->filter(function ($discount) use ($coupon) {
                return $discount->id === $coupon->discount_id && $discount->requires_coupon;
            })->merge(
                $cartDiscounts->where('requires_coupon', false)
            );
        } else {
            $cartDiscounts = $cartDiscounts->where('requires_coupon', false);
        }

        $cartDiscountAmount = 0.0;
        foreach ($cartDiscounts as $discount) {
            if ($discount->min_order_value && $subtotal < $discount->min_order_value) {
                continue;
            }
            if ($discount->usage_limit) {
                $usageCount = DB::table('coupon_usages')
                    ->whereIn('coupon_id', function ($q) use ($discount) {
                        $q->select('id')->from('coupons')->where('discount_id', $discount->id);
                    })->count();
                if ($usageCount >= $discount->usage_limit) {
                    continue;
                }
            }
            if ($discount->discount_type === 'percentage') {
                $cartDiscountAmount += ($subtotal * $discount->value) / 100;
            } elseif ($discount->discount_type === 'fixed') {
                $cartDiscountAmount += $discount->value;
            } elseif ($discount->discount_type === 'free_shipping') {
                $isFreeShipping = true;
            }
        }

        $discountApplied += $cartDiscountAmount;
        $subtotal = max(0, $subtotal - $cartDiscountAmount);

        // Calculate shipping cost
        $isShippingLocationEnabled = Setting::isShippingLocationsEnabled();
        $locationBasedShippingCost = ($isShippingLocationEnabled && !empty($locationBasedShippingCosts))
            ? max($locationBasedShippingCosts)
            : 0.0;

        $shippingTypeCost = $cart->shipping_type_id
            ? ShippingType::find($cart->shipping_type_id)?->cost ?? 0.0
            : 0.0;

        // Check for free shipping threshold
        $freeShippingThreshold = Setting::getFreeShippingThreshold();
        $shouldApplyFreeShipping = $freeShippingThreshold > 0 && $subtotal >= $freeShippingThreshold;

        // Apply free shipping logic
        if (count($cartItems) === 1 && $cartItems[0]->product && $cartItems[0]->product->isfabs_shipping) {
            $shippingTypeCost = 0.0;
            $locationBasedShippingCost = 0.0;
        } elseif ($shouldApplyFreeShipping || $isFreeShipping) {
            $shippingTypeCost = 0.0;
            $locationBasedShippingCost = 0.0;
        }

        $shippingCost = max($shippingTypeCost, $locationBasedShippingCost);

        // Calculate tax
        $taxPercentage = Setting::first()?->tax_percentage ?? 0;
        $tax = ($taxPercentage > 0) ? ($subtotal * $taxPercentage / 100) : 0.0;

        // Calculate total
        $total = $subtotal + $shippingCost + $tax;

        return [
            'subtotal' => $subtotal,
            'shipping_cost' => $shippingCost,
            'tax' => $tax,
            'tax_percentage' => $taxPercentage,
            'total' => $total,
            'currency' => $cartItems->isNotEmpty()
                ? $this->extractCurrency($cartItems[0]->product->discount_price_for_current_country ?? 'USD')
                : 'USD',
            'free_shipping_applied' => $shouldApplyFreeShipping || $isFreeShipping,
            'discount_applied' => $discountApplied,
            'coupon' => $coupon,
        ];
    }

    /**
     * Extract numeric price from string
     */
    private function extractPrice($priceString): float
    {
        return (float) preg_replace('/[^0-9.]/', '', $priceString);
    }

    /**
     * Extract currency from price string
     */
    private function extractCurrency($priceString): string
    {
        return preg_replace('/[\d.]/', '', trim($priceString));
    }

    /**
     * Calculate product shipping cost
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
     * Get fallback location-based cost
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

