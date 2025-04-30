<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WishlistController extends Controller
{
    /**
     * Toggle Product in Wishlist
     *
     * @group Wishlist
     * @authenticated
     *
     * Adds or removes a product from the authenticated user's wishlist.
     *
     * @bodyParam product_id integer required The ID of the product to toggle. Example: 42
     *
     * @response 200 {
     *   "status": "added",
     *   "message": "Product added to wishlist."
     * }
     * @response 200 {
     *   "status": "removed",
     *   "message": "Product removed from wishlist."
     * }
     * @response 401 {
     *   "message": "Unauthorized"
     * }
     * @response 422 {
     *   "message": "The given data was invalid.",
     *   "errors": {
     *     "product_id": [
     *       "The selected product id is invalid."
     *     ]
     *   }
     * }
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
     * Get User Wishlist
     *
     * @group Wishlist
     * @authenticated
     *
     * Retrieves all products in the authenticated user's wishlist,
     * ordered by most recently added first.
     *
     * @response 200 {
     *   "wishlist": [
     *     {
     *       "id": 1,
     *       "name": "Premium Headphones",
     *       "price": 199.99,
     *       "after_discount_price": 99.99,
     *       "slug": "premium-headphones",
     *       "saved_at": "2023-05-15 10:30:00"
     *     },
     *     {
     *       "id": 2,
     *       "name": "Wireless Mouse",
     *       "price": 29.99,
     *       "after_discount_price": 20.99,
     *       "slug": "wireless-mouse",
     *       "saved_at": "2023-05-10 14:15:00"
     *     }
     *   ]
     * }
     * @response 401 {
     *   "message": "Unauthorized"
     * }
     */
    public function index()
    {
        $locale = App::getLocale();

        $wishlist = DB::table('saved_products')
            ->join('products', 'products.id', '=', 'saved_products.product_id')
            ->where('saved_products.user_id', Auth::id())
            ->select([
                'products.id',
                DB::raw("JSON_UNQUOTE(JSON_EXTRACT(products.name, '$.\"$locale\"')) as name"),
                'products.price',
                'products.after_discount_price',
                'products.slug',
                'saved_products.created_at as saved_at',
            ])
            ->orderBy('saved_products.created_at', 'desc')
            ->get();

        return response()->json([
            'wishlist' => $wishlist,
        ]);
    }

    /**
     * Check if Product is in Wishlist
     *
     * @group Wishlist
     * @authenticated
     *
     * Checks whether a specific product exists in the authenticated user's wishlist.
     *
     * @urlParam productId integer required The ID of the product to check. Example: 42
     *
     * @response 200 {
     *   "isWishlisted": true
     * }
     * @response 200 {
     *   "isWishlisted": false
     * }
     * @response 401 {
     *   "message": "Unauthorized"
     * }
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
