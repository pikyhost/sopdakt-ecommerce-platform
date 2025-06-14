<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\ProductColor;
use App\Models\ProductColorSize;
use App\Models\Product;
use App\Models\Color;
use App\Models\ProductRating;
use App\Models\Size;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    protected function getCategoryWithChildrenIds($categoryId): array
    {
        $ids = [$categoryId];

        $childIds = \App\Models\Category::where('parent_id', $categoryId)->pluck('id')->toArray();
        foreach ($childIds as $childId) {
            $ids = array_merge($ids, $this->getCategoryWithChildrenIds($childId));
        }

        return $ids;
    }

    /**
     * @group Products
     *
     * Get all active products with filtering and pagination
     *
     * This endpoint returns a paginated list of all published products with their details, variants, and available filters.
     * Products can be filtered by various criteria like color, size, category, and rating.
     *
     * @queryParam color_id integer optional Filter products by color ID. Example: 1
     * @queryParam size_id integer optional Filter products by size ID. Example: 2
     * @queryParam category_id integer optional Filter products by category ID (includes subcategories). Example: 3
     * @queryParam min_rating float optional Filter products by minimum fake rating (1-5). Example: 4.5
     * @queryParam sort_by string optional Sort products by creation date. Possible values: 'latest', 'oldest'. Default: 'latest'. Example: latest
     *
     * @response 200 {
     *   "products": [
     *     {
     *       "id": 1,
     *       "category_id": 3,
     *       "category_name": "T-Shirts",
     *       "name": "Premium Cotton T-Shirt",
     *       "price": 29.99,
     *       "after_discount_price": 24.99,
     *       "description": "High quality cotton t-shirt...",
     *       "slug": "premium-cotton-t-shirt",
     *       "views": 150,
     *       "sales": 30,
     *       "fake_average_rating": 4.5,
     *       "label_id": null,
     *       "summary": "Comfortable and stylish...",
     *       "quantity": 100,
     *       "created_at": "2023-01-15T10:00:00.000000Z",
     *       "updated_at": "2023-01-20T12:30:00.000000Z",
     *       "media": {
     *         "feature_product_image": "https://example.com/storage/products/image1.jpg",
     *         "second_feature_product_image": "https://example.com/storage/products/image2.jpg"
     *       },
     *       "variants": [
     *         {
     *           "id": 5,
     *           "color_id": 1,
     *           "color_name": "Red",
     *           "image_url": "https://example.com/storage/variants/red.jpg",
     *           "sizes": [
     *             {
     *               "id": 10,
     *               "size_id": 2,
     *               "size_name": "M",
     *               "quantity": 25
     *             },
     *             {
     *               "id": 11,
     *               "size_id": 3,
     *               "size_name": "L",
     *               "quantity": 30
     *             }
     *           ]
     *         }
     *       ],
     *       "real_average_rating": 4.3,
     *       "actions": {
     *         "view": "https://example.com/api/products/1",
     *         "edit": "https://example.com/api/products/1/edit",
     *         "delete": "https://example.com/api/products/1"
     *       }
     *     }
     *   ],
     *   "pagination": {
     *     "current_page": 1,
     *     "last_page": 5,
     *     "per_page": 15,
     *     "total": 75
     *   },
     *   "filters": {
     *     "colors": [
     *       {
     *         "id": 1,
     *         "name": "Red"
     *       },
     *       {
     *         "id": 2,
     *         "name": "Blue"
     *       }
     *     ],
     *     "sizes": [
     *       {
     *         "id": 1,
     *         "name": "S"
     *       },
     *       {
     *         "id": 2,
     *         "name": "M"
     *       }
     *     ],
     *     "categories": [
     *       {
     *         "id": 1,
     *         "name": "Men",
     *         "parent_id": null
     *       },
     *       {
     *         "id": 3,
     *         "name": "T-Shirts",
     *         "parent_id": 1
     *       }
     *     ]
     *   }
     * }
     *
     * @responseField products The list of active products with their details.
     * @responseField pagination Pagination information.
     * @responseField filters Available filters for products (colors, sizes, categories).
     * @responseField products.id The product ID.
     * @responseField products.name The product name (translated to current locale).
     * @responseField products.price The original product price.
     * @responseField products.after_discount_price The discounted price if available.
     * @responseField products.media.feature_product_image The URL of the main product image.
     * @responseField products.variants Array of product variants with color and size information.
     * @responseField products.real_average_rating The actual average rating from customer reviews.
     * @responseField filters.colors Array of available colors for filtering.
     * @responseField filters.sizes Array of available sizes for filtering.
     * @responseField filters.categories Array of available categories for filtering.
     */
    public function getAllActiveProducts(Request $request): JsonResponse
    {
        $locale = app()->getLocale();

        $colorId = $request->input('color_id');
        $sizeId = $request->input('size_id');
        $categoryId = $request->input('category_id');
        $minRating = $request->input('min_rating');
        $sortBy = $request->input('sort_by'); // 'latest' or 'oldest'

        $products = Product::with([
            'category',
            'productColors.color',
            'productColors.productColorSizes.size',
            'ratings',
        ])
            ->where('is_published', true)

            // Filter by color
            ->when($colorId, fn($query) =>
            $query->whereHas('productColors', fn($q) => $q->where('color_id', $colorId)))

            // Filter by size
            ->when($sizeId, fn($query) =>
            $query->whereHas('productColors.productColorSizes', fn($q) => $q->where('size_id', $sizeId)))

            // Filter by category (including subcategories)
            // Filter by multiple categories (including their subcategories)
            ->when($request->has('category_ids'), function ($query) use ($request) {
                $inputIds = $request->input('category_ids', []);
                $allCategoryIds = collect($inputIds)
                    ->flatMap(fn($id) => $this->getCategoryWithChildrenIds($id))
                    ->unique()
                    ->values();

                $query->whereIn('category_id', $allCategoryIds);
            })

            // Filter by minimum fake rating
            ->when($minRating, fn($query) =>
            $query->where('fake_average_rating', '>=', $minRating))

            // Sorting
            ->when($sortBy === 'oldest', fn($query) => $query->orderBy('created_at', 'asc'))
            ->when($sortBy === 'latest' || !$sortBy, fn($query) => $query->orderBy('created_at', 'desc'))

            ->paginate(15);

        $result = $products->getCollection()->map(function ($product) use ($locale) {
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
                'variants' => $product->productColors->map(fn($variant) => [
                    'id' => $variant->id,
                    'color_code' =>  optional($variant->color)->code,
                    'color_id' => $variant->color_id,
                    'color_name' => optional($variant->color)->name,
                    'image_url' => asset('storage/' . $variant->image),
                    'sizes' => $variant->productColorSizes->map(fn($pcs) => [
                        'id' => $pcs->id,
                        'size_id' => $pcs->size_id,
                        'size_name' => optional($pcs->size)->name,
                        'quantity' => $pcs->quantity,
                    ]),
                ]),
                'real_average_rating' => round($product->ratings->avg('rating'), 1),
                'actions' => $this->buildProductActionsWithMethods($product),
            ];
        });

        // Get only used color and size IDs
        $usedColorIds = ProductColor::whereHas('product', fn($q) => $q->where('is_published', true))
            ->pluck('color_id')->unique();

        $usedSizeIds = ProductColorSize::whereHas('productColor.product', fn($q) => $q->where('is_published', true))
            ->pluck('size_id')->unique();

        $filters = [
            'colors' => Color::whereIn('id', $usedColorIds)
                ->get()
                ->map(fn($color) => [
                    'id' => $color->id,
                    'code' => $color->code,
                    'name' => $color->getTranslation('name', $locale),
                ]),

            'sizes' => Size::whereIn('id', $usedSizeIds)
                ->get()
                ->map(fn($size) => [
                    'id' => $size->id,
                    'name' => $size->name,
                ]),

            'categories' => Category::where('is_published', true)
                ->get()
                ->map(fn($category) => [
                    'id' => $category->id,
                    'name' => $category->getTranslation('name', $locale),
                    'parent_id' => $category->parent_id,
                ]),
        ];

        return response()->json([
            'products' => $result,
            'pagination' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ],
            'filters' => $filters,
        ]);

    }

    /**
     * Retrieve a list of recommended products.
     *
     * This endpoint fetches up to 4 randomly selected active (published) products as recommendations.
     * It includes detailed information such as categories, variants (colors and sizes), ratings, and
     * media URLs. The response is localized based on the current application locale (e.g., 'en', 'fr').
     * This endpoint is ideal for frontend use cases like displaying a "Recommended Products" section
     * on a homepage or product page in an e-commerce application.
     *
     * ### Request Details
     * - **Method**: GET
     * - **URL**: `/api/products/recommended`
     * - **Query Parameters**: None
     * - **Headers**:
     *   - `Accept: application/json`
     *   - `X-Locale: <locale>` (optional, defaults to app locale, e.g., 'en')
     *
     * ### Response Structure
     * The response contains a `recommended_products` key with an array of up to 4 product objects.
     * Each product includes:
     * - **id**: The product's unique identifier (integer).
     * - **category_id**: The ID of the product's category (integer).
     * - **category_name**: The localized name of the category (string, nullable).
     * - **name**: The localized name of the product (string).
     * - **price**: The original price of the product (float).
     * - **after_discount_price**: The discounted price, if applicable (float, nullable).
     * - **description**: The localized description of the product (string).
     * - **slug**: The URL-friendly slug for the product (string).
     * - **views**: Number of views for the product (integer).
     * - **sales**: Number of sales for the product (integer).
     * - **fake_average_rating**: A manually set average rating for display (float, nullable).
     * - **label_id**: The ID of the product's label, if any (integer, nullable).
     * - **summary**: The localized summary of the product (string).
     * - **quantity**: Total available quantity of the product (integer).
     * - **created_at**: Timestamp when the product was created (ISO 8601 string).
     * - **updated_at**: Timestamp when the product was last updated (ISO 8601 string).
     * - **media**: Object containing product images.
     *   - **feature_product_image**: URL of the primary feature image (string, nullable).
     *   - **second_feature_product_image**: URL of the secondary feature image (string, nullable).
     * - **variants**: Array of product variants (colors and sizes).
     *   - **id**: Variant ID (integer).
     *   - **color_id**: Color ID (integer).
     *   - **color_name**: Name of the color (string, nullable).
     *   - **image_url**: URL of the variant image (string).
     *   - **sizes**: Array of size options for the variant.
     *     - **id**: Product color size ID (integer).
     *     - **size_id**: Size ID (integer).
     *     - **size_name**: Name of the size (string, nullable).
     *     - **quantity**: Available quantity for this size (integer).
     * - **real_average_rating**: Computed average rating from user ratings, rounded to 1 decimal (float).
     * - **actions**: Array of available actions for the product (implementation-specific, e.g., URLs or methods).
     *
     * ### Notes for Frontend Developers
     * - **Randomization**: Products are returned in random order using `inRandomOrder()`. The selection may
     *   vary with each request, and fewer than 4 products may be returned if there are not enough active products.
     * - **Limit**: The response is limited to 4 products. Ensure your frontend UI can handle fewer items (0-4).
     * - **Locale Handling**: The `name`, `category_name`, `description`, and `summary` fields are localized based
     *   on the current app locale. Use the `X-Locale` header to override the default locale if needed.
     * - **Nullable Fields**: Fields like `category_name`, `color_name`, `size_name`, `after_discount_price`,
     *   `feature_product_image`, and `second_feature_product_image` may be `null`. Provide fallback values
     *   (e.g., "N/A" or a default image).
     * - **Image URLs**: The `image_url` in `variants` and media URLs are absolute URLs using the `storage`
     *   directory. Ensure `/storage/` is accessible (run `php artisan storage:link` on the server).
     * - **Ratings**: `real_average_rating` is computed from user ratings, while `fake_average_rating` is a preset
     *   value. Prefer `real_average_rating` for authenticity, or use `fake_average_rating` for display if set.
     * - **Actions**: The `actions` field is implementation-specific and may contain URLs or methods for actions
     *   like "add to cart" or "view details". Parse this field based on your frontend requirements.
     *
     * @return JsonResponse The JSON response containing the list of recommended products.
     *
     * @response 200 {
     *     "recommended_products": [
     *         {
     *             "id": 3,
     *             "category_id": 1,
     *             "category_name": "Electronics",
     *             "name": "Wireless Headphones",
     *             "price": 99.99,
     *             "after_discount_price": 79.99,
     *             "description": "High-quality wireless headphones with noise cancellation.",
     *             "slug": "wireless-headphones",
     *             "views": 200,
     *             "sales": 50,
     *             "fake_average_rating": 4.8,
     *             "label_id": 2,
     *             "summary": "Immersive sound with long battery life.",
     *             "quantity": 80,
     *             "created_at": "2025-05-04T12:00:00Z",
     *             "updated_at": "2025-05-04T12:00:00Z",
     *             "media": {
     *                 "feature_product_image": "https://yourapp.com/storage/images/headphones.jpg",
     *                 "second_feature_product_image": "https://yourapp.com/storage/images/headphones-side.jpg"
     *             },
     *             "variants": [
     *                 {
     *                     "id": 5,
     *                     "color_id": 2,
     *                     "color_name": "Black",
     *                     "image_url": "https://yourapp.com/storage/variants/headphones-black.jpg",
     *                     "sizes": [
     *                         {
     *                             "id": 7,
     *                             "size_id": 1,
     *                             "size_name": "One Size",
     *                             "quantity": 80
     *                         }
     *                     ]
     *                 }
     *             ],
     *             "real_average_rating": 4.5,
     *             "actions": {
     *                 "view": "https://yourapp.com/api/products/3",
     *                 "add_to_cart": "https://yourapp.com/api/cart/add/3"
     *             }
     *         }
     *     ]
     * }
     * @response 200 {
     *     "recommended_products": []
     * }
     * @response 500 {
     *     "error": "Failed to retrieve recommended products. Please try again later."
     * }
     */
    public function getRecommendedProducts(): JsonResponse
    {
        $locale = app()->getLocale();

        $recommendedProducts = Product::with([
            'category',
            'productColors.color',
            'productColors.productColorSizes.size',
            'ratings',
        ])
            ->where('is_published', true)
            ->inRandomOrder()
            ->limit(4)
            ->get();

        $result = $recommendedProducts->map(function ($product) use ($locale) {
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
                'variants' => $product->productColors->map(fn($variant) => [
                    'id' => $variant->id,
                    'color_id' => $variant->color_id,
                    'color_name' => optional($variant->color)->name,
                    'image_url' => asset('storage/' . $variant->image),
                    'sizes' => $variant->productColorSizes->map(fn($pcs) => [
                        'id' => $pcs->id,
                        'size_id' => $pcs->size_id,
                        'size_name' => optional($pcs->size)->name,
                        'quantity' => $pcs->quantity,
                    ]),
                ]),

                'real_average_rating' => round($product->ratings->avg('rating'), 1),

                'actions' => $this->buildProductActionsWithMethods($product),
            ];
        });

        return response()->json(['recommended_products' => $result]);
    }

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
            'bundles.products', // Load products inside bundles
            'ratings',
        ])
            ->where('slug', $slug)
            ->where('is_published', true)
            ->first();

        if (!$product) {
            return response()->json(['message' => 'Product not found.'], 404);
        }

        $user = auth('sanctum')->user();
        $isAdmin = $user && $user->hasRole(['admin', 'super_admin']);

        // Fetch all ratings based on user role
        $ratingsQuery = ProductRating::where('product_id', $product->id)
            ->where(function ($query) use ($user, $isAdmin) {
                $query->where('status', 'approved');

                if ($user) {
                    // Authenticated users see their own pending review
                    $query->orWhere(function ($q) use ($user) {
                        $q->where('status', 'pending')->where('user_id', $user->id);
                    });
                }

                if ($isAdmin) {
                    // Admins see everything
                    $query->orWhereNotNull('id');
                }
            })
            ->latest()
            ->get(); // Get all results without pagination

        return response()->json([
            'product' => [
                'id' => $product->id,
                'ratings' => $ratingsQuery,
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
                'summary' => $product->getTranslation('summary', $locale),
                'quantity' => $product->quantity,
                'custom_attributes' => json_decode(json_encode($product->getTranslation('custom_attributes', $locale))),
                'is_published' => $product->is_published,
                'is_featured' => $product->is_featured,
                'is_free_shipping' => $product->is_free_shipping,
                'created_at' => $product->created_at,
                'updated_at' => $product->updated_at,
                // Media section
                'media' => [
                    'size_guide_image' => $product->getSizeGuideProductImageUrl(),
                    'feature_product_image' => $product->getFeatureProductImageUrl(),
                    'second_feature_product_image' => $product->getSecondFeatureProductImageUrl(),
                    'more_product_images_and_videos' => $product->getMoreProductImagesAndVideosUrls(),
                ],
                // Size guide section
                'size_guide' => $product->sizeGuide ? [
                    'title' => $product->sizeGuide->title,
                    'description' => $product->sizeGuide->description,
                    'image_url' => $product->getSizeGuideProductImageUrl(),
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

                // Bundles
                'bundles' => $product->bundles->map(fn($bundle) => [
                    'id' => $bundle->id,
                    'name' => $bundle->getTranslation('name', $locale),
                    'type' => $bundle->bundle_type,
                    'discount_price' => $bundle->discount_price,
                    'formatted_price' => $bundle->formatPrice(),
                    'buy_x' => $bundle->buy_x,
                    'get_y' => $bundle->get_y,
                    'buy_quantity' => $bundle->buy_quantity,
                    'products' => $bundle->products->map(fn($bundleProduct) => [
                        'id' => $bundleProduct->id,
                        'name' => $bundleProduct->getTranslation('name', $locale),
                        'image' => $bundleProduct->getFeatureProductImageUrl(),
                    ]),
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
