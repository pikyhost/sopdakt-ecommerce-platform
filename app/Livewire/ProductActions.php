<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductActions extends Component
{
    public $product;
    public bool $isLoved = false;

    public function mount($product)
    {
        $this->product = $product;

        if (Auth::check()) {
            $this->isLoved = DB::table('saved_products')
                ->where('user_id', Auth::id())
                ->where('product_id', $this->product->id)
                ->exists();
        }
    }

    public function toggleLove()
    {
        if (!Auth::check()) {
            return redirect()->route('login'); // Redirect if not logged in
        }

        $exists = DB::table('saved_products')
            ->where('user_id', Auth::id())
            ->where('product_id', $this->product->id)
            ->exists();

        if ($exists) {
            // Remove from wishlist
            DB::table('saved_products')
                ->where('user_id', Auth::id())
                ->where('product_id', $this->product->id)
                ->delete();

            $this->isLoved = false;
        } else {
            // Add to wishlist
            DB::table('saved_products')->insert([
                'user_id' => Auth::id(),
                'product_id' => $this->product->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $this->isLoved = true;
        }
    }

    public function render()
    {
        return view('livewire.product-actions');
    }
}
