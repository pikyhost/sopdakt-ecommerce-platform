<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
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
    public function showWithProducts(Category $category)
    {
        $locale =app()->getLocale();

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

    protected function buildProductActionsWithMethods(Product $product): array
    {
        return [
            'add_to_cart' => [
                'method' => 'POST',
                'url' => route('cart.add'),
            ],
            'toggle_love' => [
                'method' => 'POST',
                'url' => route('wishlist.toggle'),
            ],
            'compare' => [
                'method' => 'POST',
                'url' => route('compare.add'),
            ],
            'view' => [
                'method' => 'GET',
                'url' => route('products.show', ['slug' => $product->slug]),
            ],
        ];
    }

    public function index(Request $request)
    {
        $locale = app()->getLocale();

        $categories = Category::where('is_published', true)
            ->withCount('products')
            ->latest()
            ->paginate(10);

        $transformedCategories = $categories->getCollection()->map(function ($category) use ($locale) {
            return [
                'id' => $category->id,
                'name' => $category->getTranslation('name', $locale),
                'slug' => $category->slug,
                'description' => $category->getTranslation('description', $locale),
                'image_url' => $category->getMainCategoryImageUrl(),
                'products_count' => $category->products_count,
                'url' => route('categories.show', $category->slug),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'categories' => $transformedCategories,
                'pagination' => [
                    'current_page' => $categories->currentPage(),
                    'last_page' => $categories->lastPage(),
                    'per_page' => $categories->perPage(),
                    'total' => $categories->total(),
                ],
            ],
        ]);
    }
}
