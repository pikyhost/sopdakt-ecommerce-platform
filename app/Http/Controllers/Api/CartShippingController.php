<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Setting;
use App\Services\CartServiceApi;
use Illuminate\Support\Facades\Auth;

class CartShippingController extends Controller
{
    /**
     * Check if cart qualifies for free shipping
     *
     * This endpoint checks if the current cart meets the free shipping threshold set in the system settings.
     *
     * @response 200 {
     *   "is_free_shipping": true,
     *   "shipping_cost": null,
     *   "threshold": 100.00,
     *   "cart_total": 120.50
     * }
     * @response 404 {
     *   "message": "Cart not found."
     * }
     * @response 500 {
     *   "message": "Free shipping threshold not set."
     * }
     *
     * @group Cart
     * @authenticated
     */
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
            'shipping_cost' => $isFreeShipping ? 0 : $cart->shipping_cost,
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
