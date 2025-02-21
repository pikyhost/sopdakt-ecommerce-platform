<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Color;
use App\Models\Size;
use Livewire\Component;
use Livewire\WithPagination;

class CategoryProducts extends Component
{
    use WithPagination;

    public $category;
    public $colors;
    public $sizes;
    public $selectedColors = [];
    public $selectedSizes = [];
    public $minPrice;
    public $maxPrice;
    public string $sortBy = 'latest';

    protected $queryString = [
        'selectedColors' => ['except' => []],
        'selectedSizes' => ['except' => []],
        'minPrice' => ['except' => null],
        'maxPrice' => ['except' => null],
        'sortBy' => ['except' => 'latest'],
    ];

    public function mount(Category $category)
    {
        $this->category = $category;
        $this->colors = Color::pluck('name', 'id');
        $this->sizes = Size::pluck('name', 'id');
    }

    public function filterProducts()
    {
        $this->resetPage(); // Reset pagination when filters change
    }

    public function updated($property)
    {
        if (in_array($property, ['selectedColors', 'selectedSizes', 'minPrice', 'maxPrice', 'sortBy'])) {
            $this->filterProducts();
        }
    }

    public function getProductsQuery()
    {
        $query = $this->category->products()
            ->with(['media', 'colorsWithImages'])
            ->where('is_published', 1)
            ->withAvg('ratings', 'rating');

        // Filter by selected colors
        if (!empty($this->selectedColors)) {
            $query->whereHas('colorsWithImages', function ($q) {
                $q->whereIn('color_id', $this->selectedColors);
            });
        }

        // Filter by selected sizes
        if (!empty($this->selectedSizes)) {
            $query->whereHas('sizes', function ($q) {
                $q->whereIn('sizes.id', $this->selectedSizes);
            });
        }

        // Price filtering
        if (!is_null($this->minPrice)) {
            $query->whereRaw('(CASE WHEN after_discount_price IS NOT NULL THEN after_discount_price ELSE price END) >= ?', [$this->minPrice]);
        }

        if (!is_null($this->maxPrice)) {
            $query->whereRaw('(CASE WHEN after_discount_price IS NOT NULL THEN after_discount_price ELSE price END) <= ?', [$this->maxPrice]);
        }

        // Sorting logic
        $sortOptions = [
            'popularity' => 'COALESCE(views, 0) DESC',
            'rating' => 'COALESCE(fake_average_rating, ratings_avg_rating, 0) DESC',
            'date' => 'created_at DESC',
            'price_asc' => 'COALESCE(after_discount_price, price, 0) ASC',
            'price_desc' => 'COALESCE(after_discount_price, price, 0) DESC',
            'latest' => 'created_at DESC',
        ];

        if (isset($sortOptions[$this->sortBy])) {
            $query->orderByRaw($sortOptions[$this->sortBy]);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        return $query;
    }

    public function render()
    {
        $products = $this->getProductsQuery()
            ->paginate(20)
            ->through(function ($p) {
                $p->final_average_rating = $p->fake_average_rating ?? $p->ratings_avg_rating;
                return $p;
            });

        return view('livewire.category-products', compact('products'));
    }
}
