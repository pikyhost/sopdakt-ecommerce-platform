<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function show($slug)
    {
        // Find product by slug and load relationships
        $product = Product::where('slug', $slug)->with('filamentComments')->firstOrFail();

        // Return view with product data
        return view('front.product-sticky-info', compact('product'));
    }

    public function toggleWishlist(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);

        if (!Auth::check()) {
            return response()->json(['message' => 'You need to be logged in to modify the wishlist'], 401);
        }

        $userId = Auth::id();
        $productId = $request->product_id;

        // Check if the product is already in the wishlist
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

            return response()->json(['status' => 'removed', 'message' => 'Product removed from wishlist']);
        } else {
            // Add to wishlist
            DB::table('saved_products')->insert([
                'user_id' => $userId,
                'product_id' => $productId,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return response()->json(['status' => 'added', 'message' => 'Product added to wishlist']);
        }
    }

    // Function to check if the product is in the user's wishlist
    public function checkWishlist($productId)
    {
        $isWishlisted = Auth::check() && DB::table('saved_products')
                ->where('user_id', Auth::id())
                ->where('product_id', $productId)
                ->exists();

        return response()->json(['isWishlisted' => $isWishlisted]);
    }
}
