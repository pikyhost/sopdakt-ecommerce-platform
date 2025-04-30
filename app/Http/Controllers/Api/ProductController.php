<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProductColor;
use App\Models\ProductColorSize;
use App\Models\Product;
use App\Models\Color;
use App\Models\Size;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Get Product Colors and Sizes
     *
     * @group Products
     *
     * Retrieves all available colors and sizes for a specific product.
     *
     * @urlParam product integer required The ID of the product. Example: 42
     *
     * @response 200 {
     *   "colors": [
     *     {
     *       "color": {
     *         "id": 1,
     *         "name": "Red"
     *       },
     *       "sizes": [
     *         {
     *           "id": 1,
     *           "name": "S"
     *         },
     *         {
     *           "id": 2,
     *           "name": "M"
     *         }
     *       ]
     *     }
     *   ]
     * }
     * @response 404 {
     *   "message": "Product not found."
     * }
     */
    public function colorsSizes(Product $product)
    {
        // Get all product colors with their related sizes
        $productColors = ProductColor::with(['color', 'sizes'])
            ->where('product_id', $product->id)
            ->get();

        $colorsWithSizes = $productColors->map(function ($productColor) {
            return [
                'color' => [
                    'id' => $productColor->color->id,
                    'name' => $productColor->color->name,
                ],
                'sizes' => $productColor->sizes->map(function ($size) {
                    return [
                        'id' => $size->id,
                        'name' => $size->name,
                    ];
                }),
            ];
        });

        return response()->json([
            'colors' => $colorsWithSizes,
        ]);
    }

    /**
     * Get Product by Slug
     *
     * This endpoint returns a complete product record based on its slug. It includes all fields from the database
     * such as price, stock, rating, discount, metadata, and dynamic custom attributes. It also provides
     * action URLs for frontend interaction (e.g., add to cart, wishlist toggle).
     *
     * @group Products
     *
     * @urlParam slug string required The unique slug of the product. Example: premium-headphones
     *
     * @response 200 {
     *   "product": {
     *     "id": 1,
     *     "user_id": 3,
     *     "category_id": 2,
     *     "name": "Premium Headphones",
     *     "sku": "PH-2023",
     *     "price": 199,
     *     "after_discount_price": 149,
     *     "cost": 120,
     *     "shipping_estimate_time": "2-4 days",
     *     "description": "High-quality headphones with noise cancellation.",
     *     "slug": "premium-headphones",
     *     "meta_title": "Best Headphones 2023",
     *     "meta_description": "Wireless and durable audio gear",
     *     "discount_start": "2025-01-01T00:00:00",
     *     "discount_end": "2025-01-10T23:59:59",
     *     "views": 100,
     *     "sales": 30,
     *     "fake_average_rating": 5,
     *     "label_id": 1,
     *     "summary": "Best-in-class wireless headphones",
     *     "quantity": 20,
     *     "custom_attributes": {
     *       "Bluetooth": "5.0",
     *       "Battery Life": "30 hours"
     *     },
     *     "is_published": true,
     *     "is_featured": true,
     *     "is_free_shipping": false,
     *     "created_at": "2025-01-01T10:00:00",
     *     "updated_at": "2025-01-02T10:00:00",
     *     "actions": {
     *       "add_to_cart": "http://yourdomain.com/api/cart/1",
     *       "toggle_love": "http://yourdomain.com/api/wishlist/toggle/1",
     *       "compare": "http://yourdomain.com/api/compare/1",
     *       "view": "http://yourdomain.com/products/premium-headphones"
     *     }
     *   }
     * }
     * @response 404 {
     *   "message": "Product not found."
     * }
     */
    public function showBySlug(string $slug)
    {
        $product = Product::where('slug', $slug)
            ->where('is_published', true)
            ->first();

        if (!$product) {
            return response()->json([
                'message' => 'Product not found.',
            ], 404);
        }

        return response()->json([
            'product' => array_merge(
                $product->toArray(),
                ['actions' => $this->buildProductActions($product)]
            ),
        ]);
    }

    /**
     * Get Featured Products
     *
     * Returns a maximum of 3 published products marked as featured. Each product includes all database fields
     * and dynamic action URLs (e.g., add to cart, toggle wishlist, compare, view).
     *
     * @group Products
     *
     * @response 200 {
     *   "products": [
     *     {
     *       "id": 1,
     *       "name": "Premium Headphones",
     *       "price": 199,
     *       "after_discount_price": 149,
     *       "quantity": 20,
     *       "is_featured": true,
     *       "...": "... other fields ...",
     *       "actions": {
     *         "add_to_cart": "http://yourdomain.com/api/cart/1",
     *         "toggle_love": "http://yourdomain.com/api/wishlist/toggle/1",
     *         "compare": "http://yourdomain.com/api/compare/1",
     *         "view": "http://yourdomain.com/products/premium-headphones"
     *       }
     *     },
     *     {
     *       "id": 2,
     *       "name": "Wireless Earbuds",
     *       "price": 129,
     *       "is_featured": true,
     *       "...": "... other fields ...",
     *       "actions": {
     *         "add_to_cart": "http://yourdomain.com/api/cart/2",
     *         "toggle_love": "http://yourdomain.com/api/wishlist/toggle/2",
     *         "compare": "http://yourdomain.com/api/compare/2",
     *         "view": "http://yourdomain.com/products/wireless-earbuds"
     *       }
     *     }
     *   ]
     * }
     */
    public function featured()
    {
        $products = Product::where('is_published', true)
            ->where('is_featured', true)
            ->limit(3)
            ->get()
            ->map(function (Product $product) {
                return array_merge(
                    $product->toArray(),
                    ['actions' => $this->buildProductActions($product)]
                );
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
