<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WishlistController extends Controller
{
    /**
     * Toggle wishlist (add/remove product)
     */
    public function toggle(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $userId = Auth::id();
        $productId = $request->product_id;

        $exists = DB::table('saved_products')
            ->where('user_id', $userId)
            ->where('product_id', $productId)
            ->exists();

        if ($exists) {
            // Remove from wishlist
            DB::table('saved_products')
                ->where('user_id', $userId)
                ->where('product_id', $productId)
                ->delete();

            return response()->json([
                'status' => 'removed',
                'message' => 'Product removed from wishlist.',
            ]);
        } else {
            // Add to wishlist
            DB::table('saved_products')->insert([
                'user_id' => $userId,
                'product_id' => $productId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json([
                'status' => 'added',
                'message' => 'Product added to wishlist.',
            ]);
        }
    }

    /**
     * Get the wishlist for the authenticated user
     */
    public function index()
    {
        $wishlist = DB::table('saved_products')
            ->join('products', 'products.id', '=', 'saved_products.product_id')
            ->where('saved_products.user_id', Auth::id())
            ->select('products.*', 'saved_products.created_at as saved_at')
            ->orderBy('saved_products.created_at', 'desc')
            ->get();

        return response()->json([
            'wishlist' => $wishlist,
        ]);
    }

    /**
     * Check if a product is wishlisted
     */
    public function isWishlisted($productId)
    {
        $isWishlisted = DB::table('saved_products')
            ->where('user_id', Auth::id())
            ->where('product_id', $productId)
            ->exists();

        return response()->json([
            'isWishlisted' => $isWishlisted,
        ]);
    }
}
