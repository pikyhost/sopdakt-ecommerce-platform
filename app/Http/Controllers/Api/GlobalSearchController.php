<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\Category;
use App\Models\Discount;
use App\Models\Label;
use App\Models\Product;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class GlobalSearchController extends Controller
{
    /**
     * Global Search API
     *
     * Search across products, categories, blogs, discounts, and tags.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @queryParam query string required The search term (min: 2, max: 255 characters). Example: phone
     *
     * @response 200 {
     *     "data": {
     *         "products": [
     *             {
     *                 "id": 1,
     *                 "name": "Product Name",
     *                 "summary": "Product Summary",
     *                 "labels": ["label1", "label2"]
     *             },
     *             ...
     *         ],
     *         "categories": [
     *             {
     *                 "id": 1,
     *                 "name": "Category Name"
     *             },
     *             ...
     *         ],
     *         "blogs": [
     *             {
     *                 "id": 1,
     *                 "title": "Blog Title",
     *                 "slug": "blog-slug",
     *                 "published_at": "2025-05-07T00:00:00Z"
     *             },
     *             ...
     *         ],
     *         "discounts": [
     *             {
     *                 "id": 1,
     *                 "name": "Discount Name",
     *                 "description": "Discount Description",
     *                 "discount_type": "percentage",
     *                 "value": 10.00
     *             },
     *             ...
     *         ],
     *         "tags": [
     *             {
     *                 "id": 1,
     *                 "name_ar": "تاغ",
     *                 "name_en": "Tag"
     *             },
     *             ...
     *         ]
     *     },
     *     "meta": {
     *         "timestamp": "2025-05-07T12:34:56Z",
     *         "query": "search term"
     *     }
     * }
     * @response 422 {
     *     "message": "The query field must be at least 2 characters.",
     *     "errors": {
     *         "query": ["The query field must be at least 2 characters."]
     *     }
     * }
     */
    public function search(Request $request)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'query' => 'required|string|min:2|max:255',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $query = $request->query('query');
        $searchTerm = '%' . $query . '%';

        // Search Products
        $products = Product::query()
            ->select('id', 'name', 'summary')
            ->where('name', 'like', $searchTerm)
            ->orWhere('summary', 'like', $searchTerm)
            ->orWhere('description', 'like', $searchTerm)
            ->with(['labels' => function ($query) {
                $query->select('labels.id', 'labels.title');
            }])
            ->limit(10)
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'summary' => $product->summary,
                    'labels' => $product->labels->pluck('title')->toArray(),
                ];
            });

        // Search Categories
        $categories = Category::query()
            ->select('id', 'name')
            ->where('name', 'like', $searchTerm)
            ->limit(10)
            ->get()
            ->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                ];
            });

        // Search Blogs
        $blogs = Blog::query()
            ->select('id', 'title', 'slug', 'published_at')
            ->where('is_active', true)
            ->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', $searchTerm)
                    ->orWhere('content', 'like', $searchTerm);
            })
            ->limit(10)
            ->get()
            ->map(function ($blog) {
                return [
                    'id' => $blog->id,
                    'title' => $blog->title,
                    'slug' => $blog->slug,
                    'published_at' => $blog->published_at?->toISOString(),
                ];
            });

        // Search Discounts
        $discounts = Discount::query()
            ->select('id', 'name', 'description', 'discount_type', 'value')
            ->where('is_active', true)
            ->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', $searchTerm)
                    ->orWhere('description', 'like', $searchTerm);
            })
            ->where(function ($q) {
                $q->whereNull('ends_at')
                    ->orWhere('ends_at', '>=', now());
            })
            ->limit(10)
            ->get()
            ->map(function ($discount) {
                return [
                    'id' => $discount->id,
                    'name' => $discount->name,
                    'description' => $discount->description,
                    'discount_type' => $discount->discount_type,
                    'value' => $discount->value,
                ];
            });

        // Search Labels and get related Products
        $labelIds = Label::query()
            ->where('title', 'like', $searchTerm)
            ->pluck('id');

        $productsFromLabels = Product::query()
            ->select('id', 'name', 'summary')
            ->whereHas('labels', function ($query) use ($labelIds) {
                $query->whereIn('labels.id', $labelIds);
            })
            ->with(['labels' => function ($query) {
                $query->select('labels.id', 'labels.title');
            }])
            ->limit(10)
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'summary' => $product->summary,
                    'labels' => $product->labels->pluck('title')->toArray(),
                ];
            });

        // Search Tags
        $tags = Tag::query()
            ->select('id', 'name_ar', 'name_en')
            ->where('is_active', true)
            ->where(function ($q) use ($searchTerm) {
                $q->where('name_ar', 'like', $searchTerm)
                    ->orWhere('name_en', 'like', $searchTerm);
            })
            ->limit(10)
            ->get()
            ->map(function ($tag) {
                return [
                    'id' => $tag->id,
                    'name_ar' => $tag->name_ar,
                    'name_en' => $tag->name_en,
                ];
            });

        // Search Blogs by Tags
        $tagIds = Tag::query()
            ->where('is_active', true)
            ->where(function ($q) use ($searchTerm) {
                $q->where('name_ar', 'like', $searchTerm)
                    ->orWhere('name_en', 'like', $searchTerm);
            })
            ->pluck('id');

        $blogsFromTags = Blog::query()
            ->select('id', 'title', 'slug', 'published_at')
            ->where('is_active', true)
            ->whereHas('tags', function ($query) use ($tagIds) {
                $query->whereIn('tags.id', $tagIds);
            })
            ->limit(10)
            ->get()
            ->map(function ($blog) {
                return [
                    'id' => $blog->id,
                    'title' => $blog->title,
                    'slug' => $blog->slug,
                    'published_at' => $blog->published_at?->toISOString(),
                ];
            });

        // Merge and deduplicate products
        $allProducts = $products->merge($productsFromLabels)->unique('id');

        // Merge and deduplicate blogs
        $allBlogs = $blogs->merge($blogsFromTags)->unique('id');

        return response()->json([
            'data' => [
                'products' => $allProducts->values()->toArray(),
                'categories' => $categories->values()->toArray(),
                'blogs' => $allBlogs->values()->toArray(),
                'discounts' => $discounts->values()->toArray(),
                'tags' => $tags->values()->toArray(),
            ],
            'meta' => [
                'timestamp' => now()->toISOString(),
                'query' => $query,
            ],
        ], 200);
    }
}
