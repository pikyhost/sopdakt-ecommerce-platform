<?php

namespace App\Livewire;

use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Illuminate\Support\Collection;

class ProductComparisonPage extends Component
{
    public Collection $products;

    public function mount($ids = null)
    {
        $productIds = explode(',', $ids ?? '');
        $this->products = Product::whereIn('id', $productIds)
            ->withAvg('ratings', 'rating')
            ->leftJoinSub(
                DB::table('product_ratings')
                    ->select('product_id', DB::raw('AVG(rating) as avg_rating'))
                    ->groupBy('product_id'),
                'ratings',
                'products.id',
                '=',
                'ratings.product_id'
            )
            ->select([
                'products.*',
                DB::raw('COALESCE(products.fake_average_rating, ratings.avg_rating) as final_rating'),
            ])
            ->get();
    }

    public function render()
    {
        return view('livewire.product-comparison-page', [
            'products' => $this->products
        ]); // Ensure correct layout
    }
}
