<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ProductRating;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class ProductReviews extends Component
{
    use WithPagination;

    public $product;
    public $rating;
    public $comment;
    public $userReview;

    protected $rules = [
        'rating' => 'required|integer|min:1|max:5',
        'comment' => 'required|string|min:1',
    ];

    public function mount(Product $product)
    {
        $this->product = $product;

        // Fetch user's existing review
        $this->loadUserReview();
    }

    private function loadUserReview()
    {
        $this->userReview = ProductRating::where('product_id', $this->product->id)
            ->where('user_id', Auth::id())
            ->first();

        if ($this->userReview) {
            $this->rating = $this->userReview->rating;
            $this->comment = $this->userReview->comment;
        }
    }

    public function addReview()
    {
        $this->validate();

        $user = Auth::user();
        $isAdmin = $user->hasRole(['admin', 'super_admin']);
        $status = $isAdmin ? 'approved' : 'pending';

        // Update or create the review
        ProductRating::updateOrCreate(
            [
                'product_id' => $this->product->id,
                'user_id' => $user->id,
            ],
            [
                'rating' => $this->rating,
                'comment' => $this->comment,
                'status' => $status,
            ]
        );

        $message = __($isAdmin ? 'messages.review_saved' : 'messages.review_pending');

        // Ensure correct notification structure
        $this->dispatch('notify', message: $message, type: 'success');

        // Reload user review without resetting fields
        $this->loadUserReview();
    }

    public function deleteReview($reviewId)
    {
        $review = ProductRating::findOrFail($reviewId);

        if ($review->user_id !== Auth::id() && !Auth::user()->hasRole(['admin', 'super_admin'])) {
            abort(403);
        }

        $review->delete();

        $this->dispatch('notify', message: __('messages.review_deleted'), type: 'success');

        // Reset user review data
        $this->userReview = null;
        $this->rating = null;
        $this->comment = null;
    }

    public function render()
    {
        $user = Auth::user();
        $isAdmin = $user && $user->hasRole(['admin', 'super_admin']);

        return view('livewire.product-reviews', [
            'reviews' => ProductRating::where('product_id', $this->product->id)
                ->where(function ($query) use ($user, $isAdmin) {
                    $query->where('status', 'approved');

                    if ($user) { // Ensure user is authenticated before checking user_id
                        $query->orWhere(function ($subQuery) use ($user) {
                            $subQuery->where('status', 'pending')->where('user_id', $user->id);
                        });
                    }

                    if ($isAdmin) {
                        $query->orWhereNotNull('id'); // Admin sees all reviews
                    }
                })
                ->latest()
                ->paginate(5)
        ]);
    }

}
