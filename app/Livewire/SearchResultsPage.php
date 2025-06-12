<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;
use App\Models\Category;
use App\Models\Label;

class SearchResultsPage extends Component
{
    public $query;
    public $products;
    public $categories;

    public function mount($query)
    {
        $this->query = $query;
        $this->performSearch();
    }

    public function performSearch()
    {
        $searchTerm = '%' . $this->query . '%';

        // Search in Products
        $this->products = Product::where('name', 'like', $searchTerm)
            ->orWhere('summary', 'like', $searchTerm)
            ->orWhere('description', 'like', $searchTerm)
            ->with('labels')
            ->get();

        // Search in Categories
        $this->categories = Category::where('name', 'like', $searchTerm)->get();

        // Search in Labels and get related Products
        $labelIds = Label::where('title', 'like', $searchTerm)->pluck('id');
        $productsFromLabels = Product::whereHas('labels', function ($query) use ($labelIds) {
            $query->whereIn('labels.id', $labelIds);
        })->get();

        // Merge products and remove duplicates
        $this->products = $this->products->merge($productsFromLabels)->unique('id');
    }

    public function render()
    {
        return view('livewire.search-results-page');
    }
}
