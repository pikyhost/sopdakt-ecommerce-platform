<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

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

        $locale = App::getLocale();

        $products = DB::table('products')
            ->whereIn('id', $request->product_ids)
            ->select([
                'id',
                DB::raw("JSON_UNQUOTE(JSON_EXTRACT(name, '$.\"$locale\"')) as name"),
                DB::raw("JSON_UNQUOTE(JSON_EXTRACT(description, '$.\"$locale\"')) as description"),
                DB::raw("JSON_UNQUOTE(JSON_EXTRACT(meta_title, '$.\"$locale\"')) as meta_title"),
                DB::raw("JSON_UNQUOTE(JSON_EXTRACT(meta_description, '$.\"$locale\"')) as meta_description"),
                'price',
                'after_discount_price',
                'slug',
                'sku',
                'cost',
                'shipping_estimate_time',
                'discount_start',
                'discount_end',
                'views',
                'sales',
                'fake_average_rating',
                'summary',
                'quantity',
                'custom_attributes',
                'is_published',
                'is_featured',
                'is_free_shipping',
            ])
            ->get()
            ->map(function ($product) {
                $product->custom_attributes = json_decode($product->custom_attributes, true);
                return $product;
            });

        return response()->json([
            'products' => $products,
        ]);
    }
}
