<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function show($slug)
    {
        // Find product and load necessary relationships
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
                'bundles',
            ])
            ->firstOrFail();

        // Get translations of custom attributes (Fix TypeError)
        $customAttributes = $product->getTranslations('custom_attributes');

        $bundles = $product->bundles()->with('products')->get();

        // Get category IDs
        $subcategoryId = $product->category_id;
        $parentCategoryId = $product->category->parent_id ?? null;

        // Fetch 8 related products from the same subcategory (excluding the current product)
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

        // Fetch 3 featured products from the same subcategory or parent category
        $featuredProducts = Product::where('is_featured', true)
            ->where(function ($query) use ($subcategoryId, $parentCategoryId) {
                $query->where('category_id', $subcategoryId);
                if ($parentCategoryId) {
                    $query->orWhere('category_id', $parentCategoryId);
                }
            })
            ->where('id', '!=', $product->id)
            ->inRandomOrder()
            ->limit(3)
            ->get();

        // Fetch 3 best-selling products from the same subcategory or parent category
        $bestSellingProducts = Product::where(function ($query) use ($subcategoryId, $parentCategoryId) {
            $query->where('category_id', $subcategoryId);
            if ($parentCategoryId) {
                $query->orWhere('category_id', $parentCategoryId);
            }
        })
            ->where('id', '!=', $product->id)
            ->orderByDesc('sales') // Order by most sales
            ->limit(3)
            ->get();

        // Fetch 3 latest products from the same subcategory or parent category
        $latestProducts = Product::where(function ($query) use ($subcategoryId, $parentCategoryId) {
            $query->where('category_id', $subcategoryId);
            if ($parentCategoryId) {
                $query->orWhere('category_id', $parentCategoryId);
            }
        })
            ->where('id', '!=', $product->id)
            ->latest() // Order by latest created_at
            ->limit(3)
            ->get();

        // Fetch top 3 rated products from the same subcategory or parent category
        $topRatedProducts = Product::where(function ($query) use ($subcategoryId, $parentCategoryId) {
            $query->where('category_id', $subcategoryId);
            if ($parentCategoryId) {
                $query->orWhere('category_id', $parentCategoryId);
            }
        })
            ->where('id', '!=', $product->id)
            ->withAvg('ratings', 'rating') // Calculate average rating
            ->get()
            ->map(function ($p) {
                $p->final_average_rating = $p->fake_average_rating ?? $p->ratings_avg_rating;
                return $p;
            })
            ->sortByDesc('final_average_rating')
            ->take(3);


        return view('front.product-sticky-info',
            compact('product', 'relatedProducts', 'bundles', 'customAttributes',
                'featuredProducts', 'bestSellingProducts', 'latestProducts', 'topRatedProducts'));
    }

}
