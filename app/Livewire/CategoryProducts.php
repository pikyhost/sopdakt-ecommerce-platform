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
    public $sortBy = 'latest';
    public $minPrice;
    public $maxPrice;
    public int $perPage = 1;

    protected $queryString = [
        'selectedColors' => ['except' => []],
        'selectedSizes' => ['except' => []],
        'sortBy' => ['except' => 'latest'],
        'minPrice' => ['except' => null],
        'maxPrice' => ['except' => null],
        'perPage' => ['except' => 1],
    ];

    public function mount($slug)
    {
        $this->slug = $slug;
        $this->category = Category::where('slug', $slug)->firstOrFail();
        $this->colors = Color::pluck('name', 'id');
        $this->sizes = Size::pluck('name', 'id');
    }

    public function updating($property)
    {
        $this->resetPage();
    }

    public function resetPriceFilter()
    {
        $this->minPrice = null;
        $this->maxPrice = null;
        $this->resetPage();
    }

    public function render()
    {
        $query = $this->category->products()
            ->with('media')
            ->where('is_published', 1)
            ->with(['colorsWithImages'])
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

        if (!is_null($this->minPrice)) {
            $query->whereRaw('(CASE WHEN after_discount_price IS NOT NULL THEN after_discount_price ELSE price END) >= ?', [$this->minPrice]);
        }

        if (!is_null($this->maxPrice)) {
            $query->whereRaw('(CASE WHEN after_discount_price IS NOT NULL THEN after_discount_price ELSE price END) <= ?', [$this->maxPrice]);
        }

        // Sorting Logic
        switch ($this->sortBy) {
            case 'popularity':
                $query->orderBy('views', 'desc');
                break;
            case 'rating':
                $query->orderByRaw('COALESCE(fake_average_rating, ratings_avg_rating, 0) DESC');
                break;
            case 'date':
                $query->orderBy('created_at', 'desc');
                break;
            case 'price_asc':
                $query->orderByRaw('CASE WHEN after_discount_price IS NOT NULL THEN after_discount_price ELSE price END ASC');
                break;
            case 'price_desc':
                $query->orderByRaw('CASE WHEN after_discount_price IS NOT NULL THEN after_discount_price ELSE price END DESC');
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }

        // Fetch paginated products
        $products = $query->paginate($this->perPage)->through(function ($p) {
            $p->final_average_rating = $p->fake_average_rating ?? $p->ratings_avg_rating;
            return $p;
        });

        return view('livewire.category-products', compact('products'));
    }

}
