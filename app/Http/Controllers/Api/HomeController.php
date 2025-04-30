<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\HomePageSettingResource;
use App\Models\Category;
use App\Models\HomePageSetting;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Get Featured Categories
     *
     * @group Homepage
     *
     * Retrieves a list of featured categories for the homepage with their images.
     *
     * @response 200 {
     *   "success": true,
     *   "data": [
     *     {
     *       "name": "Electronics",
     *       "image_url": "https://example.com/media/categories/electronics.jpg"
     *     },
     *     {
     *       "name": "Fashion",
     *       "image_url": "https://example.com/media/categories/fashion.jpg"
     *     }
     *   ]
     * }
     * @response 500 {
     *   "success": false,
     *   "message": "Server Error"
     * }
     */
    public function featuredCategories(): JsonResponse
    {
        // Eager load the media relationship and only select needed columns
        $categories = Category::with(['media' => function ($query) {
            $query->where('collection_name', 'main_category_image');
        }])
            ->select('id', 'name')
            ->where('is_published', true)
            ->whereNull('parent_id')
            ->orderBy('created_at', 'desc')
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
     * Get Best Selling Products
     *
     * @group Homepage
     *
     * Retrieves top 10 best-selling products with complete details including:
     * - Product information
     * - Available colors with sizes
     * - Action links
     *
     * @response 200 {
     *   "success": true,
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "Premium Headphones",
     *       "price": 199.99,
     *       "after_discount_price": 179.99,
     *       "sales": 150,
     *       "slug": "premium-headphones",
     *       "image_url": "https://example.com/media/products/headphones.jpg",
     *       "category": {
     *         "name": "Electronics",
     *         "slug": "electronics"
     *       },
     *       "colors_with_sizes": [
     *         {
     *           "color_name": "Black",
     *           "color_code": "#000000",
     *           "color_image": null,
     *           "sizes": [
     *             {
     *               "size_name": "One Size",
     *               "quantity": 50
     *             }
     *           ]
     *         }
     *       ],
     *       "actions": {
     *         "add_to_cart": "http://example.com/api/cart/1",
     *         "toggle_love": "http://example.com/api/wishlist/toggle/1",
     *         "compare": "http://example.com/api/compare/1",
     *         "view": "http://example.com/products/premium-headphones"
     *       }
     *     }
     *   ]
     * }
     */
    public function fakeBestSellers(): JsonResponse
    {
        $products = Product::with([
            'media',
            'category',
            'productColors.color',
            'productColors.productColorSizes.size',
        ])
            ->select([
                'id',
                'name',
                'price',
                'after_discount_price',
                'sales',
                'slug',
                'category_id',
                'fake_average_rating'
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
                        'add_to_cart' => route('cart.add'), // No parameter passed
                        'toggle_love' => route('wishlist.toggle'), // Also doesn't accept params in URL
                        'compare' => route('compare.add'), // Same here
                        'view' => route('products.show', ['slug' => $product->slug]),
                    ],
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $products,
        ]);
    }

    public function realBestSellers()
    {
        $bestSellers = Product::withTranslation()
            ->withCount(['orderItems as total_sold' => function ($query) {
                $query->select(DB::raw("SUM(quantity)"));
            }])
            ->orderByDesc('total_sold')
            ->whereHas('orderItems') // Only products that were ordered
            ->take(10)
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
                        'add_to_cart' => route('cart.add'), // No parameter passed
                        'toggle_love' => route('wishlist.toggle'), // Also doesn't accept params in URL
                        'compare' => route('compare.add'), // Same here
                        'view' => route('products.show', ['slug' => $product->slug]),
                    ],
                ];
            });

        return response()->json([
            'message' => __('Best selling products retrieved successfully.'),
            'products' => $bestSellers,
        ]);
    }

    /**
     * Get Homepage Slider with CTA
     *
     * @group Homepage
     *
     * Retrieves configured slider images and call-to-action data for the homepage.
     *
     * @response 200 {
     *   "data": {
     *     "slider_images": [
     *       {
     *         "url": "http://example.com/media/slider/slide1.jpg",
     *         "link": "/products/summer-collection",
     *         "text": "Summer Sale"
     *       }
     *     ],
     *     "cta": {
     *       "title": "New Arrivals",
     *       "description": "Check out our latest products",
     *       "button_text": "Shop Now",
     *       "button_link": "/new-arrivals"
     *     }
     *   }
     * }
     * @response 404 {
     *   "message": "Homepage settings not found."
     * }
     */
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
