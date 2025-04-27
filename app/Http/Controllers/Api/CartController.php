<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductColor;
use App\Models\ProductColorSize;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $cart = Cart::where(function ($query) {
            if (Auth::check()) {
                $query->where('user_id', Auth::id());
            } else {
                $query->where('session_id', Session::getId());
            }
        })->with('items.product')->first();

        return response()->json([
            'cart' => $cart,
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

        // Create or find the cart
        $cart = Cart::firstOrCreate(
            ['user_id' => Auth::id()],
            ['session_id' => Auth::check() ? null : Session::getId()]
        );

        // Create or update the cart item
        $cartItem = $cart->items()
            ->where('product_id', $product->id)
            ->where('size_id', $request->size_id)
            ->where('color_id', $request->color_id)
            ->first();

        $price = (float) $product->discount_price_for_current_country;
        $subtotal = $request->quantity * $price;

        if ($cartItem) {
            $cartItem->increment('quantity', $request->quantity);
            $cartItem->update(['subtotal' => $cartItem->quantity * $cartItem->price_per_unit]);
        } else {
            $cart->items()->create([
                'product_id' => $product->id,
                'size_id' => $request->size_id,
                'color_id' => $request->color_id,
                'quantity' => $request->quantity,
                'price_per_unit' => $price,
                'subtotal' => $subtotal,
                'currency_id' => optional(Setting::getCurrency())->id,
            ]);
        }

        return response()->json(['message' => 'Product added to cart successfully.']);
    }

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

    public function destroy($itemId)
    {
        $item = CartItem::findOrFail($itemId);
        $item->delete();

        return response()->json(['message' => 'Item removed from cart.']);
    }
}
