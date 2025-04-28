<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProductColor;
use App\Models\ProductColorSize;
use App\Models\Product;
use App\Models\Color;
use App\Models\Size;
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
     * @group Products
     *
     * Retrieves product details using its slug.
     *
     * @urlParam slug string required The slug of the product. Example: premium-headphones
     *
     * @response 200 {
     *   "product": {
     *     "id": 1,
     *     "name": "Premium Headphones",
     *     "slug": "premium-headphones",
     *     "price": 199.99,
     *     "description": "High-quality noise-cancelling headphones",
     *     "is_published": true
     *   }
     * }
     * @response 404 {
     *   "message": "Product not found."
     * }
     */
    public function showBySlug($slug)
    {
        $product = DB::table('products')
            ->where('slug', $slug)
            ->where('is_published', true)
            ->first();

        if (!$product) {
            return response()->json([
                'message' => 'Product not found.',
            ], 404);
        }

        return response()->json([
            'product' => $product,
        ]);
    }

    /**
     * Get Featured Products
     *
     * @group Products
     *
     * Retrieves a list of 3 featured products with their basic information,
     * available colors, sizes, and action links.
     *
     * @response 200 {
     *   "products": [
     *     {
     *       "id": 1,
     *       "name": "Premium Headphones",
     *       "category": "Electronics",
     *       "slug": "premium-headphones",
     *       "colors": ["Black", "White"],
     *       "sizes": {
     *         "Black": ["One Size"],
     *         "White": ["One Size"]
     *       },
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
    public function featured()
    {
        $products = DB::table('products')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->where('products.is_published', true)
            ->where('products.is_featured', true)
            ->select(
                'products.id',
                'products.name as product_name',
                'categories.name as category_name',
                'products.slug'
            )
            ->limit(3)
            ->get()
            ->map(function ($product) {
                $colors = DB::table('product_colors')
                    ->where('product_id', $product->id)
                    ->pluck('color');

                $sizesByColor = DB::table('product_sizes')
                    ->where('product_id', $product->id)
                    ->get()
                    ->groupBy('color');

                return [
                    'id' => $product->id,
                    'name' => $product->product_name,
                    'category' => $product->category_name,
                    'slug' => $product->slug,
                    'colors' => $colors,
                    'sizes' => $sizesByColor->map(function ($sizes) {
                        return $sizes->pluck('size');
                    }),
                    'actions' => [
                        'add_to_cart' => route('cart.add', ['product_id' => $product->id]),
                        'toggle_love' => route('wishlist.toggle', ['product_id' => $product->id]),
                        'compare' => route('compare.add', ['product_id' => $product->id]),
                        'view' => route('products.show', ['slug' => $product->slug]),
                    ],
                ];
            });

        return response()->json([
            'products' => $products,
        ]);
    }
}
