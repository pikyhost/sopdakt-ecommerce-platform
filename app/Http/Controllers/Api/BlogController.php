<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Mail\Markdown;
use Illuminate\Support\Facades\Auth;

class BlogController extends Controller
{
    public function getTags()
    {
        $locale = app()->getLocale();

        $tags = Tag::select('id', 'name_en', 'name_ar')
            ->limit(12)
            ->get()
            ->map(function ($tag) use ($locale) {
                return [
                    'id' => $tag->id,
                    'name' => $locale === 'ar' ? $tag->name_ar : $tag->name_en,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $tags,
            'message' => 'Tags retrieved successfully'
        ]);
    }
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
            $excerptMarkdown = $blog->getTranslation('content', $locale);
            $excerptHtml = Markdown::parse($excerptMarkdown)->toHtml();

            return [
                'id' => $blog->id,
                'title' => $blog->getTranslation('title', $locale),
                'slug' => $blog->slug,
                'excerpt' => $excerptHtml,
                'published_at' => $blog->published_at->format('Y-m-d'),
                'category' => $blog->category ? [
                    'id' => $blog->category->id,
                    'name' => $blog->category->getTranslation('name', $locale),
                ] : null,
                'author' => [
                    'id' => $blog->author->id,
                    'name' => $blog->author->name,
                    'desc_for_comment' => $blog->author->desc_for_comment ?? 'Author',
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

        return response()->json([
            'success' => true,
            'data' => $paginatedResponse,
            'message' => 'Blogs retrieved successfully'
        ]);
    }

    public function show(Request $request, $slug)
    {
        $locale = app()->getLocale();

        $blog = Blog::with(['category', 'author', 'tags', 'likers'])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        // Convert Markdown to HTML for main blog
        $contentMarkdown = $blog->getTranslation('content', $locale);
        $contentHtml = Markdown::parse($contentMarkdown)->toHtml();

        // Fetch and convert related blogs
        $relatedBlogs = Blog::where('blog_category_id', $blog->blog_category_id)
            ->where('id', '!=', $blog->id)
            ->where('is_active', true)
            ->orderBy('published_at', 'desc')
            ->limit(3)
            ->get()
            ->map(function ($relatedBlog) use ($locale) {
                $excerptMarkdown = $relatedBlog->getTranslation('content', $locale);
                $excerptHtml = Markdown::parse($excerptMarkdown)->toHtml();

                return [
                    'id' => $relatedBlog->id,
                    'title' => $relatedBlog->getTranslation('title', $locale),
                    'slug' => $relatedBlog->slug,
                    'excerpt' => $excerptHtml,
                    'published_at' => $relatedBlog->published_at->format('Y-m-d'),
                    'image_url' => $relatedBlog->getMainBlogImageUrl(),
                ];
            });

        // Main blog response
        $response = [
            'id' => $blog->id,
            'title' => $blog->getTranslation('title', $locale),
            'slug' => $blog->slug,
            'content' => $contentHtml,
            'published_at' => $blog->published_at->format('Y-m-d'),
            'category' => $blog->category ? [
                'id' => $blog->category->id,
                'name' => $blog->category->getTranslation('name', $locale),
                'slug' => $blog->category->slug,
            ] : null,
            'author' => [
                'id' => $blog->author->id,
                'name' => $blog->author->name,
                'desc_for_comment' => $blog->author->desc_for_comment ?? 'Author',
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
            $excerptMarkdown = $blog->getTranslation('content', $locale);
            $excerptHtml = Markdown::parse($excerptMarkdown)->toHtml();

            return [
                'id' => $blog->id,
                'title' => $blog->getTranslation('title', $locale),
                'slug' => $blog->slug,
                'excerpt' => $excerptHtml,
                'published_at' => $blog->published_at->format('Y-m-d'),
                'author' => [
                    'id' => $blog->author->id,
                    'name' => $blog->author->name,
                    'desc_for_comment' => $blog->author->desc_for_comment ?? 'Author',
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

    public function byTag(Request $request, $tagId)
    {
        $locale = app()->getLocale();

        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);

        $tag = Tag::findOrFail($tagId);

        $blogs = Blog::with(['author', 'category'])
            ->whereHas('tags', function ($query) use ($tagId) {
                $query->where('tags.id', $tagId); // ← Fixed here
            })
            ->where('is_active', true)
            ->orderBy('published_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        $transformedBlogs = $blogs->getCollection()->map(function ($blog) use ($locale) {
            $excerptMarkdown = $blog->getTranslation('content', $locale);
            $excerptHtml = Markdown::parse($excerptMarkdown)->toHtml();

            return [
                'id' => $blog->id,
                'title' => $blog->getTranslation('title', $locale),
                'slug' => $blog->slug,
                'excerpt' => $excerptHtml,
                'published_at' => $blog->published_at->format('Y-m-d'),
                'author' => [
                    'id' => $blog->author->id,
                    'name' => $blog->author->name,
                    'desc_for_comment' => $blog->author->desc_for_comment ?? 'Author',
                    'avatar' => asset('storage/' . $blog->author->avatar_url)
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
                $excerptMarkdown = $blog->getTranslation('content', $locale);
                $excerptHtml = Markdown::parse($excerptMarkdown)->toHtml();

                return [
                    'id' => $blog->id,
                    'title' => $blog->getTranslation('title', $locale),
                    'slug' => $blog->slug,
                    'excerpt' => $excerptHtml,
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

    public function recent(Request $request)
    {
        $locale = app()->getLocale();

        $limit = $request->input('limit', 5);

        $blogs = Blog::where('is_active', true)
            ->orderBy('published_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($blog) use ($locale) {
                $excerptMarkdown = $blog->getTranslation('content', $locale);
                $excerptHtml = Markdown::parse($excerptMarkdown)->toHtml();

                return [
                    'id' => $blog->id,
                    'title' => $blog->getTranslation('title', $locale),
                    'slug' => $blog->slug,
                    'excerpt' => $excerptHtml,
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

    public function toggleLike(Request $request, $blogId)
    {
        $user = Auth::guard('sanctum')->user();

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
            $excerptMarkdown = $blog->getTranslation('content', $locale);
            $excerptHtml = Markdown::parse($excerptMarkdown)->toHtml();

            return [
                'id' => $blog->id,
                'title' => $blog->getTranslation('title', $locale),
                'slug' => $blog->slug,
                'excerpt' => $excerptHtml,
                'published_at' => $blog->published_at->format('Y-m-d'),
                'author' => [
                    'id' => $blog->author->id,
                    'name' => $blog->author->name,
                    'desc_for_comment' => $blog->author->desc_for_comment ?? 'Author',
                    'avatar' => asset('storage/' . $blog->author->avatar_url)
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
