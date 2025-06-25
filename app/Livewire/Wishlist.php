<?php

namespace App\Livewire;

use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Wishlist extends Component
{
    public $wishlist = [];

    public function mount()
    {
        $this->fetchWishlist();
    }

    protected function fetchWishlist()
    {
        $this->wishlist = Product::query()
            ->select('products.*', 'saved_products.user_id', 'saved_products.created_at as saved_at')
            ->join('saved_products', 'products.id', '=', 'saved_products.product_id')
            ->where('saved_products.user_id', Auth::id())
            ->with(['category'])
            ->orderByDesc('saved_at')
            ->get()
            ->map(function ($product) {
                $product->stock_status = $product->quantity > 0 ? 'available' : 'out_of_stock';
                return $product;
            });
    }

    public function removeFromWishlist($productId)
    {
        Auth::user()->savedProducts()->detach($productId);
        $this->fetchWishlist();
    }

    public function render()
    {
        return view('livewire.wishlist');
    }
}
