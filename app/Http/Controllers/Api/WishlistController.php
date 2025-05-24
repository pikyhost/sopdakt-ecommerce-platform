<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SavedProduct;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class WishlistController extends Controller
{
    /**
     * Return user ID or session ID
     */
    protected function getSessionOrUserIdentifier(Request $request)
    {
        // For guest users using x-session-id
        if ($request->header('x-session-id')) {
            return ['userId' => null, 'sessionId' => $request->header('x-session-id')];
        }

        // For authenticated users
        $user = auth('sanctum')->user();
        if ($user) {
            return ['userId' => $user->id, 'sessionId' => null];
        }

        throw new HttpException(403, 'Invalid session handling - x-session-id header required');
    }

    /**
     * Add or remove product from wishlist.
     */
    public function toggle(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        ['userId' => $userId, 'sessionId' => $sessionId] = $this->getSessionOrUserIdentifier($request);

        $query = SavedProduct::where('product_id', $request->product_id)
            ->when($userId, fn($q) => $q->where('user_id', $userId))
            ->when(!$userId, fn($q) => $q->where('session_id', $sessionId));

        $savedProduct = $query->first();

        if ($savedProduct) {
            $savedProduct->delete();

            return response()->json([
                'status' => 'removed',
                'message' => 'Product removed from wishlist.',
            ]);
        } else {
            SavedProduct::create([
                'product_id' => $request->product_id,
                'user_id' => $userId,
                'session_id' => $userId ? null : $sessionId,
            ]);

            return response()->json([
                'status' => 'added',
                'message' => 'Product added to wishlist.',
            ]);
        }
    }

    /**
     * Return user's or guest's wishlist
     */
    public function index(Request $request)
    {
        ['userId' => $userId, 'sessionId' => $sessionId] = $this->getSessionOrUserIdentifier($request);

        $wishlistItems = SavedProduct::with(['product' => function ($query) {
            $query->select('id', 'name', 'price', 'after_discount_price', 'slug');
        }])
            ->where(function ($q) use ($userId, $sessionId) {
                $userId ? $q->where('user_id', $userId) : $q->where('session_id', $sessionId);
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

    /**
     * Check which product IDs are in the wishlist
     */
    public function isWishlisted(Request $request)
    {
        $productIds = explode(',', $request->query('product_ids', ''));

        if (empty($productIds)) {
            return response()->json(['wishlisted' => []]);
        }

        ['userId' => $userId, 'sessionId' => $sessionId] = $this->getSessionOrUserIdentifier($request);

        $wishlisted = SavedProduct::whereIn('product_id', $productIds)
            ->where(function ($q) use ($userId, $sessionId) {
                $userId ? $q->where('user_id', $userId) : $q->where('session_id', $sessionId);
            })
            ->pluck('product_id')
            ->toArray();

        return response()->json([
            'wishlisted' => $wishlisted,
        ]);
    }
}
