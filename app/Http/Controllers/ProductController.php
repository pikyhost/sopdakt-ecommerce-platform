<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

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
                'media',
                'ratings' => function ($query) {
                    $query->approved()->with('user');
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

        // Fetch related products in one optimized query
        $relatedProducts = Product::where('category_id', $subcategoryId)
            ->where('id', '!=', $product->id)
            ->with('media')
            ->inRandomOrder()
            ->limit(8)
            ->get();

        // If not enough products, fetch additional from parent category
        if ($relatedProducts->count() < 8 && $parentCategoryId) {
            $additionalProducts = Product::where('category_id', $parentCategoryId)
                ->where('id', '!=', $product->id)
                ->with('media')
                ->inRandomOrder()
                ->limit(8 - $relatedProducts->count())
                ->get();

            $relatedProducts = $relatedProducts->merge($additionalProducts);
        }

        // Fetch featured, best-selling, latest, and top-rated products in one query
        $otherProducts = Product::where(function ($query) use ($subcategoryId, $parentCategoryId) {
            $query->where('category_id', $subcategoryId);
            if ($parentCategoryId) {
                $query->orWhere('category_id', $parentCategoryId);
            }
        })
            ->where('id', '!=', $product->id)
            ->with('media', 'ratings')
            ->get();

        // Filter the collections
        $featuredProducts = $otherProducts->where('is_featured', true)->shuffle()->take(3);
        $bestSellingProducts = $otherProducts->sortByDesc('sales')->take(3);
        $latestProducts = $otherProducts->sortByDesc('created_at')->take(3);

        // Calculate top-rated products
        $topRatedProducts = $otherProducts->map(function ($p) {
            $p->final_average_rating = $p->fake_average_rating ?? $p->ratings->avg('rating');
            return $p;
        })->sortByDesc('final_average_rating')->take(3);

        return view('front.product-sticky-info', compact(
            'product', 'relatedProducts', 'customAttributes', 'featuredProducts',
            'bestSellingProducts', 'latestProducts', 'topRatedProducts'
        ));
    }
}
