<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Country;
use App\Models\Product;
use App\Models\ProductColorSize;
use App\Models\Setting;
use App\Models\ShippingType;
use App\Services\CartServiceApi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class CartController extends Controller
{
    public function getNestedCartData(Request $request)
    {
        $locale = app()->getLocale(); // or optionally: $request->input('locale', app()->getLocale());

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
            'currency' => Setting::getCurrency(),
        ]);
    }

    public function store(Request $request)
    {

        $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1', 'max:10'],
            'color_id' => ['nullable', 'exists:colors,id'],
            'size_id' => ['nullable', 'exists:sizes,id'],
        ]);

        $product = Product::findOrFail($request->product_id);

        // Custom validation: check if the product must be ordered in quantity >= 2
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


        $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1', 'max:10'],
            'color_id' => ['nullable', 'exists:colors,id'],
            'size_id' => ['nullable', 'exists:sizes,id'],
        ]);

        $cart = app(CartServiceApi::class)->getCart();

        app(CartServiceApi::class)->addItemToCart(
            $cart,
            $request->product_id,
            $request->quantity,
            $request->color_id,
            $request->size_id
        );

        return response()->json([
            'message' => 'Product added to cart successfully.',
            'cart_id' => $cart->id,
        ]);
    }

    /**
     * Update Cart Item Quantity
     *
     * @group Cart
     *
     * Updates the quantity of a specific cart item.
     *
     * @urlParam itemId integer required The ID of the cart item to update. Example: 1
     * @bodyParam quantity integer required New quantity (1-10). Example: 3
     *
     * @response 200 {
     *   "message": "Cart item updated successfully."
     * }
     * @response 404 {
     *   "message": "Item not found."
     * }
     * @response 422 {
     *   "message": "Requested quantity exceeds stock!"
     * }
     */
    public function updateQuantity(Request $request, $itemId)
    {
        $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:10'],
        ]);

        $item = CartItem::findOrFail($itemId);

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

        return response()->json(['message' => 'Cart item updated successfully.']);
    }

    /**
     * Remove Item from Cart
     *
     * @group Cart
     *
     * Removes a specific item from the cart.
     *
     * @urlParam itemId integer required The ID of the cart item to remove. Example: 1
     *
     * @response 200 {
     *   "message": "Item removed from cart."
     * }
     * @response 404 {
     *   "message": "Item not found."
     * }
     */
    public function destroy($itemId)
    {
        $item = CartItem::findOrFail($itemId);
        $item->delete();

        return response()->json(['message' => 'Item removed from cart.']);
    }
}
