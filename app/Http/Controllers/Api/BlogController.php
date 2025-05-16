<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    /**
     * Retrieve all active blog categories with their active blogs count.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @route GET /api/blogs/categories
     * @middleware api
     * @response 200 {
     *     "success": true,
     *     "data": [
     *         {
     *             "id": 1,
     *             "name": "Category Name",
     *             "slug": "category-slug",
     *             "blogs_count": 5
     *         },
     *         ...
     *     ],
     *     "message": "Blog categories retrieved successfully"
     * }
     */
    public function categories(Request $request)
    {
        $locale = app()->getLocale();

        $categories = BlogCategory::withCount(['blogs' => function ($query) {
            $query->where('is_active', true);
        }])
            ->where('is_active', true)
            ->whereNull('parent_id')
            ->get()
            ->map(function ($category) use ($locale) {
                return [
                    'id' => $category->id,
                    'name' => $category->getTranslation('name', $locale),
                    'slug' => $category->slug,
                    'blogs_count' => $category->blogs_count,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $categories,
            'message' => 'Blog categories retrieved successfully'
        ]);
    }

    /**
     * Retrieve a paginated list of active blogs.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @route GET /api/blogs
     * @middleware api
     * @queryParam per_page integer Number of blogs per page. Default: 10
     * @queryParam page integer Page number. Default: 1
     * @response 200 {
     *     "success": true,
     *     "data": {
     *         "data": [
     *             {
     *                 "id": 1,
     *                 "title": "Blog Title",
     *                 "slug": "blog-slug",
     *                 "excerpt": "Blog excerpt...",
     *                 "published_at": "2023-01-01",
     *                 "category": {
     *                     "id": 1,
     *                     "name": "Category Name"
     *                 },
     *                 "author": {
     *                     "id": 1,
     *                     "name": "Author Name"
     *                 },
     *                 "image_url": "http://example.com/image.jpg",
     *                 "likes_count": 10,
     *                 "tags": [
     *                     {"id": 1, "name": "Tag Name"},
     *                     ...
     *                 ]
     *             },
     *             ...
     *         ],
     *         "current_page": 1,
     *         "last_page": 5,
     *         ...
     *     },
     *     "message": "Blogs retrieved successfully"
     * }
     */
    public function index(Request $request)
    {
        $locale = app()->getLocale();

        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);

        $blogs = Blog::with(['category', 'author', 'tags'])
            ->where('is_active', true)
            ->orderBy('published_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        $transformedBlogs = $blogs->getCollection()->map(function ($blog) use ($locale) {
            return [
                'id' => $blog->id,
                'title' => $blog->getTranslation('title', $locale),
                'slug' => $blog->slug,
                'excerpt' => Str::limit(strip_tags($blog->getTranslation('content', $locale)), 150),
                'published_at' => $blog->published_at->format('Y-m-d'),
                'category' => $blog->category ? [
                    'id' => $blog->category->id,
                    'name' => $blog->category->getTranslation('name', $locale),
                ] : null,
                'author' => [
                    'id' => $blog->author->id,
                    'name' => $blog->author->name,
                ],
                'image_url' => $blog->getMainBlogImageUrl(),
                'likes_count' => $blog->likers()->count(),
                'tags' => $blog->tags->map(function ($tag) use ($locale) {
                    return [
                        'id' => $tag->id,
                        'name' => $locale == 'ar' ? $tag->name_ar : $tag->name_en,
                    ];
                }),
            ];
        });

        $paginatedResponse = $blogs->toArray();
        $paginatedResponse['data'] = $transformedBlogs;

        return response()->json([
            'success' => true,
            'data' => $paginatedResponse,
            'message' => 'Blogs retrieved successfully'
        ]);
    }

    /**
     * Retrieve a single blog post by its slug.
     *
     * @param Request $request
     * @param string $slug
     * @return \Illuminate\Http\JsonResponse
     *
     * @route GET /api/blogs/{slug}
     * @middleware api
     * @response 200 {
     *     "success": true,
     *     "data": {
     *         "id": 1,
     *         "title": "Blog Title",
     *         "slug": "blog-slug",
     *         "content": "Blog content...",
     *         "excerpt": "Blog excerpt...",
     *         "published_at": "2023-01-01",
     *         "category": {
     *             "id": 1,
     *             "name": "Category Name",
     *             "slug": "category-slug"
     *         },
     *         "author": {
     *             "id": 1,
     *             "name": "Author Name",
     *             "avatar": "http://example.com/storage/avatar.jpg"
     *         },
     *         "image_url": "http://example.com/image.jpg",
     *         "likes_count": 10,
     *         "is_liked": false,
     *         "tags": [
     *             {"id": 1, "name": "Tag Name"},
     *             ...
     *         ],
     *         "related_blogs": [
     *             {
     *                 "id": 2,
     *                 "title": "Related Blog Title",
     *                 "slug": "related-blog-slug",
     *                 "excerpt": "Related blog excerpt...",
     *                 "published_at": "2023-01-02",
     *                 "image_url": "http://example.com/related-image.jpg"
     *             },
     *             ...
     *         ]
     *     },
     *     "message": "Blog retrieved successfully"
     * }
     * @response 404 {
     *     "message": "Blog not found"
     * }
     */
    public function show(Request $request, $slug)
    {
        $locale = app()->getLocale();

        $blog = Blog::with(['category', 'author', 'tags', 'likers'])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $relatedBlogs = Blog::where('blog_category_id', $blog->blog_category_id)
            ->where('id', '!=', $blog->id)
            ->where('is_active', true)
            ->orderBy('published_at', 'desc')
            ->limit(3)
            ->get()
            ->map(function ($relatedBlog) use ($locale) {
                return [
                    'id' => $relatedBlog->id,
                    'title' => $relatedBlog->getTranslation('title', $locale),
                    'slug' => $relatedBlog->slug,
                    'excerpt' => Str::limit(strip_tags($relatedBlog->getTranslation('content', $locale)), 100),
                    'published_at' => $relatedBlog->published_at->format('Y-m-d'),
                    'image_url' => $relatedBlog->getMainBlogImageUrl(),
                ];
            });

        $response = [
            'id' => $blog->id,
            'title' => $blog->getTranslation('title', $locale),
            'slug' => $blog->slug,
            'content' => $blog->getTranslation('content', $locale),
            'excerpt' => Str::limit(strip_tags($blog->getTranslation('content', $locale)), 150),
            'published_at' => $blog->published_at->format('Y-m-d'),
            'category' => $blog->category ? [
                'id' => $blog->category->id,
                'name' => $blog->category->getTranslation('name', $locale),
                'slug' => $blog->category->slug,
            ] : null,
            'author' => [
                'id' => $blog->author->id,
                'name' => $blog->author->name,
                'avatar' => asset('storage/' . $blog->author->avatar_url)
            ],
            'image_url' => $blog->getMainBlogImageUrl(),
            'likes_count' => $blog->likers->count(),
            'is_liked' => $request->user() ? $blog->likers->contains($request->user()->id) : false,
            'tags' => $blog->tags->map(function ($tag) use ($locale) {
                return [
                    'id' => $tag->id,
                    'name' => $locale == 'ar' ? $tag->name_ar : $tag->name_en,
                ];
            }),
            'related_blogs' => $relatedBlogs,
        ];

        return response()->json([
            'success' => true,
            'data' => $response,
            'message' => 'Blog retrieved successfully'
        ]);
    }

    /**
     * Retrieve paginated blogs by category slug.
     *
     * @param Request $request
     * @param string $categorySlug
     * @return \Illuminate\Http\JsonResponse
     *
     * @route GET /api/blogs/category/{categorySlug}
     * @middleware api
     * @queryParam per_page integer Number of blogs per page. Default: 10
     * @queryParam page integer Page number. Default: 1
     * @response 200 {
     *     "success": true,
     *     "data": {
     *         "data": [
     *             {
     *                 "id": 1,
     *                 "title": "Blog Title",
     *                 "slug": "blog-slug",
     *                 "excerpt": "Blog excerpt...",
     *                 "published_at": "2023-01-01",
     *                 "author": {
     *                     "id": 1,
     *                     "name": "Author Name"
     *                 },
     *                 "image_url": "http://example.com/image.jpg",
     *                 "likes_count": 10,
     *                 "tags": [
     *                     {"id": 1, "name": "Tag Name"},
     *                     ...
     *                 ]
     *             },
     *             ...
     *         ],
     *         "category": {
     *             "id": 1,
     *             "name": "Category Name",
     *             "slug": "category-slug"
     *         },
     *         "current_page": 1,
     *         "last_page": 5,
     *         ...
     *     },
     *     "message": "Blogs by category retrieved successfully"
     * }
     * @response 404 {
     *     "message": "Category not found"
     * }
     */
    public function byCategory(Request $request, $categorySlug)
    {
        $locale = app()->getLocale();

        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);

        $category = BlogCategory::where('slug', $categorySlug)
            ->where('is_active', true)
            ->firstOrFail();

        $blogs = Blog::with(['author', 'tags'])
            ->where('blog_category_id', $category->id)
            ->where('is_active', true)
            ->orderBy('published_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        $transformedBlogs = $blogs->getCollection()->map(function ($blog) use ($locale) {
            return [
                'id' => $blog->id,
                'title' => $blog->getTranslation('title', $locale),
                'slug' => $blog->slug,
                'excerpt' => Str::limit(strip_tags($blog->getTranslation('content', $locale)), 150),
                'published_at' => $blog->published_at->format('Y-m-d'),
                'author' => [
                    'id' => $blog->author->id,
                    'name' => $blog->author->name,
                    'avatar' => asset('storage/' . $blog->author->avatar_url)
                ],
                'image_url' => $blog->getMainBlogImageUrl(),
                'likes_count' => $blog->likers()->count(),
                'tags' => $blog->tags->map(function ($tag) use ($locale) {
                    return [
                        'id' => $tag->id,
                        'name' => $locale == 'ar' ? $tag->name_ar : $tag->name_en,
                    ];
                }),
            ];
        });

        $paginatedResponse = $blogs->toArray();
        $paginatedResponse['data'] = $transformedBlogs;
        $paginatedResponse['category'] = [
            'id' => $category->id,
            'name' => $category->getTranslation('name', $locale),
            'slug' => $category->slug,
        ];

        return response()->json([
            'success' => true,
            'data' => $paginatedResponse,
            'message' => 'Blogs by category retrieved successfully'
        ]);
    }

    /**
     * Retrieve paginated blogs by tag ID.
     *
     * @param Request $request
     * @param int $tagId
     * @return \Illuminate\Http\JsonResponse
     *
     * @route GET /api/blogs/tag/{tagId}
     * @middleware api
     * @queryParam per_page integer Number of blogs per page. Default: 10
     * @queryParam page integer Page number. Default: 1
     * @response 200 {
     *     "success": true,
     *     "data": {
     *         "data": [
     *             {
     *                 "id": 1,
     *                 "title": "Blog Title",
     *                 "slug": "blog-slug",
     *                 "excerpt": "Blog excerpt...",
     *                 "published_at": "2023-01-01",
     *                 "author": {
     *                     "id": 1,
     *                     "name": "Author Name"
     *                 },
     *                 "category": {
     *                     "id": 1,
     *                     "name": "Category Name"
     *                 },
     *                 "image_url": "http://example.com/image.jpg",
     *                 "likes_count": 10
     *             },
     *             ...
     *         ],
     *         "tag": {
     *             "id": 1,
     *             "name": "Tag Name"
     *         },
     *         "current_page": 1,
     *         "last_page": 5,
     *         ...
     *     },
     *     "message": "Blogs by tag retrieved successfully"
     * }
     * @response 404 {
     *     "message": "Tag not found"
     * }
     */
    public function byTag(Request $request, $tagId)
    {
        $locale = app()->getLocale();

        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);

        $tag = Tag::where('id', $tagId)
            ->where('is_active', true)
            ->firstOrFail();

        $blogs = Blog::with(['author', 'category'])
            ->whereHas('tags', function ($query) use ($tagId) {
                $query->where('id', $tagId);
            })
            ->where('is_active', true)
            ->orderBy('published_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        $transformedBlogs = $blogs->getCollection()->map(function ($blog) use ($locale) {
            return [
                'id' => $blog->id,
                'title' => $blog->getTranslation('title', $locale),
                'slug' => $blog->slug,
                'excerpt' => Str::limit(strip_tags($blog->getTranslation('content', $locale)), 150),
                'published_at' => $blog->published_at->format('Y-m-d'),
                'author' => [
                    'id' => $blog->author->id,
                    'name' => $blog->author->name,
                ],
                'category' => $blog->category ? [
                    'id' => $blog->category->id,
                    'name' => $blog->category->getTranslation('name', $locale),
                ] : null,
                'image_url' => $blog->getMainBlogImageUrl(),
                'likes_count' => $blog->likers()->count(),
            ];
        });

        $paginatedResponse = $blogs->toArray();
        $paginatedResponse['data'] = $transformedBlogs;
        $paginatedResponse['tag'] = [
            'id' => $tag->id,
            'name' => $locale == 'ar' ? $tag->name_ar : $tag->name_en,
        ];

        return response()->json([
            'success' => true,
            'data' => $paginatedResponse,
            'message' => 'Blogs by tag retrieved successfully'
        ]);
    }

    /**
     * Retrieve popular blogs based on likes count.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @route GET /api/blogs/popular
     * @middleware api
     * @queryParam limit integer Number of blogs to return. Default: 5
     * @response 200 {
     *     "success": true,
     *     "data": [
     *         {
     *             "id": 1,
     *             "title": "Blog Title",
     *             "slug": "blog-slug",
     *             "excerpt": "Blog excerpt...",
     *             "published_at": "2023-01-01",
     *             "image_url": "http://example.com/image.jpg",
     *             "likes_count": 10
     *         },
     *         ...
     *     ],
     *     "message": "Popular blogs retrieved successfully"
     * }
     */
    public function popular(Request $request)
    {
        $locale = app()->getLocale();

        $limit = $request->input('limit', 5);

        $blogs = Blog::withCount('likers')
            ->where('is_active', true)
            ->orderBy('likers_count', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($blog) use ($locale) {
                return [
                    'id' => $blog->id,
                    'title' => $blog->getTranslation('title', $locale),
                    'slug' => $blog->slug,
                    'excerpt' => Str::limit(strip_tags($blog->getTranslation('content', $locale)), 100),
                    'published_at' => $blog->published_at->format('Y-m-d'),
                    'image_url' => $blog->getMainBlogImageUrl(),
                    'likes_count' => $blog->likers_count,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $blogs,
            'message' => 'Popular blogs retrieved successfully'
        ]);
    }

    /**
     * Retrieve recent blogs based on publication date.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @route GET /api/blogs/recent
     * @middleware api
     * @queryParam limit integer Number of blogs to return. Default: 5
     * @response 200 {
     *     "success": true,
     *     "data": [
     *         {
     *             "id": 1,
     *             "title": "Blog Title",
     *             "slug": "blog-slug",
     *             "excerpt": "Blog excerpt...",
     *             "published_at": "2023-01-01",
     *             "image_url": "http://example.com/image.jpg"
     *         },
     *         ...
     *     ],
     *     "message": "Recent blogs retrieved successfully"
     * }
     */
    public function recent(Request $request)
    {
        $locale = app()->getLocale();

        $limit = $request->input('limit', 5);

        $blogs = Blog::where('is_active', true)
            ->orderBy('published_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($blog) use ($locale) {
                return [
                    'id' => $blog->id,
                    'title' => $blog->getTranslation('title', $locale),
                    'slug' => $blog->slug,
                    'excerpt' => Str::limit(strip_tags($blog->getTranslation('content', $locale)), 100),
                    'published_at' => $blog->published_at->format('Y-m-d'),
                    'image_url' => $blog->getMainBlogImageUrl(),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $blogs,
            'message' => 'Recent blogs retrieved successfully'
        ]);
    }

    /**
     * Toggle like status for a blog post.
     *
     * @param Request $request
     * @param int $blogId
     * @return \Illuminate\Http\JsonResponse
     *
     * @route POST /api/blogs/{blogId}/like
     * @middleware api, auth:sanctum
     * @response 200 {
     *     "success": true,
     *     "data": {
     *         "likes_count": 11,
     *         "is_liked": true
     *     },
     *     "message": "Blog liked successfully"
     * }
     * @response 200 {
     *     "success": true,
     *     "data": {
     *         "likes_count": 10,
     *         "is_liked": false
     *     },
     *     "message": "Blog unliked successfully"
     * }
     * @response 401 {
     *     "success": false,
     *     "message": "Unauthenticated"
     * }
     * @response 404 {
     *     "message": "Blog not found"
     * }
     */
    public function toggleLike(Request $request, $blogId)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated'
            ], 401);
        }

        $blog = Blog::findOrFail($blogId);

        $liked = $blog->likers()->toggle($user->id);

        $likesCount = $blog->likers()->count();

        return response()->json([
            'success' => true,
            'data' => [
                'likes_count' => $likesCount,
                'is_liked' => $blog->likers()->where('user_id', $user->id)->exists(),
            ],
            'message' => $liked['attached'] ? 'Blog liked successfully' : 'Blog unliked successfully',
        ]);
    }

    /**
     * Search blogs by title or content.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @route GET /api/blogs/search
     * @middleware api
     * @queryParam query string required Search term
     * @queryParam per_page integer Number of blogs per page. Default: 10
     * @queryParam page integer Page number. Default: 1
     * @response 200 {
     *     "success": true,
     *     "data": {
     *         "data": [
     *             {
     *                 "id": 1,
     *                 "title": "Blog Title",
     *                 "slug": "blog-slug",
     *                 "excerpt": "Blog excerpt...",
     *                 "published_at": "2023-01-01",
     *                 "author": {
     *                     "id": 1,
     *                     "name": "Author Name"
     *                 },
     *                 "category": {
     *                     "id": 1,
     *                     "name": "Category Name"
     *                 },
     *                 "image_url": "http://example.com/image.jpg",
     *                 "likes_count": 10
     *             },
     *             ...
     *         ],
     *         "search_query": "search term",
     *         "current_page": 1,
     *         "last_page": 5,
     *         ...
     *     },
     *     "message": "Search results retrieved successfully"
     * }
     * @response 400 {
     *     "success": false,
     *     "message": "Search query is required"
     * }
     */
    public function search(Request $request)
    {
        $locale = app()->getLocale();

        $query = $request->input('query');
        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);

        if (!$query) {
            return response()->json([
                'success' => false,
                'message' => 'Search query is required'
            ], 400);
        }

        $blogs = Blog::with(['author', 'category'])
            ->where('is_active', true)
            ->where(function ($q) use ($query, $locale) {
                $q->whereRaw("JSON_EXTRACT(title, '$.{$locale}') LIKE ?", ["%{$query}%"])
                    ->orWhereRaw("JSON_EXTRACT(content, '$.{$locale}') LIKE ?", ["%{$query}%"]);
            })
            ->orderBy('published_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        $transformedBlogs = $blogs->getCollection()->map(function ($blog) use ($locale) {
            return [
                'id' => $blog->id,
                'title' => $blog->getTranslation('title', $locale),
                'slug' => $blog->slug,
                'excerpt' => Str::limit(strip_tags($blog->getTranslation('content', $locale)), 150),
                'published_at' => $blog->published_at->format('Y-m-d'),
                'author' => [
                    'id' => $blog->author->id,
                    'name' => $blog->author->name,
                ],
                'category' => $blog->category ? [
                    'id' => $blog->category->id,
                    'name' => $blog->category->getTranslation('name', $locale),
                ] : null,
                'image_url' => $blog->getMainBlogImageUrl(),
                'likes_count' => $blog->likers()->count(),
            ];
        });

        $paginatedResponse = $blogs->toArray();
        $paginatedResponse['data'] = $transformedBlogs;
        $paginatedResponse['search_query'] = $query;

        return response()->json([
            'success' => true,
            'data' => $paginatedResponse,
            'message' => 'Search results retrieved successfully'
        ]);
    }
}
