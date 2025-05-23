<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\Discount;
use App\Models\ProductCoupon;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CouponController extends Controller
{
    /**
     * Apply coupon to cart
     */
    public function apply(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $cart = $this->getOrCreateCart();

        // Check if cart already has a coupon applied
        if ($cart->coupon_id) {
            return response()->json([
                'message' => 'A coupon is already applied to this cart',
                'cart' => $cart->load('items'),
            ], 422);
        }

        $coupon = Coupon::where('code', $request->code)
            ->where('is_active', true)
            ->first();

        if (!$coupon) {
            return response()->json([
                'message' => 'Invalid coupon code',
            ], 404);
        }

        $discount = $coupon->discount;

        // Validate coupon
        $validation = $this->validateCoupon($coupon, $discount, $cart);
        if ($validation !== true) {
            return response()->json([
                'message' => $validation,
            ], 422);
        }

        // Apply discount to cart
        DB::transaction(function () use ($cart, $coupon, $discount) {
            $this->applyDiscountToCart($cart, $discount);

            // Associate coupon with cart
            $cart->coupon_id = $coupon->id;
            $cart->save();

            // Increment coupon usage if needed
            if ($coupon->total_usage_limit) {
                $coupon->increment('used_count');
            }
        });

        return response()->json([
            'message' => 'Coupon applied successfully',
            'cart' => $cart->fresh()->load('items'),
            'discount' => $discount,
        ]);
    }

    /**
     * Remove coupon from cart
     */
    public function remove(Request $request)
    {
        $cart = $this->getOrCreateCart();

        if (!$cart->coupon_id) {
            return response()->json([
                'message' => 'No coupon applied to this cart',
            ], 422);
        }

        DB::transaction(function () use ($cart) {
            $coupon = $cart->coupon;
            $discount = $coupon->discount;

            // Remove discount effects
            $this->removeDiscountFromCart($cart, $discount);

            // Remove coupon association
            $cart->coupon_id = null;
            $cart->save();
        });

        return response()->json([
            'message' => 'Coupon removed successfully',
            'cart' => $cart->fresh()->load('items'),
        ]);
    }

    /**
     * Validate coupon against cart
     */
    private function validateCoupon(Coupon $coupon, Discount $discount, Cart $cart): bool|string
    {
        // Check coupon active status
        if (!$coupon->is_active || !$discount->is_active) {
            return 'This coupon is not active';
        }

        // Check dates
        $now = Carbon::now();
        if ($discount->starts_at && $now->lt($discount->starts_at)) {
            return 'This coupon is not valid yet';
        }
        if ($discount->ends_at && $now->gt($discount->ends_at)) {
            return 'This coupon has expired';
        }

        // Check usage limits
        if ($discount->usage_limit && $discount->used_count >= $discount->usage_limit) {
            return 'This coupon has reached its usage limit';
        }
        if ($coupon->total_usage_limit && $coupon->used_count >= $coupon->total_usage_limit) {
            return 'This coupon has reached its usage limit';
        }

        // Check per-user limit for authenticated users
        if (Auth::guard('sanctum')->check() && $coupon->usage_limit_per_user) {
            $userUsage = DB::table('carts')
                ->where('user_id', Auth::guard('sanctum')->id())
                ->where('coupon_id', $coupon->id)
                ->count();

            if ($userUsage >= $coupon->usage_limit_per_user) {
                return 'You have reached the usage limit for this coupon';
            }
        }

        // Check minimum order value
        if ($discount->min_order_value && $cart->subtotal < $discount->min_order_value) {
            return 'Minimum order value for this coupon is ' . $discount->min_order_value;
        }

        // Check discount applicability to cart items
        if ($discount->applies_to === 'product') {
            $applicableProducts = $discount->products()->pluck('product_id');
            $cartHasApplicableProducts = $cart->items()->whereIn('product_id', $applicableProducts)->exists();

            if (!$cartHasApplicableProducts) {
                return 'This coupon is not applicable to any products in your cart';
            }
        }

        return true;
    }

    /**
     * Apply discount to cart
     */
    private function applyDiscountToCart(Cart $cart, Discount $discount)
    {
        $subtotal = $cart->subtotal;
        $discountValue = 0;

        switch ($discount->discount_type) {
            case 'percentage':
                $discountValue = $subtotal * ($discount->value / 100);
                break;

            case 'fixed':
                $discountValue = min($discount->value, $subtotal);
                break;

            case 'free_shipping':
                $cart->shipping_cost = 0;
                break;
        }

        // Update cart totals
        $cart->total = $subtotal - $discountValue + ($cart->shipping_cost ?? 0);
        $cart->discount_amount = $discountValue;
        $cart->save();

        // If discount applies to specific products, update those items
        if ($discount->applies_to === 'product') {
            $applicableProducts = $discount->products()->pluck('product_id');

            $cart->items()->whereIn('product_id', $applicableProducts)->each(function ($item) use ($discount) {
                $originalPrice = $item->price_per_unit;
                $discountedPrice = $originalPrice;

                if ($discount->discount_type === 'percentage') {
                    $discountedPrice = $originalPrice * (1 - ($discount->value / 100));
                } elseif ($discount->discount_type === 'fixed') {
                    $discountedPrice = max($originalPrice - $discount->value, 0);
                }

                $item->price_per_unit = $discountedPrice;
                $item->subtotal = $discountedPrice * $item->quantity;
                $item->save();
            });
        }
    }

    /**
     * Remove discount from cart
     */
    private function removeDiscountFromCart(Cart $cart, Discount $discount)
    {
        // Reset shipping cost if it was free shipping
        if ($discount->discount_type == 'free_shipping') {
            // You'll need to recalculate shipping based on your logic
            $cart->shipping_cost = $this->calculateShippingCost($cart);
        }

        // Reset product prices if discount was applied to specific products
        if ($discount->applies_to == 'product') {
            $applicableProducts = $discount->products()->pluck('product_id');

            $cart->items()->whereIn('product_id', $applicableProducts)->each(function ($item) {
                // You might need to store original prices or fetch them from products table
                $productPrice = $item->product->price; // Assuming product relationship exists
                $item->price_per_unit = $productPrice;
                $item->subtotal = $productPrice * $item->quantity;
                $item->save();
            });
        }

        // Recalculate totals
        $subtotal = $cart->items()->sum('subtotal');
        $cart->subtotal = $subtotal;
        $cart->total = $subtotal + ($cart->shipping_cost ?? 0);
        $cart->discount_amount = 0;
        $cart->save();
    }

    /**
     * Get or create cart (reused from your existing code)
     */
    private function getOrCreateCart()
    {
        if (Auth::guard('sanctum')->check()) {
            return Cart::firstOrCreate(
                ['user_id' => Auth::guard('sanctum')->id()],
                [
                    'session_id' => session()->getId(),
                    'country_id' => request('country_id'),
                    'governorate_id' => request('governorate_id'),
                ]
            );
        }

        // Use consistent session ID for guest
        $sessionId = request()->header('x-session-id') ?? session()->getId();

        return Cart::firstOrCreate(
            ['session_id' => $sessionId],
            [
                'user_id' => null,
                'country_id' => request('country_id'),
                'governorate_id' => request('governorate_id'),
            ]
        );
    }

    /**
     * Calculate shipping cost (placeholder - implement your own logic)
     */
    private function calculateShippingCost(Cart $cart)
    {
        // Implement your shipping calculation logic here
        return 0;
    }

    /**
     * Apply coupon directly to a product
     */
    public function applyToProduct(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'product_id' => 'required|exists:products,id',
        ]);

        $coupon = ProductCoupon::where('code', $request->code)
            ->where('product_id', $request->product_id)
            ->where('is_active', true)
            ->first();

        if (!$coupon) {
            return response()->json(['message' => 'Invalid coupon code for this product'], 404);
        }

        // Validate coupon
        $now = now();
        if ($coupon->starts_at && $now->lt($coupon->starts_at)) {
            return response()->json(['message' => 'This coupon is not valid yet'], 422);
        }
        if ($coupon->ends_at && $now->gt($coupon->ends_at)) {
            return response()->json(['message' => 'This coupon has expired'], 422);
        }
        if ($coupon->usage_limit && $coupon->used_count >= $coupon->usage_limit) {
            return response()->json(['message' => 'This coupon has reached its usage limit'], 422);
        }

        // Return the discounted price without modifying cart
        return response()->json([
            'message' => 'Coupon applied successfully',
            'original_price' => $coupon->original_price,
            'discounted_price' => $coupon->discounted_price,
            'valid_until' => $coupon->ends_at,
        ]);
    }

    /**
     * Apply product coupon to cart item
     */
    public function applyProductCouponToCart(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'cart_item_id' => 'required|exists:cart_items,id',
        ]);

        $cartItem = CartItem::findOrFail($request->cart_item_id);

        $coupon = ProductCoupon::where('code', $request->code)
            ->where('product_id', $cartItem->product_id)
            ->where('is_active', true)
            ->first();

        // ... same validation as above ...

        // Apply to cart item
        DB::transaction(function () use ($cartItem, $coupon) {
            $cartItem->price_per_unit = $coupon->discounted_price;
            $cartItem->subtotal = $coupon->discounted_price * $cartItem->quantity;
            $cartItem->save();

            $coupon->increment('used_count');

            // Update cart totals
            $cart = $cartItem->cart;
            $cart->subtotal = $cart->items()->sum('subtotal');
            $cart->total = $cart->subtotal + ($cart->shipping_cost ?? 0);
            $cart->save();
            // In applyProductCouponToCart method, update the cart item:
            $cartItem->update([
                'price_per_unit' => $coupon->discounted_price,
                'subtotal' => $coupon->discounted_price * $cartItem->quantity,
                'coupon_id' => $coupon->id, // Track which coupon was applied
                'original_price' => $cartItem->price_per_unit // Store original price
            ]);
        });

        return response()->json([
            'message' => 'Product coupon applied to cart item',
            'cart' => $cartItem->cart->fresh()->load('items'),
        ]);
    }
}
