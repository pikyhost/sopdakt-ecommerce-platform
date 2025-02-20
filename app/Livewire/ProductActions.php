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
            return redirect()->to('/client/login'); // Redirect if not logged in
        }

        if ($this->isLoved) {
            DB::table('saved_products')
                ->where('user_id', Auth::id())
                ->where('product_id', $this->product->id)
                ->delete();

            $this->isLoved = false;
        } else {
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
