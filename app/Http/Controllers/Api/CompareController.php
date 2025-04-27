<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CompareController extends Controller
{
    /**
     * Compare products by given IDs
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
