<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class WishlistButton extends Component
{
    public $productId;
    public $isWishlisted = false;

    public function mount($productId)
    {
        $this->productId = $productId;
        $this->isWishlisted = Auth::check() && DB::table('saved_products')
                ->where('user_id', Auth::id())
                ->where('product_id', $this->productId)
                ->exists();
    }

    public function toggleWishlist()
    {
        if (!Auth::check()) {
            session()->flash('error', 'You need to be logged in to modify the wishlist.');
            return;
        }

        $userId = Auth::id();

        if ($this->isWishlisted) {
            // Remove from wishlist
            DB::table('saved_products')
                ->where('user_id', $userId)
                ->where('product_id', $this->productId)
                ->delete();
            $this->isWishlisted = false;
            session()->flash('success', 'Product removed from wishlist.');
        } else {
            // Add to wishlist
            DB::table('saved_products')->insert([
                'user_id' => $userId,
                'product_id' => $this->productId,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            $this->isWishlisted = true;
            session()->flash('success', 'Product added to wishlist.');
        }
    }

    public function render()
    {
        return view('livewire.wishlist-button');
    }
}
