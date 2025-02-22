<?php

namespace App\Http\Controllers;

use App\Models\Product;

class ProductController extends Controller
{
    public function show($slug)
    {
        // Fetch product with all required relationships
        $product = Product::where('slug', $slug)
            ->with([
                'colorsWithImages',
                'sizes',
                'labels',
                'ratings' => function ($query) {
                    $query->with('user');
                },
                'attributes',
                'types',
                'category.parent',
                'bundles.products.media',
            ])
            ->firstOrFail();

        // Get translations of custom attributes
        $customAttributes = $product->getTranslations('custom_attributes');

        // Get category IDs
        $subcategoryId = $product->category_id;
        $parentCategoryId = $product->category->parent_id ?? null;

        // Fetch products once and filter them in PHP
        $allProducts = Product::where(function ($query) use ($subcategoryId, $parentCategoryId) {
            $query->where('category_id', $subcategoryId);
            if ($parentCategoryId) {
                $query->orWhere('category_id', $parentCategoryId);
            }
        })
            ->where('id', '!=', $product->id)
            ->with('media', 'ratings')
            ->get();

        // Extract related products (first 8 random)
        $relatedProducts = $allProducts->shuffle()->take(8);

        // Filter featured, best-selling, latest, and top-rated products from the same collection
        $featuredProducts = $allProducts->where('is_featured', true)->shuffle()->take(3);
        $bestSellingProducts = $allProducts->sortByDesc('sales')->take(3);
        $latestProducts = $allProducts->sortByDesc('created_at')->take(3);

        // Calculate top-rated products
        $topRatedProducts = $allProducts->map(function ($p) {
            $p->final_average_rating = $p->fake_average_rating ?? $p->ratings->avg('rating');
            return $p;
        })->sortByDesc('final_average_rating')->take(3);

        return view('front.product-sticky-info', compact(
            'product', 'relatedProducts', 'customAttributes', 'featuredProducts',
            'bestSellingProducts', 'latestProducts', 'topRatedProducts'
        ));
    }
}
