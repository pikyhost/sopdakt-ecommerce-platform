<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Setting;
use App\Services\CartServiceApi;
use Illuminate\Support\Facades\Auth;

class CartShippingController extends Controller
{
    public function check()
    {
        $cart = $this->getCart();

        if (!$cart) {
            return response()->json(['message' => 'Cart not found.'], 404);
        }

        $threshold = Setting::first()?->free_shipping_threshold;

        if ($threshold === null) {
            return response()->json(['message' => 'Free shipping threshold not set.'], 500);
        }

        $isFreeShipping = $cart->total >= $threshold;

        $cart->update([
            'is_free_shipping' => $isFreeShipping,
            'shipping_cost' => $isFreeShipping ? null : $cart->shipping_cost,
        ]);

        return response()->json([
            'is_free_shipping' => $isFreeShipping,
            'shipping_cost' => $cart->shipping_cost,
            'threshold' => $threshold,
            'cart_total' => $cart->total,
        ]);
    }

    /**
     * Get or create cart with enhanced validation
     */
    private function getCart()
    {
        if (Auth::guard('sanctum')->check()) {
            return Cart::where('user_id', Auth::guard('sanctum')->id())->first();
        }
        return Cart::where('session_id',app(CartServiceApi::class)->getSessionId())
            ->first();
    }
}
