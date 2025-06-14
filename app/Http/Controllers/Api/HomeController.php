<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\HomePageSetting;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function footerInfo(): JsonResponse
    {
        $locale = app()->getLocale();

        $setting = Setting::first();

        $contact = Setting::getContactDetails();
        $socials = Setting::getSocialMediaLinks();

        return response()->json([
            'address' => $setting?->getTranslation('address', $locale) ?? '',
            'phone'   => $contact['phone'] ?? '',
            'email'   => $contact['email'] ?? '',
            'social_media' => $socials,
        ]);
    }

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
                                    'size_id' => $productColorSize->size->id ?? null,
                                    'size_name' => $productColorSize->size->name ?? null,
                                    'quantity' => $productColorSize->quantity,
                                ];
                            })->filter(fn ($size) => $size['quantity'] > 0)->values(),
                        ];
                    }),
                    'variants' => $product->productColors->map(function ($variant) {
                        return [
                            'id' => $variant->id,
                            'color_id' => $variant->color_id,
                            'color_name' => optional($variant->color)->name,
                            'image_url' => $variant->image ? asset('storage/' . $variant->image) : null,
                            'sizes' => $variant->productColorSizes->map(function ($pcs) {
                                return [
                                    'id' => $pcs->id,
                                    'size_id' => $pcs->size_id,
                                    'size_name' => optional($pcs->size)->name,
                                    'quantity' => $pcs->quantity,
                                ];
                            }),
                        ];
                    }),
                    'actions' => [
                        'add_to_cart' => route('cart.add'),
                        'toggle_love' => route('wishlist.toggle'),
                        'compare' => route('compare.add'),
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
                                    'size_id' => $productColorSize->size->id ?? null,
                                    'size_name' => $productColorSize->size->name ?? null,
                                    'quantity' => $productColorSize->quantity,
                                ];
                            })->filter(fn ($size) => $size['quantity'] > 0)->values(),
                        ];
                    }),
                    'variants' => $product->productColors->map(function ($variant) {
                        return [
                            'id' => $variant->id,
                            'color_id' => $variant->color_id,
                            'color_name' => optional($variant->color)->name,
                            'image_url' => $variant->image ? asset('storage/' . $variant->image) : null,
                            'sizes' => $variant->productColorSizes->map(function ($pcs) {
                                return [
                                    'id' => $pcs->id,
                                    'size_id' => $pcs->size_id,
                                    'size_name' => optional($pcs->size)->name,
                                    'quantity' => $pcs->quantity,
                                ];
                            }),
                        ];
                    }),
                    'actions' => [
                        'add_to_cart' => route('cart.add'),
                        'toggle_love' => route('wishlist.toggle'),
                        'compare' => route('compare.add'),
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
                'title' => $homePageSetting->getTranslation('center_title', $locale),
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
