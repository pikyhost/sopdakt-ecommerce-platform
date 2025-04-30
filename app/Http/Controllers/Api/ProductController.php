<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProductColor;
use App\Models\ProductColorSize;
use App\Models\Product;
use App\Models\Color;
use App\Models\Size;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Get all color and size variants for a product.
     *
     * This endpoint returns all color variants for a given product, each including:
     * - Color information (ID, name)
     * - Associated image (if available)
     * - Size variants under each color, including:
     *   - Size ID
     *   - Size name
     *   - Available quantity
     *
     * This structure matches the `variants` format used in the product detail endpoint.
     *
     * @group Products
     *
     * @urlParam product int required The ID of the product. Example: 1
     *
     * @response 200 {
     *   "variants": [
     *     {
     *       "id": 1,
     *       "color_id": 3,
     *       "color_name": "Red",
     *       "image_url": "https://example.com/storage/variant1.jpg",
     *       "sizes": [
     *         {
     *           "id": 5,
     *           "size_id": 2,
     *           "size_name": "L",
     *           "quantity": 8
     *         },
     *         {
     *           "id": 6,
     *           "size_id": 3,
     *           "size_name": "XL",
     *           "quantity": 4
     *         }
     *       ]
     *     }
     *   ]
     * }
     */
    public function colorsSizes($id): JsonResponse
    {
        // Re-fetch with correct and fresh relationships
        $product = Product::with([
            'productColors.color',
            'productColors.productColorSizes.size',
        ])->findOrFail($id);

        $variants = $product->productColors->map(function ($variant) {
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
        });

        return response()->json(['variants' => $variants]);
    }


    /**
     * Get a single published product by its slug.
     *
     * This endpoint retrieves a product using its unique slug. The response includes:
     * - Localized fields (`name`, `description`, `summary`, `meta_title`, etc.) based on the `Accept-Language` header.
     * - Associated user and category names.
     * - Media including feature image, secondary image, sizes image, and more images/videos.
     * - Size guide (if any) with title, description, and image URL.
     * - Variants (product colors) with nested sizes and quantity.
     * - Labels (with localized titles and color codes).
     * - Bundles the product is part of (optional).
     * - Real average rating based on reviews.
     * - Action endpoints with methods for cart, wishlist, and comparison.
     *
     * @group Products
     *
     * @urlParam slug string required The unique slug of the product. Example: "smartphone-2025"
     *
     * @response 200 {
     *   "product": {
     *     "id": 1,
     *     "user_name": "Admin",
     *     "category_name": "Accessories",
     *     "name": "Localized Product Name",
     *     ...
     *     "media": {
     *       "feature_product_image": "https://example.com/storage/feature.jpg",
     *       "second_feature_product_image": "https://example.com/storage/feature2.jpg",
     *       "sizes_image": "https://example.com/storage/sizes.jpg",
     *       "more_product_images_and_videos": [...]
     *     },
     *     "size_guide": {
     *       "title": "Size Chart",
     *       "description": "Details...",
     *       "image_url": "https://example.com/storage/size_guide.jpg"
     *     },
     *     "variants": [...],
     *     "labels": [...],
     *     "bundles": [...],
     *     "average_rating": 4.3,
     *     "actions": {
     *       "add_to_cart": { "method": "POST", "url": "/api/cart" },
     *       ...
     *     }
     *   }
     * }
     */
    public function showBySlug(string $slug): JsonResponse
    {
        $locale = app()->getLocale();

        $product = Product::with([
            'user',
            'category',
            'sizeGuide',
            'productColors.color',
            'productColors.productColorSizes.size',
            'labels',
            'bundles',
            'ratings',
        ])
            ->where('slug', $slug)
            ->where('is_published', true)
            ->first();

        if (!$product) {
            return response()->json(['message' => 'Product not found.'], 404);
        }

        return response()->json([
            'product' => [
                'id' => $product->id,
                'user_id' => $product->user_id,
                'user_name' => optional($product->user)->name,
                'category_id' => $product->category_id,
                'category_name' => optional($product->category)?->getTranslation('name', $locale),
                'name' => $product->getTranslation('name', $locale),
                'sku' => $product->sku,
                'price' => $product->price,
                'after_discount_price' => $product->after_discount_price,
                'cost' => $product->cost,
                'shipping_estimate_time' => $product->shipping_estimate_time,
                'description' => $product->getTranslation('description', $locale),
                'slug' => $product->slug,
                'meta_title' => $product->getTranslation('meta_title', $locale),
                'meta_description' => $product->getTranslation('meta_description', $locale),
                'discount_start' => $product->discount_start,
                'discount_end' => $product->discount_end,
                'views' => $product->views,
                'sales' => $product->sales,
                'fake_average_rating' => $product->fake_average_rating,
                'label_id' => $product->label_id,
                'summary' => $product->getTranslation('summary', $locale),
                'quantity' => $product->quantity,
                'custom_attributes' => $product->getTranslation('custom_attributes', $locale),
                'is_published' => $product->is_published,
                'is_featured' => $product->is_featured,
                'is_free_shipping' => $product->is_free_shipping,
                'created_at' => $product->created_at,
                'updated_at' => $product->updated_at,

                // Media section
                'media' => [
                    'feature_product_image' => $product->getFeatureProductImageUrl(),
                    'second_feature_product_image' => $product->getSecondFeatureProductImageUrl(),
                    'more_product_images_and_videos' => $product->getMoreProductImagesAndVideosUrls(),
                ],

                // Size guide section
                'size_guide' => $product->sizeGuide ? [
                    'title' => $product->sizeGuide->title,
                    'description' => $product->sizeGuide->description,
                    'image_url' => asset('storage/' . $product->sizeGuide->image_path),
                ] : null,

                // Labels
                'labels' => $product->labels->map(function ($label) use ($locale) {
                    return [
                        'id' => $label->id,
                        'title' => $label->getTranslation('title', $locale),
                        'color_code' => $label->color_code,
                        'background_color_code' => $label->background_color_code,
                    ];
                }),

                // Variants with color and sizes
                'variants' => $product->productColors->map(function ($variant) {
                    return [
                        'id' => $variant->id,
                        'color_id' => $variant->color_id,
                        'color_name' => optional($variant->color)->name,
                        'image_url' => asset('storage/' . $variant->image),
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

                // Bundles (if needed)
                'bundles' => $product->bundles->map(fn($bundle) => [
                    'id' => $bundle->id,
                    'name' => $bundle->getTranslation('name', $locale),
                    'type' => $bundle->bundle_type,
                    'discount_price' => $bundle->discount_price,
                    'formatted_price' => $bundle->formatPrice(),
                    'buy_x' => $bundle->buy_x,
                    'get_y' => $bundle->get_y,
                ]),

                // Real average rating
                'real_average_rating' => round($product->ratings->avg('rating'), 1),

                // Actions for UI
                'actions' => $this->buildProductActionsWithMethods($product),
            ]
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


    /**
     * Get up to 3 featured published products.
     *
     * This endpoint retrieves a limited list of featured and published products.
     * It returns localized fields based on the `Accept-Language` header and includes:
     * - Localized fields (`name`, `description`, `summary`, `meta_title`, etc.)
     * - Related user and category names
     * - Product media (feature image, secondary image, and more)
     * - Frontend action URLs (e.g., add to cart, wishlist)
     *
     * @group Products
     *
     * @response 200 {
     *   "products": [
     *     {
     *       "id": 1,
     *       "name": "Localized name",
     *       "description": "Localized description...",
     *       ...
     *       "media": {
     *         "feature_product_image": "https://example.com/storage/feature.jpg",
     *         "second_feature_product_image": "https://example.com/storage/feature2.jpg",
     *         "more_product_images_and_videos": [
     *           "https://example.com/storage/image1.jpg",
     *           "https://example.com/storage/video1.mp4"
     *         ]
     *       },
     *       "actions": {
     *         "add_to_cart": {
     *           "method": "POST",
     *           "url": "https://example.com/api/cart"
     *         },
     *         ...
     *       }
     *     }
     *   ]
     * }
     */
    public function featured()
    {
        $locale = app()->getLocale();

        $products = Product::where('is_published', true)
            ->where('is_featured', true)
            ->limit(3)
            ->get()
            ->map(function (Product $product) use ($locale) {
                return [
                    'id' => $product->id,
                    'category_id' => $product->category_id,
                    'category_name' => optional($product->category)?->getTranslation('name', $locale),
                    'name' => $product->getTranslation('name', $locale),
                    'sku' => $product->sku,
                    'price' => $product->price,
                    'after_discount_price' => $product->after_discount_price,
                    'description' => $product->getTranslation('description', $locale),
                    'slug' => $product->slug,
                    'meta_title' => $product->getTranslation('meta_title', $locale),
                    'meta_description' => $product->getTranslation('meta_description', $locale),
                    'discount_start' => $product->discount_start,
                    'discount_end' => $product->discount_end,
                    'views' => $product->views,
                    'sales' => $product->sales,
                    'fake_average_rating' => $product->fake_average_rating,
                    'summary' => $product->getTranslation('summary', $locale),
                    'quantity' => $product->quantity,
                    'custom_attributes' => $product->getTranslation('custom_attributes', $locale),
                    'is_published' => $product->is_published,
                    'is_featured' => $product->is_featured,
                    'is_free_shipping' => $product->is_free_shipping,
                    'created_at' => $product->created_at,
                    'updated_at' => $product->updated_at,

                    'media' => [
                        'feature_product_image' => $product->getFeatureProductImageUrl(),
                        'second_feature_product_image' => $product->getSecondFeatureProductImageUrl(),
                    ],

                    'actions' => $this->buildProductActions($product),
                ];
            });

        return response()->json([
            'products' => $products,
        ]);
    }

    protected function buildProductActions(Product $product): array
    {
        return [
            'add_to_cart' => route('cart.add'), // No parameter passed
            'toggle_love' => route('wishlist.toggle'), // Also doesn't accept params in URL
            'compare' => route('compare.add'), // Same here
            'view' => route('products.show', ['slug' => $product->slug]),
        ];
    }

}
