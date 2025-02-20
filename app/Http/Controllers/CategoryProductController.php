<?php

namespace App\Http\Controllers;

use App\Models\Category;

class CategoryProductController extends Controller
{
    public function show($slug)
    {
        $category = Category::query()->where('slug', $slug)->firstOrFail();
        return view('front.category-horizontal-filter2', compact('slug', 'category'));
    }
}
