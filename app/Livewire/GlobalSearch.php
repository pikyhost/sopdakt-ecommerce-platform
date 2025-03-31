<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;
use App\Models\Category;
use App\Models\Label;

class GlobalSearch extends Component
{
    public $query = '';
    public $results = [];
    public $showMore = false;

    public function updatedQuery()
    {
        $this->search();
    }

    public function search()
    {
        if (strlen($this->query) < 2) {
            $this->results = [];
            return;
        }

        $searchTerm = '%' . $this->query . '%';

        // Search in Products
        $products = Product::where('name', 'like', $searchTerm)
            ->orWhere('summary', 'like', $searchTerm)
            ->orWhere('description', 'like', $searchTerm)
            ->with('labels')
            ->get();

        // Search in Categories
        $categories = Category::where('name', 'like', $searchTerm)->get();

        // Search in Labels and get related Products
        $labelIds = Label::where('title', 'like', $searchTerm)->pluck('id');
        $productsFromLabels = Product::whereHas('labels', function ($query) use ($labelIds) {
            $query->whereIn('labels.id', $labelIds);
        })->get();

        // Merge products and remove duplicates
        $allProducts = $products->merge($productsFromLabels)->unique('id');

        // Limit results to 6 and show more if needed
        $this->showMore = $allProducts->count() > 6 || $categories->count() > 6;
        $this->results = [
            'Products' => $allProducts->take(6),
            'Categories' => $categories->take(6),
        ];
    }

    public function render()
    {
        return view('livewire.global-search');
    }
}
