<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;

class BlogController extends Controller
{
    // ---------- Helper (used only inside this file) ----------

    // The data the sidebar needs on every page: categories with their
    // post counts, and the newest posts for the "Popular" box.
    private function sidebarData()
    {
        return [
            // withCount adds a "posts_count" to each category, counting only published posts
            'categories' => Category::withCount(['posts' => function ($query) {
                $query->published();
            }])->orderBy('id')->get(),

            'popularPosts' => Post::published()->latest('published_at')->take(4)->get(),
        ];
    }

    // ---------- One method per page ----------

    // Home page  ( Route: / )
    public function home()
    {
        // with() loads each post's category and author in one go (faster)
        $pagedPosts = Post::published()
            ->with(['category', 'user'])
            ->latest('published_at')
            ->paginate(10);

        return view('welcome', array_merge(
            ['pagedPosts' => $pagedPosts],
            $this->sidebarData()
        ));
    }

    // All posts page  ( Route: /posts )
    public function allPosts()
    {
        $pagedPosts = Post::published()
            ->with(['category', 'user'])
            ->latest('published_at')
            ->paginate(10);

        return view('posts', array_merge(
            ['pagedPosts' => $pagedPosts],
            $this->sidebarData()
        ));
    }

    // Categories list page  ( Route: /categories )
    public function categoryIndex()
    {
        // A post to recommend from the whole blog
        $recommendedPost = Post::published()->oldest('published_at')->first();

        return view('categories.index', array_merge(
            ['recommendedPost' => $recommendedPost],
            $this->sidebarData()
        ));
    }

    // Posts in one category  ( Route: /show/{slug} )
    public function categoryShow($slug)
    {
        // Find the category by its slug, or show a 404 page
        $category = Category::where('slug', $slug)->firstOrFail();

        // Only this category's published posts, 10 per page
        $post_lists = $category->posts()
            ->published()
            ->with('user')
            ->latest('published_at')
            ->paginate(10);

        return view('categories.show', array_merge(
            [
                'name'       => $category->name,
                'category'   => $category,
                'post_lists' => $post_lists,
            ],
            $this->sidebarData()
        ));
    }

    // Single post page  ( Route: /post/{id} )
    public function showPost($id)
    {
        // Find the post, or show a 404 page
        $post = Post::published()
            ->with(['category', 'user'])
            ->findOrFail($id);

        // The next older and next newer posts, by publish date
        $previousPost = Post::published()
            ->where('published_at', '<', $post->published_at)
            ->latest('published_at')
            ->first();

        $nextPost = Post::published()
            ->where('published_at', '>', $post->published_at)
            ->oldest('published_at')
            ->first();

        return view('categories.post', array_merge(
            [
                'post'         => $post,
                'current'      => $post->category->slug,
                'previousPost' => $previousPost,
                'nextPost'     => $nextPost,
            ],
            $this->sidebarData()
        ));
    }
}