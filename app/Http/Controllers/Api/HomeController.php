<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\HomePageSettingResource;
use App\Models\Category;
use App\Models\HomePageSetting;
use App\Models\Product;
use Illuminate\Http\JsonResponse;

class HomeController extends Controller
{
    /**
     * Get featured categories for homepage (optimized query)
     */
    public function featuredCategories(): JsonResponse
    {
        // Eager load the media relationship and only select needed columns
        $categories = Category::with(['media' => function ($query) {
            $query->where('collection_name', 'main_category_image');
        }])
            ->select('id', 'name') // Only select what we need
            ->where('is_published', true) // Only published categories
            ->whereNull('parent_id') // Only parent categories if you want
            ->orderBy('created_at', 'desc') // Or any other relevant order
            ->limit(8)
            ->get()
            ->map(function ($category) {
                return [
                    'name' => $category->name,
                    'image_url' => $category->getFirstMediaUrl('main_category_image'),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    /**
     * Get top 10 best-selling products (ordered by sales)
     */
    public function bestSellers(): JsonResponse
    {
        $products = Product::with([
            'media',
            'category',
            'productColors.color', // Eager load color relation inside productColors
            'productColors.productColorSizes.size', // Eager load sizes through productColorSizes
        ])
            ->select([
                'id',
                'name',
                'price',
                'after_discount_price',
                'sales',
                'slug',
                'category_id',
            ])
            ->where('is_published', true)
            ->orderBy('sales', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'after_discount_price' => $product->after_discount_price,
                    'sales' => $product->sales,
                    'slug' => $product->slug,
                    'image_url' => $product->getFirstMediaUrl(),
                    'category' => $product->category?->only(['name', 'slug']),
                    'colors_with_sizes' => $product->productColors->map(function ($productColor) {
                        return [
                            'color_name' => $productColor->color->name ?? null,
                            'color_code' => $productColor->color->code ?? null,
                            'color_image' => $productColor->image ? asset('storage/' . $productColor->image) : null,
                            'sizes' => $productColor->productColorSizes->map(function ($productColorSize) {
                                return [
                                    'size_name' => $productColorSize->size->name ?? null,
                                    'quantity' => $productColorSize->quantity,
                                ];
                            })->filter(fn ($size) => $size['quantity'] > 0)->values(),
                        ];
                    }),
                    'actions' => [
                        'add_to_cart' => route('cart.add', ['product_id' => $product->id]),
                        'toggle_love' => route('wishlist.toggle', ['product_id' => $product->id]),
                        'compare' => route('compare.add', ['product_id' => $product->id]),
                        'view' => route('products.show', ['slug' => $product->slug]),
                    ],
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $products,
        ]);
    }


    public function sliderWithCta()
    {
        $homePageSetting = HomePageSetting::getCached();

        if (!$homePageSetting) {
            return response()->json([
                'message' => 'Homepage settings not found.'
            ], 404);
        }

        return new HomePageSettingResource($homePageSetting);
    }
}
