<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function show($slug)
    {
        // Fetch product with necessary relationships
        $product = Product::where('slug', $slug)
            ->with([
                'colorsWithImages',
                'sizes',
                'labels',
                'media', // Eager load media here
                'ratings' => function ($query) {
                    $query->approved()->with('user');
                },
                'attributes',
                'types',
                'category.parent',
                'bundles',
            ])
            ->firstOrFail();

        // Get translations of custom attributes
        $customAttributes = $product->getTranslations('custom_attributes');

        $bundles = $product->bundles()->with('products')->get();

        // Get category IDs
        $subcategoryId = $product->category_id;
        $parentCategoryId = $product->category->parent_id ?? null;

        // Fetch related products (limit 8)
        $relatedProducts = Product::where('category_id', $subcategoryId)
            ->where('id', '!=', $product->id)
            ->inRandomOrder()
            ->limit(8)
            ->get();

        // If not enough products, fetch from the parent category
        if ($relatedProducts->count() < 8 && $parentCategoryId) {
            $additionalProducts = Product::where('category_id', $parentCategoryId)
                ->where('id', '!=', $product->id)
                ->inRandomOrder()
                ->limit(8 - $relatedProducts->count())
                ->get();

            $relatedProducts = $relatedProducts->merge($additionalProducts);
        }

        // Fetch featured, best-selling, latest, and top-rated products
        $otherProducts = Product::where(function ($query) use ($subcategoryId, $parentCategoryId) {
            $query->where('category_id', $subcategoryId);
            if ($parentCategoryId) {
                $query->orWhere('category_id', $parentCategoryId);
            }
        })
            ->where('id', '!=', $product->id)
            ->get();

        // Extract product collections
        $featuredProducts = $otherProducts->where('is_featured', true)->shuffle()->take(3);
        $bestSellingProducts = $otherProducts->sortByDesc('sales')->take(3);
        $latestProducts = $otherProducts->sortByDesc('created_at')->take(3);
        $topRatedProducts = $otherProducts
            ->loadAvg('ratings', 'rating') // Load ratings once
            ->map(function ($p) {
                $p->final_average_rating = $p->fake_average_rating ?? $p->ratings_avg_rating;
                return $p;
            })
            ->sortByDesc('final_average_rating')
            ->take(3);

        // Load media for all related products in one query
        $relatedProducts->load('media');
        $featuredProducts->load('media');
        $bestSellingProducts->load('media');
        $latestProducts->load('media');
        $topRatedProducts->load('media');

        return view('front.product-sticky-info',
            compact('product', 'relatedProducts', 'bundles', 'customAttributes',
                'featuredProducts', 'bestSellingProducts', 'latestProducts', 'topRatedProducts'));
    }


}
