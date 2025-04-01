<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Category;
use App\Models\Product;

class FilteredProducts extends Component
{
    public $selectedCategorySlug = 'all';
    public $categories;
    public $products = [];

    public function mount()
    {
        // Fetch categories with slug instead of ID
        $this->categories = Category::whereNull('parent_id')->pluck('name', 'slug');

        // Load initial products
        $this->loadProducts();
    }

    public function filterByCategory($categorySlug)
    {
        $this->selectedCategorySlug = $categorySlug === 'all' ? 'all' : Category::where('slug', $categorySlug)->value('slug') ?? 'all';
        $this->loadProducts();
    }

    private function loadProducts()
    {
        $this->products = Product::when($this->selectedCategorySlug !== 'all', function ($query) {
            $query->whereHas('category', function ($q) {
                $q->where('slug', $this->selectedCategorySlug);
            });
        })
            ->latest()
            ->take(10)
            ->get();
    }

    public function render()
    {
        return view('livewire.filtered-products', [
            'products' => $this->products,
            'selectedCategorySlug' => $this->selectedCategorySlug,
        ]);
    }
}
