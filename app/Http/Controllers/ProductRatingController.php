<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductRating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductRatingController extends Controller
{
    public function index(Product $product)
    {
        $user = Auth::user();
        $isAdmin = $user->hasRole(['admin', 'super_admin']);

        $reviews = ProductRating::where('product_id', $product->id)
            ->where(function ($query) use ($user, $isAdmin) {
                $query->where('status', 'approved');

                if ($user) {
                    $query->orWhere(function ($q) use ($user) {
                        $q->where('status', 'pending')->where('user_id', $user->id);
                    });
                }

                if ($isAdmin) {
                    $query->orWhereNotNull('id');
                }
            })
            ->latest()
            ->paginate(5);

        return response()->json($reviews);
    }

    public function store(Request $request, Product $product)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:1',
        ]);

        $user = Auth::user();
        $isAdmin = $user->hasRole(['admin', 'super_admin']);
        $status = $isAdmin ? 'approved' : 'pending';

        $review = ProductRating::updateOrCreate(
            ['product_id' => $product->id, 'user_id' => $user->id],
            array_merge($validated, ['status' => $status])
        );

        return response()->json([
            'message' => $isAdmin ? 'Review saved.' : 'Review submitted and pending approval.',
            'review' => $review
        ]);
    }

    public function update(Request $request, Product $product, ProductRating $rating)
    {
        $this->authorizeReviewOwner($rating);

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:1',
        ]);

        $isAdmin = Auth::user()->hasRole(['admin', 'super_admin']);
        $status = $isAdmin ? 'approved' : 'pending';

        $rating->update(array_merge($validated, ['status' => $status]));

        return response()->json(['message' => 'Review updated.', 'review' => $rating]);
    }

    public function destroy(Product $product, ProductRating $rating)
    {
        $this->authorizeReviewOwner($rating);

        $rating->delete();

        return response()->json(['message' => 'Review deleted.']);
    }

    private function authorizeReviewOwner(ProductRating $rating)
    {
        $user = Auth::user();

        if ($rating->user_id !== $user->id && !$user->hasRole(['admin', 'super_admin'])) {
            abort(403, 'Unauthorized');
        }
    }
}
