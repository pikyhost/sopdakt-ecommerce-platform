<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Show Category with Products
     *
     * @group Categories
     *
     * Display a category and its associated products with translations and details.
     *
     * @urlParam slug string required The slug of the category.
     * @queryParam locale string The locale to use for translations (default: en).
     *
     * @response 200 {
     *   "success": true,
     *   "data": {
     *     "id": 1,
     *     "name": "Electronics",
     *     "slug": "electronics",
     *     "description": "Localized description",
     *     "products": [ ... ]
     *   }
     * }
     */
    public function showWithProducts(Request $request, Category $category)
    {
        $locale = $request->get('locale', app()->getLocale());

        $category->load([
            'products' => [
                'category', 'media', 'productColors.productColorSizes.size', 'productColors.color', 'ratings'
            ]
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $category->id,
                'name' => $category->getTranslation('name', $locale),
                'slug' => $category->slug,
                'description' => $category->getTranslation('description', $locale),
                'products' => $category->products->map(function ($product) use ($locale) {
                    return [
                        'id' => $product->id,
                        'category_id' => $product->category_id,
                        'category_name' => optional($product->category)?->getTranslation('name', $locale),
                        'name' => $product->getTranslation('name', $locale),
                        'price' => $product->price,
                        'after_discount_price' => $product->after_discount_price,
                        'description' => $product->getTranslation('description', $locale),
                        'slug' => $product->slug,
                        'views' => $product->views,
                        'sales' => $product->sales,
                        'fake_average_rating' => $product->fake_average_rating,
                        'label_id' => $product->label_id,
                        'summary' => $product->getTranslation('summary', $locale),
                        'quantity' => $product->quantity,
                        'created_at' => $product->created_at,
                        'updated_at' => $product->updated_at,

                        'media' => [
                            'feature_product_image' => $product->getFeatureProductImageUrl(),
                            'second_feature_product_image' => $product->getSecondFeatureProductImageUrl(),
                        ],

                        'variants' => $product->productColors->map(fn ($variant) => [
                            'id' => $variant->id,
                            'color_id' => $variant->color_id,
                            'color_name' => optional($variant->color)->name,
                            'image_url' => asset('storage/' . $variant->image),
                            'sizes' => $variant->productColorSizes->map(fn ($pcs) => [
                                'id' => $pcs->id,
                                'size_id' => $pcs->size_id,
                                'size_name' => optional($pcs->size)->name,
                                'quantity' => $pcs->quantity,
                            ]),
                        ]),

                        'real_average_rating' => round($product->ratings->avg('rating') ?? 0, 1),

                        'actions' => $this->buildProductActionsWithMethods($product),
                    ];
                }),
            ],
        ]);
    }
}
