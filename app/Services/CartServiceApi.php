<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;

class CartServiceApi
{
    public function getCart()
    {
        $user = Auth::user();
        $sessionId = $this->getSessionId();

        if ($user) {
          dd(19);
            $cart = Cart::firstOrCreate(
                ['user_id' => $user->id],
                ['session_id' => null]
            );

            // Merge any existing guest cart
            if ($sessionId) {
                dd(27);
                $this->mergeGuestCart($cart, $sessionId);
            }
        } else {
            dd(30);
            // For guests - use session-based cart
            $cart = Cart::firstOrCreate(
                ['session_id' => $sessionId],
                ['user_id' => null]
            );
        }

        return $cart;
    }

    public function mergeGuestCart(Cart $userCart, string $sessionId)
    {
        $guestCart = Cart::where('session_id', $sessionId)->first();

        if ($guestCart && $guestCart->id !== $userCart->id) {
            // Transfer all items from guest cart to user cart
            foreach ($guestCart->items as $item) {
                $this->addItemToCart($userCart, $item->product_id, $item->quantity, $item->color_id, $item->size_id);
            }

            // Delete the guest cart
            $guestCart->delete();
        }
    }

    public function addItemToCart(Cart $cart, $productId, $quantity, $colorId = null, $sizeId = null)
    {
        $product = Product::findOrFail($productId);

        // Check stock availability here if needed

        $cartItem = $cart->items()
            ->where('product_id', $productId)
            ->where('color_id', $colorId)
            ->where('size_id', $sizeId)
            ->first();

        $price = (float) $product->discount_price_for_current_country;

        if ($cartItem) {
            $cartItem->increment('quantity', $quantity);
            $cartItem->update(['subtotal' => $cartItem->quantity * $price]);
        } else {
            CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $productId,
                'color_id' => $colorId,
                'size_id' => $sizeId,
                'quantity' => $quantity,
                'price_per_unit' => $price,
                'subtotal' => $quantity * $price,
                'currency_id' => optional(Setting::getCurrency())->id, // 🔁 Add this line
            ]);
        }

        $this->updateCartTotals($cart);
    }

    protected function updateCartTotals(Cart $cart)
    {
        $subtotal = $cart->items()->sum('subtotal');

        // Add tax, shipping calculations as needed
        $cart->update([
            'subtotal' => $subtotal,
            'total' => $subtotal,
        ]);
    }

    protected function getSessionId()
    {
        // For API requests
        if (request()->header('X-Guest-Session')) {
            return request()->header('X-Guest-Session');
        }

        // For web requests
        return session()->getId();
    }
}
