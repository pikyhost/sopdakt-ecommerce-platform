<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function show($slug)
    {
        // Fetch the product with all required relationships in a single query
        $product = Product::where('slug', $slug)
            ->with([
                'colorsWithImages',
                'sizes:id,name',
                'labels',
                'ratings',
                'attributes',
                'types',
                'category:id,parent_id,slug,name',  // Load only necessary fields
                'category.parent:id,name', // Ensure parent is loaded with minimal fields
                'bundles.products.media',
            ])
            ->withAvg('ratings', 'rating')
            ->firstOrFail();  //this make query

        // Use eager-loaded category data to avoid additional queries
        $category = $product->category; // Access the eager-loaded category
        $subcategoryId = $category?->id;
        $parentCategoryId = $category?->parent?->id; // Access the eager-loaded parent

        // Fetch related products efficiently
        $relatedProducts = Product::whereIn('category_id', array_filter([$subcategoryId, $parentCategoryId]))
            ->where('id', '!=', $product->id)
            ->with('media')
            ->select([
                'id', 'slug', 'name', 'price', 'after_discount_price',
                'created_at', 'is_featured', 'category_id'
            ])
            ->inRandomOrder()
            ->limit(8)
            ->get();  //this make query

        // Fetch products with ratings in a single optimized query
        $products = Product::whereIn('category_id', array_filter([$subcategoryId, $parentCategoryId]))
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
                'products.id',
                'products.slug',
                'products.name',
                'products.price',
                'products.sales',
                'products.created_at',
                'products.is_featured',
                DB::raw('COALESCE(products.fake_average_rating, ratings.avg_rating) as final_rating'),
            ])
            ->with('media')
            ->get();

        // Categorizing products efficiently
        $featuredProducts = $products->where('is_featured', true)->take(3);
        $bestSellingProducts = $products->sortByDesc('sales')->take(3);
        $latestProducts = $products->sortByDesc('created_at')->take(3);
        $topRatedProducts = $products->sortByDesc('final_rating')->take(3);

        return view('front.product-sticky-info', compact(
            'product', 'relatedProducts', 'featuredProducts',
            'bestSellingProducts', 'latestProducts', 'topRatedProducts'
        ));
    }
}
