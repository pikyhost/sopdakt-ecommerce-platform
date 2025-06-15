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
    public function getAllActiveProducts(Request $request): JsonResponse
    {
        $locale = app()->getLocale();

        $colorId = $request->input('color_id');
        $sizeId = $request->input('size_id');
        $categoryId = $request->input('category_id');
        $minRating = $request->input('min_rating');
        $sortBy = $request->input('sort_by'); // 'latest' or 'oldest'

        // Get session ID or user for wishlist checking
        $sessionId = null;
        $userId = null;

        if ($request->header('x-session-id')) {
            $sessionId = $request->header('x-session-id');
        }

        $user = auth('sanctum')->user();
        if ($user) {
            $userId = $user->id;
        }

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

        // Get wishlisted product IDs for current user/session
        $wishlistedProductIds = collect();

        if ($userId) {
            $wishlistedProductIds = \DB::table('saved_products')
                ->where('user_id', $userId)
                ->pluck('product_id');
        } elseif ($sessionId) {
            $wishlistedProductIds = \DB::table('saved_products')
                ->where('session_id', $sessionId)
                ->pluck('product_id');
        }

        $result = $products->getCollection()->map(function ($product) use ($locale, $wishlistedProductIds) {
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
                'is_wishlisted' => $wishlistedProductIds->contains($product->id),
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

    public function getRecommendedProducts(): JsonResponse
    {
        $locale = app()->getLocale();

        // Get session ID or user for wishlist checking
        $sessionId = null;
        $userId = null;

        if (request()->header('x-session-id')) {
            $sessionId = request()->header('x-session-id');
        }

        $user = auth('sanctum')->user();
        if ($user) {
            $userId = $user->id;
        }

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

        // Get wishlisted product IDs for current user/session
        $wishlistedProductIds = collect();

        if ($userId) {
            $wishlistedProductIds = \DB::table('saved_products')
                ->where('user_id', $userId)
                ->pluck('product_id');
        } elseif ($sessionId) {
            $wishlistedProductIds = \DB::table('saved_products')
                ->where('session_id', $sessionId)
                ->pluck('product_id');
        }

        $result = $recommendedProducts->map(function ($product) use ($locale, $wishlistedProductIds) {
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
                'is_wishlisted' => $wishlistedProductIds->contains($product->id),
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

    public function showBySlug(string $slug): JsonResponse
    {
        $locale = app()->getLocale();

        // Get session ID or user for wishlist checking
        $sessionId = null;
        $userId = null;

        if (request()->header('x-session-id')) {
            $sessionId = request()->header('x-session-id');
        }

        $user = auth('sanctum')->user();
        if ($user) {
            $userId = $user->id;
        }

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

        // Check if product is wishlisted
        $isWishlisted = false;
        if ($userId) {
            $isWishlisted = \DB::table('saved_products')
                ->where('user_id', $userId)
                ->where('product_id', $product->id)
                ->exists();
        } elseif ($sessionId) {
            $isWishlisted = \DB::table('saved_products')
                ->where('session_id', $sessionId)
                ->where('product_id', $product->id)
                ->exists();
        }

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
                'is_wishlisted' => $isWishlisted,
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

    public function featured()
    {
        $locale = app()->getLocale();

        // Get session ID or user for wishlist checking
        $sessionId = null;
        $userId = null;

        if (request()->header('x-session-id')) {
            $sessionId = request()->header('x-session-id');
        }

        $user = auth('sanctum')->user();
        if ($user) {
            $userId = $user->id;
        }

        $featuredProducts = Product::where('is_published', true)
            ->where('is_featured', true)
            ->limit(3)
            ->get();

        // Get wishlisted product IDs for current user/session
        $wishlistedProductIds = collect();

        if ($userId) {
            $wishlistedProductIds = \DB::table('saved_products')
                ->where('user_id', $userId)
                ->pluck('product_id');
        } elseif ($sessionId) {
            $wishlistedProductIds = \DB::table('saved_products')
                ->where('session_id', $sessionId)
                ->pluck('product_id');
        }

        $products = $featuredProducts->map(function (Product $product) use ($locale, $wishlistedProductIds) {
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
                'is_wishlisted' => $wishlistedProductIds->contains($product->id),
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

    protected function getCategoryWithChildrenIds($categoryId): array
    {
        $ids = [$categoryId];

        $childIds = \App\Models\Category::where('parent_id', $categoryId)->pluck('id')->toArray();
        foreach ($childIds as $childId) {
            $ids = array_merge($ids, $this->getCategoryWithChildrenIds($childId));
        }

        return $ids;
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
