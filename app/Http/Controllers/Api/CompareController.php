<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

/**
 * @group Products
 *
 * APIs for managing product comparison
 */
class CompareController extends Controller
{
    /**
     * Compare Products
     *
     * Compares multiple products side-by-side by their IDs.
     *
     * @bodyParam product_ids array required Array of product IDs to compare (minimum 2 products). Example: [1, 2, 3]
     * @bodyParam product_ids.* integer required Each ID must exist in the products table.
     *
     * @response 200 {
     *   "products": [
     *     {
     *       "id": 1,
     *       "name": "Premium Headphones",
     *       "description": "Noise-cancelling wireless headphones",
     *       "meta_title": "Best Wireless Headphones",
     *       "meta_description": "Top-quality wireless headphones with noise cancellation",
     *       "price": 199,
     *       "after_discount_price": 179,
     *       "slug": "premium-headphones",
     *       "quantity": 25,
     *       "sku": "HD2024",
     *       "cost": 120,
     *       "shipping_estimate_time": "2-4 days",
     *       "discount_start": "2025-04-01 00:00:00",
     *       "discount_end": "2025-04-30 23:59:59",
     *       "views": 1280,
     *       "sales": 430,
     *       "fake_average_rating": 4,
     *       "summary": "Great sound quality in a compact design",
     *       "custom_attributes": {
     *         "color": "black",
     *         "battery_life": "30 hours"
     *       },
     *       "is_published": true,
     *       "is_featured": false,
     *       "is_free_shipping": true
     *     }
     *   ]
     * }
     *
     * @response 422 {
     *   "message": "The given data was invalid.",
     *   "errors": {
     *     "product_ids": [
     *       "The product ids field is required.",
     *       "The product ids must be an array.",
     *       "The product ids must have at least 2 items."
     *     ],
     *     "product_ids.0": [
     *       "The selected product_ids.0 is invalid."
     *     ]
     *   }
     * }
     */
    public function compare(Request $request)
    {
        $request->validate([
            'product_ids' => 'required|array|min:2',
            'product_ids.*' => 'exists:products,id',
        ]);

        $locale = app()->getLocale();

        $products = Product::with([
            'category',
            'productColors.color',
            'productColors.productColorSizes.size',
            'ratings',
        ])
            ->whereIn('id', $request->product_ids)
            ->get()
            ->map(function ($product) use ($locale) {
                return [
                    'id' => $product->id,
                    'status' => $product->quantity > 0 ? "In Stock" : "Out of stock",
                    'category_id' => $product->category_id,
                    'name' => $product->getTranslation('name', $locale),
                    'description' => $product->getTranslation('description', $locale),
                    'meta_title' => $product->getTranslation('meta_title', $locale),
                    'meta_description' => $product->getTranslation('meta_description', $locale),
                    'price' => $product->price,
                    'after_discount_price' => $product->after_discount_price,
                    'slug' => $product->slug,
                    'sku' => $product->sku,
                    'cost' => $product->cost,
                    'shipping_estimate_time' => $product->shipping_estimate_time,
                    'discount_start' => $product->discount_start,
                    'discount_end' => $product->discount_end,
                    'views' => $product->views,
                    'sales' => $product->sales,
                    'fake_average_rating' => $product->fake_average_rating,
                    'summary' => $product->getTranslation('summary', $locale),
                    'quantity' => $product->quantity,
                    'is_published' => $product->is_published,
                    'is_featured' => $product->is_featured,
                    'is_free_shipping' => $product->is_free_shipping,
                    'media' => [
                        'feature_product_image' => $product->getFeatureProductImageUrl(),
                        'second_feature_product_image' => $product->getSecondFeatureProductImageUrl(),
                    ],
                    'variants' => $product->productColors->map(fn($variant) => [
                        'id' => $variant->id,
                        'color_code' => optional($variant->color)->code,
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
                ];
            });

        return response()->json([
            'products' => $products,
        ]);
    }

}
