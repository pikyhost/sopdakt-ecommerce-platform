<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductColorSize;
use App\Models\Setting;
use App\Services\CartServiceApi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class CartController extends Controller
{
    public function store(Request $request)
    {
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
