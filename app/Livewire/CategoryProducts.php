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
    public $slug;
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
        'sortBy' => ['except' => ['latest']],
    ];

    public function mount(Category $category)
    {
        $this->category = $category;
        $this->colors = Color::pluck('name', 'id');
        $this->sizes = Size::pluck('name', 'id');
    }

    public function resetPriceFilter()
    {
        $this->minPrice = null;
        $this->maxPrice = null;
        $this->resetPage();
    }

    public function updatedSortBy()
    {
        $this->resetPage(); // Reset pagination when sorting changes
    }

    public function render()
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

        // Sorting Logic
        switch ($this->sortBy) {
            case 'popularity':
                $query->orderByRaw('COALESCE(views, 0) DESC');
                break;
            case 'rating':
                $query->orderByRaw('COALESCE(fake_average_rating, ratings_avg_rating, 0) DESC');
                break;
            case 'date':
                $query->orderBy('created_at', 'desc');
                break;
            case 'price_asc':
                $query->orderByRaw('COALESCE(after_discount_price, price, 0) ASC');
                break;
            case 'price_desc':
                $query->orderByRaw('COALESCE(after_discount_price, price, 0) DESC');
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }

        // Fetch paginated products
        $products = $query->paginate(20)->through(function ($p) {
            $p->final_average_rating = $p->fake_average_rating ?? $p->ratings_avg_rating;
            return $p;
        });

        return view('livewire.category-products', compact('products'));
    }
}
