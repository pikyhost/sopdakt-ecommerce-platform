<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductActions extends Component
{
    public $product;
    public $isLoved;

    public function mount($product)
    {
        $this->product = $product;
        $this->loadLoveStatus();
    }

    public function loadLoveStatus()
    {
        $this->isLoved = Auth::check() && DB::table('saved_products')
                ->where('user_id', Auth::id())
                ->where('product_id', $this->product->id)
                ->exists();
    }

    public function toggleLove()
    {
        if (!Auth::check()) {
            return redirect()->to('/client/login');
        }

        $exists = DB::table('saved_products')
            ->where('user_id', Auth::id())
            ->where('product_id', $this->product->id)
            ->exists();

        if ($exists) {
            DB::table('saved_products')
                ->where('user_id', Auth::id())
                ->where('product_id', $this->product->id)
                ->delete();
        } else {
            DB::table('saved_products')->insert([
                'user_id' => Auth::id(),
                'product_id' => $this->product->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Refresh love status after toggling
        $this->loadLoveStatus();
    }

    public function render()
    {
        return view('livewire.product-actions');
    }
}
