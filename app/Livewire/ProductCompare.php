<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Component;
use Illuminate\Support\Collection;

class ProductCompare extends Component
{
    public Collection $compareProducts;

    protected $listeners = ['updateCompareList' => 'updateCompareProducts'];

    public function mount()
    {
        $this->updateCompareProducts(session()->get('compare_products', []));
    }

    public function updateCompareProducts($products)
    {
        $this->compareProducts = collect(Product::whereIn('id', $products)->get());
    }

    public function clearCompare()
    {
        session()->forget('compare_products');
        $this->compareProducts = collect(); // Ensure it is always a collection
        $this->dispatch('updateCompareList', []);
    }

    public function render()
    {
        return view('livewire.product-compare', [
            'compareProducts' => $this->compareProducts
        ]);
    }
}
