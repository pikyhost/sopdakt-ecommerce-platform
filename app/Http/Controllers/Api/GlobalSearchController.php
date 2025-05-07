<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\Category;
use App\Models\Discount;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

/**
 * @group Global Search
 *
 * API endpoints for performing a global search across products, categories, discounts, and blogs.
 */
class GlobalSearchController extends Controller
{
    /**
     * Perform a global search
     *
     * Searches for products, categories, discounts, and blogs based on a query string. The search is performed
     * on relevant fields (e.g., product name, category name, discount name, blog title) and returns a paginated
     * list of results. Results are limited per entity type to ensure balanced output. Only active discounts and
     * blogs are included, and products associated with matching labels are also returned.
     *
     * @authenticated
     * @bodyParam query string required The search term (minimum 2 characters). Example: phone
     * @bodyParam per_page integer optional Number of results per entity type (1-20, default: 6). Example: 10
     * @bodyParam page integer optional Page number for pagination (default: 1). Example: 1
     * @response 200 {
     *     "success": true,
     *     "data": {
     *         "products": [
     *             {
     *                 "id": 1,
     *                 "name": "Smartphone",
     *                 "slug": "smartphone",
     *                 "summary": "A high-end smartphone",
     *                 "labels": [
     *                     {"id": 1, "title": "Electronics"}
     *                 ]
     *             },
     *             ...
     *         ],
     *         "categories": [
     *             {
     *                 "id": 1,
     *                 "name": "Electronics",
     *                 "slug": "electronics"
     *             },
     *             ...
     *         ],
     *         "discounts": [
     *             {
     *                 "id": 1,
     *                 "name": "Summer Sale",
     *                 "description": "20% off electronics",
     *                 "value": 20.00,
     *                 "discount_type": "percentage"
     *             },
     *             ...
     *         ],
     *         "blogs": [
     *             {
     *                 "id": 1,
     *                 "title": "Top Phones of 2025",
     *                 "slug": "top-phones-2025",
     *                 "blog_category": {"id": 1, "name": "Tech"},
     *                 "author": {"id": 1, "name": "John Doe"}
     *             },
     *             ...
     *         ]
     *     },
     *     "meta": {
     *         "query": "phone",
     *         "total_results": 12,
     *         "pagination": {
     *             "per_page": 6,
     *             "current_page": 1,
     *             "last_page": 2,
     *             "total": 12
     *         }
     *     }
     * }
     * @response 422 {
     *     "message": "The given data was invalid.",
     *     "errors": {
     *         "query": ["The query field is required."]
     *     }
     * }
     * @response 429 {
     *     "message": "Too Many Requests"
     * }
     * @param Request $request
     * @return JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'query' => 'required|string|min:2|max:255',
            'per_page' => 'sometimes|integer|min:1|max:20',
            'page' => 'sometimes|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $query = trim($request->input('query'));
        $perPage = (int) $request->input('per_page', 6);
        $page = (int) $request->input('page', 1);
        $searchTerm = '%' . Str::replace(['%', '_'], ['\\%', '\\_'], $query) . '%';

        // Search Products (name, summary, description, or related labels)
        $products = Product::query()
            ->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', $searchTerm)
                    ->orWhere('summary', 'like', $searchTerm)
                    ->orWhere('description', 'like', $searchTerm);
            })
            ->orWhereHas('labels', function ($q) use ($searchTerm) {
                $q->where('title', 'like', $searchTerm);
            })
            ->with('labels:id,title')
            ->select('id', 'name', 'slug', 'summary')
            ->distinct()
            ->paginate($perPage, ['*'], 'products_page', $page);

        // Search Categories
        $categories = Category::where('name', 'like', $searchTerm)
            ->select('id', 'name', 'slug')
            ->paginate($perPage, ['*'], 'categories_page', $page);

        // Search Discounts
        $discounts = Discount::where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', now());
            })
            ->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', $searchTerm)
                    ->orWhere('description', 'like', $searchTerm);
            })
            ->select('id', 'name', 'description', 'value', 'discount_type')
            ->paginate($perPage, ['*'], 'discounts_page', $page);

        // Search Blogs
        $blogs = Blog::where('is_active', true)
            ->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', $searchTerm)
                    ->orWhere('content', 'like', $searchTerm);
            })
            ->with([
                'blogCategory' => fn ($q) => $q->select('id', 'name'),
                'author' => fn ($q) => $q->select('id', 'name'),
            ])
            ->select('id', 'title', 'slug')
            ->paginate($perPage, ['*'], 'blogs_page', $page);

        // Calculate total results
        $totalResults = $products->total() + $categories->total() + $discounts->total() + $blogs->total();

        return response()->json([
            'success' => true,
            'data' => [
                'products' => $products->items(),
                'categories' => $categories->items(),
                'discounts' => $discounts->items(),
                'blogs' => $blogs->items(),
            ],
            'meta' => [
                'query' => $query,
                'total_results' => $totalResults,
                'pagination' => [
                    'products' => [
                        'per_page' => $products->perPage(),
                        'current_page' => $products->currentPage(),
                        'last_page' => $products->lastPage(),
                        'total' => $products->total(),
                    ],
                    'categories' => [
                        'per_page' => $categories->perPage(),
                        'current_page' => $categories->currentPage(),
                        'last_page' => $categories->lastPage(),
                        'total' => $categories->total(),
                    ],
                    'discounts' => [
                        'per_page' => $discounts->perPage(),
                        'current_page' => $discounts->currentPage(),
                        'last_page' => $discounts->lastPage(),
                        'total' => $discounts->total(),
                    ],
                    'blogs' => [
                        'per_page' => $blogs->perPage(),
                        'current_page' => $blogs->currentPage(),
                        'last_page' => $blogs->lastPage(),
                        'total' => $blogs->total(),
                    ],
                ],
            ],
        ]);
    }
}
