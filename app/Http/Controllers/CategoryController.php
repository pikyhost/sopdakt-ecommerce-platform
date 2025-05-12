<?php

namespace App\Http\Controllers;

use App\Models\Category;

class CategoryController extends Controller
{
    /**
     * Show Category with Products
     *
     * @group Categories
     *
     * Display a category and its associated products.
     *
     * @urlParam slug string required The slug of the category.
     *
     * @response 200 {
     *   "success": true,
     *   "data": {
     *     "id": 1,
     *     "name": "Electronics",
     *     "slug": "electronics",
     *     "description": "Category description...",
     *     "products": [
     *       {
     *         "id": 101,
     *         "name": "Smartphone",
     *         "price": 299.99
     *       }
     *     ]
     *   }
     * }
     */
    public function showWithProducts(Category $category)
    {
        $category->load('products');

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
                'description' => $category->description,
                'products' => $category->products,
            ],
        ]);
    }
}
