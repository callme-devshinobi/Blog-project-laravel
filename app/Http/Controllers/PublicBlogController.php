<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Category;
use Illuminate\Http\Request;

class PublicBlogController extends Controller
{
    // Sidebar data, limited to this blog's posts
    private function sidebarData(Blog $blog)
    {
        return [
            'categories' => Category::withCount(['posts' => function ($q) use ($blog) {
                $q->published()->where('blog_id', $blog->id);
            }])->orderBy('id')->get(),

            'popularPosts' => $blog->posts()->published()->latest('published_at')->take(4)->get(),
        ];
    }

    // Directory of all blogs  ( /blogs )
    public function index()
    {
        $blogs = Blog::with('user')
            ->whereHas('posts', fn ($q) => $q->published())
            ->withCount(['posts as published_count' => fn ($q) => $q->published()])
            ->latest()
            ->paginate(12);

        return view('blogs.index', compact('blogs'));
    }

    // Blog home  ( /@name )
    public function show(Blog $blog)
    {
        $pagedPosts = $blog->posts()->published()
            ->with(['category', 'user'])
            ->latest('published_at')
            ->paginate(10);

        return view('welcome', array_merge(
            ['blog' => $blog, 'pagedPosts' => $pagedPosts],
            $this->sidebarData($blog)
        ));
    }

    // All posts  ( /@name/posts )
    public function allPosts(Blog $blog)
    {
        $pagedPosts = $blog->posts()->published()
            ->with(['category', 'user'])
            ->latest('published_at')
            ->paginate(10);

        return view('posts', array_merge(
            ['blog' => $blog, 'pagedPosts' => $pagedPosts],
            $this->sidebarData($blog)
        ));
    }

    // Categories  ( /@name/categories )
    public function categoryIndex(Blog $blog)
    {
        $recommendedPost = $blog->posts()->published()->oldest('published_at')->first();

        // Date of the blogger's most recently updated published post
        $lastUpdated = $blog->posts()->published()->latest('updated_at')->value('updated_at');

        return view('categories.index', array_merge(
            [
                'blog'            => $blog,
                'recommendedPost' => $recommendedPost,
                'lastUpdated'     => $lastUpdated,
            ],
            $this->sidebarData($blog)
        ));
    }

    // This blogger's posts in one category  ( /@name/show/{slug} )
    public function categoryShow(Blog $blog, $slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();

        $post_lists = $blog->posts()->published()
            ->where('category_id', $category->id)
            ->with('user')
            ->latest('published_at')
            ->paginate(10);

        // Date of the latest updated post in this category, for this blogger
        $lastUpdated = $blog->posts()->published()
            ->where('category_id', $category->id)
            ->latest('updated_at')
            ->value('updated_at');

        return view('categories.show', array_merge(
            [
                'blog'        => $blog,
                'name'        => $category->name,
                'category'    => $category,
                'post_lists'  => $post_lists,
                'lastUpdated' => $lastUpdated,
            ],
            $this->sidebarData($blog)
        ));
    }

    // One post, only from this blog  ( /@name/post/{slug} )
    public function post(Blog $blog, $slug)
    {
        $post = $blog->posts()->published()
            ->where('slug', $slug)
            ->with(['category', 'user'])
            ->firstOrFail();

        $previousPost = $blog->posts()->published()
            ->where('published_at', '<', $post->published_at)
            ->latest('published_at')->first();

        $nextPost = $blog->posts()->published()
            ->where('published_at', '>', $post->published_at)
            ->oldest('published_at')->first();

        return view('categories.post', array_merge(
            [
                'blog'         => $blog,
                'post'         => $post,
                'current'      => $post->category->slug,
                'previousPost' => $previousPost,
                'nextPost'     => $nextPost,
            ],
            $this->sidebarData($blog)
        ));
    }

    public function about(Blog $blog)
{
    return view('about', [
        'blog'          => $blog,
        'postCount'     => $blog->posts()->published()->count(),
        'categoryCount' => $blog->posts()->published()->distinct()->count('category_id'),
        'since'         => $blog->created_at->year,
        'topics'        => Category::whereHas('posts', fn ($q) => $q->published()->where('blog_id', $blog->id))->orderBy('name')->pluck('name'),
    ]);
}

    public function contact(Blog $blog)
    {
        return view('contact', ['blog' => $blog]);
    }

    // Search inside one blog  ( /@name/search )
public function search(Blog $blog, Request $request)
{
    $q = mb_substr(trim((string) $request->query('q', '')), 0, 80);

    $results = mb_strlen($q) >= 2
        ? $blog->posts()->published()->search($q)
              ->with(['category', 'user'])
              ->latest('published_at')
              ->paginate(10)
              ->withQueryString()
        : null;

    return view('search', array_merge(
        ['blog' => $blog, 'q' => $q, 'results' => $results],
        $this->sidebarData($blog)
    ));
}
}