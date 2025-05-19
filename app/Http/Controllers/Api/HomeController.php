<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\HomePageSetting;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
    public function featuredCategories(Request $request): JsonResponse
    {
        $locale = app()->getLocale();

        $categories = Category::with(['media' => fn ($query) =>
        $query->where('collection_name', 'main_category_image')
        ])
            ->select('id', 'name', 'slug')
            ->where('is_published', true)
            ->whereNull('parent_id')
            ->orderByDesc('created_at')
            ->limit(8)
            ->get()
            ->map(function ($category) use ($locale) {
                return [
                    'id' => $category->id,
                    'name' => $category->getTranslation('name', $locale),
                    'slug' => $category->slug,
                    'image_url' => $category->getMainCategoryImageUrl(),
                    'actions' => [
                        'web_url' => url("/categories/{$category->slug}"),
                    ],
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $categories,
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
                    'media' => [
                        'feature_product_image' => $product->getFeatureProductImageUrl(),
                        'second_feature_product_image' => $product->getSecondFeatureProductImageUrl(),
                    ],
                    'category' => $product->category?->only(['name', 'slug']),
                    'colors_with_sizes' => $product->productColors->map(function ($productColor) {
                        return [
                            'color_id' => $productColor->color->id ?? null,
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

    /**
     * Get the real top 10 best-selling products based on the total quantity sold.
     *
     * This method retrieves products that have been ordered at least once,
     * calculates the total quantity sold (`total_sold`) via the `orderItems` relationship,
     * and returns the top 10 products sorted by the highest sales.
     *
     * Each product includes:
     * - Basic info: id, name (translated), price, discounted price, sales count, slug, image URL
     * - Category (with name and slug)
     * - Available color variants with:
     *     - Color name, code, and image
     *     - Sizes available for that color with quantity > 0
     * - Action URLs:
     *     - Add to cart
     *     - Toggle wishlist
     *     - Compare
     *     - View product details
     *
     * Translation is handled automatically via Spatie Laravel Translatable package.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * Example Response:
     * {
     *     "message": "Best selling products retrieved successfully.",
     *     "products": [
     *         {
     *             "id": 1,
     *             "name": "Stylish Shirt",
     *             "price": 100,
     *             "after_discount_price": 80,
     *             "sales": 50,
     *             "slug": "stylish-shirt",
     *             "image_url": "https://example.com/storage/media/filename.jpg",
     *             "category": {
     *                 "name": "Men",
     *                 "slug": "men"
     *             },
     *             "colors_with_sizes": [
     *                 {
     *                     "color_name": "Red",
     *                     "color_code": "#FF0000",
     *                     "color_image": "https://example.com/storage/colors/red.png",
     *                     "sizes": [
     *                         {
     *                             "size_name": "M",
     *                             "quantity": 5
     *                         },
     *                         ...
     *                     ]
     *                 },
     *                 ...
     *             ],
     *             "actions": {
     *                 "add_to_cart": "https://yourdomain.com/cart",
     *                 "toggle_love": "https://yourdomain.com/wishlist/toggle",
     *                 "compare": "https://yourdomain.com/compare/add",
     *                 "view": "https://yourdomain.com/products/stylish-shirt"
     *             }
     *         },
     *         ...
     *     ]
     * }
     */
    public function realBestSellers()
    {
        $bestSellers = Product::query()
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
                    'media' => [
                        'feature_product_image' => $product->getFeatureProductImageUrl(),
                        'second_feature_product_image' => $product->getSecondFeatureProductImageUrl(),
                    ],
                    'category' => $product->category?->only(['name', 'slug']),
                    'colors_with_sizes' => $product->productColors->map(function ($productColor) {
                        return [
                            'color_id' => $productColor->color->id ?? null,
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
     * Get homepage slider and CTA content.
     *
     * This endpoint returns the main slider, second slider, center section,
     * last sections, and latest section based on the current app locale
     * (either English or Arabic).
     *
     * @route GET /api/homepage/slider-with-cta
     *
     * @queryParam locale string Optional. Language code ('en' or 'ar').
     *
     * @response {
     *   "data": {
     *     "main_slider": {
     *       "image_url": "string",
     *       "thumbnail_url": "string",
     *       "heading": "string",
     *       "discount_text": "string",
     *       "discount_value": "string",
     *       "starting_price": "string",
     *       "currency_symbol": "string",
     *       "button_text": "string",
     *       "button_url": "string"
     *     },
     *     "second_slider": {
     *       "image_url": "string",
     *       "thumbnail_url": "string"
     *     },
     *     "center_section": {
     *       "image_url": "string",
     *       "heading": "string",
     *       "button_text": "string",
     *       "button_url": "string"
     *     },
     *     "last_sections": [
     *       {
     *         "image_url": "string",
     *         "heading": "string",
     *         "subheading": "string",
     *         "button_text": "string",
     *         "button_url": "string"
     *       },
     *       ...
     *     ],
     *     "latest_section": {
     *       "image_url": "string",
     *       "heading": "string",
     *       "button_text": "string",
     *       "button_url": "string"
     *     }
     *   }
     * }
     *
     * @return \Illuminate\Http\JsonResponse|\App\Http\Resources\HomePageSettingResource
     */
    public function sliderWithCta()
    {
        $homePageSetting = HomePageSetting::getCached();

        if (!$homePageSetting) {
            return response()->json([
                'message' => 'Homepage settings not found.'
            ], 404);
        }

        $locale = app()->getLocale();

        $data = [
            'main_slider' => [
                'image_url' => $homePageSetting->getSlider1ImageUrl(),
                'thumbnail_url' => $homePageSetting->getSlider1ThumbnailUrl(),
                'heading' => $homePageSetting->getTranslation('main_heading', $locale),
                'discount_text' => $homePageSetting->getTranslation('discount_text', $locale),
                'discount_value' => $homePageSetting->getTranslation('discount_value', $locale),
                'starting_price' => $homePageSetting->starting_price,
                'currency_symbol' => $homePageSetting->currency_symbol,
                'button_text' => $homePageSetting->getTranslation('button_text', $locale),
                'button_url' => $homePageSetting->button_url,
            ],
            'second_slider' => [
                'image_url' => $homePageSetting->getSlider2ImageUrl(),
                'thumbnail_url' => $homePageSetting->getSlider2ThumbnailUrl(),
            ],
            'center_section' => [
                'image_url' => $homePageSetting->getCenterImageUrl(),
                'heading' => $homePageSetting->getTranslation('center_main_heading', $locale),
                'button_text' => $homePageSetting->getTranslation('center_button_text', $locale),
                'button_url' => $homePageSetting->center_button_url,
            ],
            'last_sections' => [
                [
                    'image_url' => $homePageSetting->getLast1ImageUrl(),
                    'heading' => $homePageSetting->getTranslation('last1_heading', $locale),
                    'subheading' => $homePageSetting->getTranslation('last1_subheading', $locale),
                    'button_text' => $homePageSetting->getTranslation('last1_button_text', $locale),
                    'button_url' => $homePageSetting->last1_button_url,
                ],
                [
                    'image_url' => $homePageSetting->getLast2ImageUrl(),
                    'heading' => $homePageSetting->getTranslation('last2_heading', $locale),
                    'subheading' => $homePageSetting->getTranslation('last2_subheading', $locale),
                    'button_text' => $homePageSetting->getTranslation('last2_button_text', $locale),
                    'button_url' => $homePageSetting->last2_button_url,
                ]
            ],
            'latest_section' => [
                'heading' => $homePageSetting->getTranslation('latest_heading', $locale),
                'button_text' => $homePageSetting->getTranslation('latest_button_text', $locale),
                'button_url' => $homePageSetting->latest_button_url,
            ],
        ];

        return response()->json(['data' => $data]);
    }
}
