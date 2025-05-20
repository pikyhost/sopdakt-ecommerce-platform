<?php

namespace App\Http\Controllers\Api;

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SavedProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WishlistController extends Controller
{
    protected function getSessionOrUserIdentifier(Request $request): array
    {
        $sessionId = $request->session()->getId();
        $userId = Auth::id();

        return [$userId, $sessionId];
    }

    public function toggle(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        [$userId, $sessionId] = $this->getSessionOrUserIdentifier($request);

        $query = DB::table('saved_products')
            ->where('product_id', $request->product_id)
            ->where(function ($q) use ($userId, $sessionId) {
                if ($userId) {
                    $q->where('user_id', $userId);
                } else {
                    $q->where('session_id', $sessionId);
                }
            });

        $exists = $query->exists();

        if ($exists) {
            $query->delete();

            return response()->json([
                'status' => 'removed',
                'message' => 'Product removed from wishlist.',
            ]);
        } else {
            DB::table('saved_products')->insert([
                'product_id' => $request->product_id,
                'user_id' => $userId,
                'session_id' => $userId ? null : $sessionId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json([
                'status' => 'added',
                'message' => 'Product added to wishlist.',
            ]);
        }
    }

    public function index(Request $request)
    {
        [$userId, $sessionId] = $this->getSessionOrUserIdentifier($request);
        $locale = app()->getLocale();

        $wishlistItems = SavedProduct::with(['product' => function ($query) use ($locale) {
            $query->select('id', 'name', 'price', 'after_discount_price', 'slug');
        }])
            ->where(function ($q) use ($userId, $sessionId) {
                if ($userId) {
                    $q->where('user_id', $userId);
                } else {
                    $q->where('session_id', $sessionId);
                }
            })
            ->latest()
            ->get()
            ->map(function ($savedProduct) {
                return [
                    'id' => $savedProduct->product->id,
                    'name' => $savedProduct->product->name,
                    'price' => $savedProduct->product->price,
                    'after_discount_price' => $savedProduct->product->after_discount_price,
                    'slug' => $savedProduct->product->slug,
                    'saved_at' => $savedProduct->created_at->toDateTimeString(),
                ];
            });

        return response()->json([
            'wishlist' => $wishlistItems,
        ]);
    }

    // Efficient batch check
    public function isWishlisted(Request $request)
    {
        $productIds = explode(',', $request->query('product_ids', ''));

        if (empty($productIds)) {
            return response()->json(['wishlisted' => []]);
        }

        [$userId, $sessionId] = $this->getSessionOrUserIdentifier($request);

        $wishlisted = DB::table('saved_products')
            ->whereIn('product_id', $productIds)
            ->where(function ($q) use ($userId, $sessionId) {
                if ($userId) {
                    $q->where('user_id', $userId);
                } else {
                    $q->where('session_id', $sessionId);
                }
            })
            ->pluck('product_id')
            ->toArray();

        return response()->json([
            'wishlisted' => $wishlisted,
        ]);
    }
}
