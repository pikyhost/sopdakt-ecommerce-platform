<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class BlogController extends Controller
{
    /**
     * Retrieve all active blog categories with their children.
     *
     * This endpoint fetches all active blog categories that have no parent (top-level categories)
     * and includes their child categories. The response is localized based on the Accept-Language
     * header (defaults to English). The response includes category ID, name, description, and
     * nested children.
     *
     * @param Request $request The HTTP request containing the Accept-Language header.
     * @return \Illuminate\Http\JsonResponse JSON response containing the list of categories.
     */
    public function getCategories(Request $request)
    {
        $locale = $request->header('Accept-Language', 'en');
        App::setLocale($locale);

        $categories = BlogCategory::where('is_active', true)
            ->with('children')
            ->whereNull('parent_id')
            ->get()
            ->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'description' => $category->description,
                    'children' => $category->children->map(function ($child) {
                        return [
                            'id' => $child->id,
                            'name' => $child->name,
                            'description' => $child->description,
                        ];
                    })->toArray(),
                ];
            });

        return response()->json(['data' => $categories]);
    }

    /**
     * Retrieve a paginated list of published blogs with optional filters.
     *
     * This endpoint fetches published blogs, optionally filtered by category, tag, or search term.
     * The response is paginated (10 blogs per page) and includes blog details, related category,
     * author, tags, like status, and likes count. The response is localized based on the
     * Accept-Language header (defaults to English).
     *
     * @param Request $request The HTTP request containing query parameters (category_id, tag_id, search).
     * @return \Illuminate\Http\JsonResponse JSON response containing the paginated list of blogs.
     */
    public function getBlogs(Request $request)
    {
        $locale = $request->header('Accept-Language', 'en');
        App::setLocale($locale);

        $query = Blog::query()
            ->where('is_published', true)
            ->with(['category', 'author', 'tags', 'likers']);

        // Filter by category
        if ($request->has('category_id')) {
            $query->where('blog_category_id', $request->category_id);
        }

        // Filter by tag
        if ($request->has('tag_id')) {
            $query->whereHas('tags', function ($q) use ($request) {
                $q->where('tags.id', $request->tag_id);
            });
        }

        // Search by title or content
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search, $locale) {
                $q->where('title->' . $locale, 'like', '%' . $search . '%')
                    ->orWhere('content->' . $locale, 'like', '%' . $search . '%');
            });
        }

        // Pagination
        $blogs = $query->latest()->paginate(10);

        $formattedBlogs = $blogs->getCollection()->map(function ($blog) use ($locale) {
            return [
                'id' => $blog->id,
                'title' => $blog->getTranslation('title', $locale),
                'content' => $blog->getTranslation('content', $locale),
                'slug' => $blog->slug,
                'image' => $blog->getMainBlogImageUrl(),
                'category' => $blog->category ? [
                    'id' => $blog->category->id,
                    'name' => $blog->category->name,
                    'description' => $blog->category->description,
                ] : null,
                'author' => [
                    'id' => $blog->author->id,
                    'name' => $blog->author->name,
                ],
                'tags' => $blog->tags->map(function ($tag) {
                    return [
                        'id' => $tag->id,
                        'name' => $tag->name,
                    ];
                })->toArray(),
                'is_liked' => auth()->check() ? $blog->likers->contains(auth()->id()) : false,
                'likes_count' => $blog->likers->count(),
                'created_at' => $blog->created_at->toDateTimeString(),
            ];
        });

        return response()->json([
            'data' => $formattedBlogs,
            'current_page' => $blogs->currentPage(),
            'last_page' => $blogs->lastPage(),
            'per_page' => $blogs->perPage(),
            'total' => $blogs->total(),
        ]);
    }

    /**
     * Retrieve a single blog by its slug.
     *
     * This endpoint fetches a published blog by its slug, including related category, author,
     * tags, like status, and likes count. The response is localized based on the Accept-Language
     * header (defaults to English). Returns a 404 if the blog is not found.
     *
     * @param Request $request The HTTP request containing the Accept-Language header.
     * @param string $slug The slug of the blog to retrieve.
     * @return \Illuminate\Http\JsonResponse JSON response containing the blog details.
     */
    public function getBlogBySlug(Request $request, $slug)
    {
        $locale = $request->header('Accept-Language', 'en');
        App::setLocale($locale);

        $blog = Blog::where('slug', $slug)
            ->where('is_published', true)
            ->with(['category', 'author', 'tags', 'likers'])
            ->firstOrFail();

        $formattedBlog = [
            'id' => $blog->id,
            'title' => $blog->getTranslation('title', $locale),
            'content' => $blog->getTranslation('content', $locale),
            'slug' => $blog->slug,
            'image' => $blog->getMainBlogImageUrl(),
            'category' => $blog->category ? [
        'id' => $blog->category->id,
        'name' => $blog->category->name,
        'description' => $blog->category->description,
    ] : null,
            'author' => [
        'id' => $blog->author->id,
        'name' => $blog->author->name,
    ],
            'tags' => $blog->tags->map(function ($tag) {
        return [
            'id' => $tag->id,
            'name' => $tag->name,
        ];
    })->toArray(),
            'is_liked' => auth()->check() ? $blog->likers->contains(auth()->id()) : false,
            'likes_count' => $blog->likers->count(),
            'created_at' => $blog->created_at->toDateTimeString(),
        ];

        return response()->json(['data' => $formattedBlog]);
    }

    /**
     * Retrieve all active tags.
     *
     * This endpoint fetches all active tags, returning their ID and name. The response is
     * localized based on the Accept-Language header (defaults to English).
     *
     * @param Request $request The HTTP request containing the Accept-Language header.
     * @return \Illuminate\Http\JsonResponse JSON response containing the list of tags.
     */
    public function getTags(Request $request)
    {
        $locale = $request->header('Accept-Language', 'en');
        App::setLocale($locale);

        $tags = Tag::where('is_active', true)
            ->get()
            ->map(function ($tag) {
                return [
                    'id' => $tag->id,
                    'name' => $tag->name,
                ];
            });

        return response()->json(['data' => $tags]);
    }

    /**
     * Toggle like/unlike status for a blog.
     *
     * This endpoint allows an authenticated user to like or unlike a blog. If the user has
     * already liked the blog, it removes the like; otherwise, it adds a like. Requires Sanctum
     * authentication. Returns a 401 if unauthorized or a 404 if the blog is not found.
     *
     * @param Request $request The HTTP request.
     * @param int $blogId The ID of the blog to like/unlike.
     * @return \Illuminate\Http\JsonResponse JSON response with a success message.
     */
    public function toggleLike(Request $request, $blogId)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $blog = Blog::findOrFail($blogId);
        $liked = $blog->likers()->where('user_id', $user->id)->exists();

        if ($liked) {
            $blog->likers()->detach($user->id);
            $message = 'Blog unliked successfully';
        } else {
            $blog->likers()->attach($user->id);
            $message = 'Blog liked successfully';
        }

        return response()->json(['message' => $message]);
    }
}
