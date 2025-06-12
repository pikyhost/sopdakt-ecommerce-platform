<?php

namespace App\Livewire;

use Livewire\Component;

class AddToCompare extends Component
{
    public $product;
    public $isCompared = false;
    public $compareProducts = [];

    protected $listeners = ['updateCompareList' => 'updateCompareProducts'];

    public function mount($product)
    {
        $this->compareProducts = session()->get('compare_products', []);
        $this->isCompared = in_array($this->product->id, $this->compareProducts);
    }

    public function toggleCompare()
    {
        $compareProducts = session()->get('compare_products', []);

        if (in_array($this->product->id, $compareProducts)) {
            $compareProducts = array_diff($compareProducts, [$this->product->id]);
            $this->isCompared = false;
        } else {
            if (count($compareProducts) < 3) { // Limit to 3 products
                $compareProducts[] = $this->product->id;
                $this->isCompared = true;
            } else {
                session()->flash('error', 'You can only compare up to 3 products.');
            }
        }

        session()->put('compare_products', array_values($compareProducts));
        $this->dispatch('updateCompareList', $compareProducts);
    }

    public function updateCompareProducts($products)
    {
        $this->compareProducts = $products;
        $this->isCompared = in_array($this->product->id, $this->compareProducts);
    }

    public function render()
    {
        return view('livewire.add-to-compare');
    }
}
