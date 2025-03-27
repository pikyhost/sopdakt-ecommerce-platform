<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Component;

class ProductCompare extends Component
{
    public $compareProducts = [];

    protected $listeners = ['updateCompareList' => 'updateCompareProducts'];

    public function mount()
    {
        $this->updateCompareProducts(session()->get('compare_products', []));
    }

    public function updateCompareProducts($products)
    {
        $this->compareProducts = Product::whereIn('id', $products)->get();
    }

    public function clearCompare()
    {
        session()->forget('compare_products');
        $this->compareProducts = [];
        $this->dispatch('updateCompareList', []);
    }

    public function render()
    {
        return view('livewire.product-compare');
    }
}
