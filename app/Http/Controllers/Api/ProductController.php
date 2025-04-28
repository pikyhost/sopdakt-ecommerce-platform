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
     * Show product by slug
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
     * Get 3 featured products
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
