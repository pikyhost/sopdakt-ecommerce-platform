<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function show($slug)
    {
        // Find product and load only approved ratings
        $product = Product::where('slug', $slug)
            ->with(['colors', 'sizes', 'labels', 'media', 'ratings' => function ($query) {
                $query->approved()->with('user');
            }])
            ->firstOrFail();

        return view('front.product-sticky-info', compact('product'));
    }
}
