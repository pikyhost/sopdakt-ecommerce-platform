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

    public function removeFromCompare($productId)
    {
        $compareProducts = session()->get('compare_products', []);
        $compareProducts = array_diff($compareProducts, [$productId]);

        session()->put('compare_products', $compareProducts);
        $this->updateCompareProducts($compareProducts);
    }

    public function clearCompare()
    {
        session()->forget('compare_products');
        $this->compareProducts = collect();
        $this->dispatch('updateCompareList', []);
    }

    public function render()
    {
        return view('livewire.product-compare', [
            'compareProducts' => $this->compareProducts
        ]);
    }
}
