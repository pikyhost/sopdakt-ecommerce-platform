<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\ProductRating;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class ProductReviews extends Component
{
    public $product;
    public $reviews;
    public $rating;
    public $comment;
    public $editingReviewId;
    public $name;
    public $email;

    protected $rules = [
        'rating' => 'required|integer|min:1|max:5',
        'comment' => 'required|string|max:500',
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
    ];

    public function mount(Product $product)
    {
        $this->product = $product;
        $this->loadReviews();
    }

    public function loadReviews()
    {
        $this->reviews = ProductRating::where('product_id', $this->product->id)
            ->latest()
            ->get();
    }

    public function addReview()
    {
        $this->validate();

        ProductRating::create([
            'product_id' => $this->product->id,
            'user_id' => Auth::id(),
            'rating' => $this->rating,
            'comment' => $this->comment,
            'name' => $this->name,
            'email' => $this->email,
        ]);

        $this->reset(['rating', 'comment', 'name', 'email']);
        $this->loadReviews();
        session()->flash('message', 'Review added successfully.');
    }

    public function editReview($reviewId)
    {
        $review = ProductRating::findOrFail($reviewId);
        $this->editingReviewId = $review->id;
        $this->rating = $review->rating;
        $this->comment = $review->comment;
        $this->name = $review->name;
        $this->email = $review->email;
    }

    public function updateReview()
    {
        $this->validate();

        $review = ProductRating::findOrFail($this->editingReviewId);
        $review->update([
            'rating' => $this->rating,
            'comment' => $this->comment,
            'name' => $this->name,
            'email' => $this->email,
        ]);

        $this->reset(['editingReviewId', 'rating', 'comment', 'name', 'email']);
        $this->loadReviews();
        session()->flash('message', 'Review updated successfully.');
    }

    public function deleteReview($reviewId)
    {
        ProductRating::findOrFail($reviewId)->delete();
        $this->loadReviews();
        session()->flash('message', 'Review deleted successfully.');
    }

    public function render()
    {
        return view('livewire.product-reviews');
    }
}
