<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Color;
use App\Models\Size;
use Livewire\Component;
use Livewire\WithPagination;

class CategoryProductsList extends Component
{
    use WithPagination;

    public $category;
    public $sortBy = 'latest';
    public $selectedSizes = [];
    public $selectedColors = [];
    public $minPrice;
    public $maxPrice;
    public $colors;
    public $sizes;
    public int $perPage = 1;

    protected $queryString = ['sortBy', 'selectedSizes', 'selectedColors', 'minPrice', 'maxPrice', 'perPage'];

    public function mount(Category $category)
    {
        $this->category = $category;
        $this->colors = Color::pluck('name', 'id');
        $this->sizes = Size::pluck('name', 'id');
    }

    public function updatedSortBy()
    {
        $this->resetPage();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function updatedSelectedSizes()
    {
        $this->resetPage();
    }

    public function updatedSelectedColors()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset(['selectedSizes', 'selectedColors', 'minPrice', 'maxPrice', 'sortBy']);
    }

    public function getProductsQuery()
    {
        $query = $this->category->products()
            ->availableInUserCountry() // Apply local scope
            ->with(['media', 'productColors'])
            ->where('is_published', 1)
            ->withAvg('ratings', 'rating');

        if (!empty($this->selectedColors)) {
            $query->whereHas('colorsWithImages', function ($q) {
                $q->whereIn('color_id', $this->selectedColors);
            });
        }

        if (!empty($this->selectedSizes)) {
            $query->whereHas('sizes', function ($q) {
                $q->whereIn('sizes.id', $this->selectedSizes);
            });
        }

        if ($this->minPrice) {
            $query->where('price', '>=', $this->minPrice);
        }

        if ($this->maxPrice) {
            $query->where('price', '<=', $this->maxPrice);
        }

        $sortOptions = [
            'popularity' => 'views DESC',
            'rating' => 'COALESCE(fake_average_rating, ratings_avg_rating, 0) DESC',
            'date' => 'created_at DESC',
            'price_asc' => 'COALESCE(after_discount_price, price, 0) ASC',
            'price_desc' => 'COALESCE(after_discount_price, price, 0) DESC',
            'latest' => 'created_at DESC',
        ];

        if (array_key_exists($this->sortBy, $sortOptions)) {
            $query->orderByRaw($sortOptions[$this->sortBy]);
        }

        return $query;
    }

    public function render()
    {
        return view('livewire.category-products-list', [
            'products' => $this->getProductsQuery()->paginate($this->perPage),
        ]);
    }
}
