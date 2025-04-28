<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CompareController extends Controller
{
    /**
     * Compare Products
     *
     * @group Products
     *
     * Compares multiple products side-by-side by their IDs.
     *
     * @bodyParam product_ids array required Array of product IDs to compare (minimum 2 products). Example: [1, 2, 3]
     * @bodyParam product_ids.* integer required Product IDs must exist in the database.
     *
     * @response 200 {
     *   "products": [
     *     {
     *       "id": 1,
     *       "name": "Premium Headphones",
     *       "price": 199.99,
     *       "description": "Noise-cancelling wireless headphones",
     *       "features": "Bluetooth 5.0, 30hr battery"
     *     },
     *     {
     *       "id": 2,
     *       "name": "Wireless Earbuds",
     *       "price": 129.99,
     *       "description": "Compact true wireless earbuds",
     *       "features": "IPX4 waterproof, 24hr battery"
     *     }
     *   ]
     * }
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

        $products = DB::table('products')
            ->whereIn('id', $request->product_ids)
            ->get();

        return response()->json([
            'products' => $products,
        ]);
    }
}
