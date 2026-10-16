<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    // ---------- Helper methods (used only inside this file) ----------

    // Your categories list, written once instead of in every route.
    // "private" means only this class can use it.
    private function getCategories()
    {
        return [
            ["id" => 1, "name" => "Laravel", "slug" => "laravel",
             "description" => "Framework tutorials, project builds, Eloquent tips and everything PHP artisan.",
             "post_count" => 0, "latest" => "Laravel 12 guide", "icon" => "⬡"],
            ["id" => 2, "name" => "Web Development", "slug" => "web-dev",
             "description" => "Full-stack building blocks — HTML, CSS, tooling, deployment and modern workflows",
             "post_count" => 0, "latest" => "Blog series Pt. 1", "icon" => "◧"],
            ["id" => 3, "name" => "JavaScript", "slug" => "js",
             "description" => "Vanilla JS fundamentals, browser APIs, patterns and the occasional framework note",
             "post_count" => 0, "latest" => "Async deep-dive", "icon" => "JS"],
            ["id" => 4, "name" => "Tools & Resources", "slug" => "tools",
             "description" => "VS Code extensions, packages, CLI tools and setups that make dev life easier.",
             "post_count" => 0, "latest" => "10 VS Code picks", "icon" => "⚒"],
            ["id" => 5, "name" => "Productivity", "slug" => "productivity",
             "description" => "Habits, routines and systems that keep you consistent as a developer.",
             "post_count" => 0, "latest" => "5 dev habits", "icon" => "✦"],
            ["id" => 6, "name" => "Tech News", "slug" => "news",
             "description" => "What matters in the dev world — releases, updates and honest takes.",
             "post_count" => 0, "latest" => "Laravel 12 released", "icon" => "◔"],
        ];
    }

    // Takes the categories and the posts, and fills in the real post count
    // for each category (same idea as the map() you had in your '/' route).
    private function addCounts($categories, $posts)
    {
        return collect($categories)->map(function ($cat) use ($posts) {
            $cat['post_count'] = collect($posts)->where('category', $cat['name'])->count();
            return $cat;
        });
    }

    // ---------- One method per page ----------

    // Home page  ( Route: / )
    public function home()
    {
        // Get every post from the database, newest first
        $posts = Blog::latest('published_at')->get();

        $pagedPosts = Blog::latest('published_at')->paginate(10);

        $categories = $this->addCounts($this->getCategories(), $posts);

        return view('welcome', [
            'posts'      => $posts,
            'pagedPosts' => $pagedPosts,
            'categories' => $categories,
        ]);
    }

    // Categories list page  ( Route: /categories )
    public function categoryIndex()
    {
        $posts = Blog::latest('published_at')->get();
        $categories = $this->addCounts($this->getCategories(), $posts);
        // A post to recommend from the whole blog
        $recommendedPost = Blog::oldest('published_at')->first();


        return view('categories.index', [
            'categories' => $categories,
            'posts'      => $posts,
            'recommendedPost' => $recommendedPost,
        ]);
    }

    // Posts in one category  ( Route: /show/{name} )
    public function categoryShow($name)
    {
        $posts = Blog::latest('published_at')->get();

        $categories = $this->addCounts($this->getCategories(), $posts);

        // Find the one category whose name matches the URL
        $category = collect($categories)->firstWhere('name', $name);

        // If someone types a category that doesn't exist, show a 404 page
        abort_if(! $category, 404);

        // Ask the database only for posts in this category
        $post_lists = Blog::where('category', $name)
        ->latest('published_at')
        ->paginate(10);
        return view('categories.show', [
        'name'       => $name,
        'category'   => $category,
        'post_lists' => $post_lists,
        'categories' => $categories,
        'posts'      => $posts,
        ]);
    }

    // Single post page  ( Route: /post/{id} )
    public function showPost($id)
    {
        $post = Blog::findOrFail($id);
    
        $posts = Blog::latest('published_at')->get();
    
        $categories = $this->addCounts($this->getCategories(), $posts);
    
        // Find this post's category so the sidebar can highlight it
        $currentCategory = collect($categories)->firstWhere('name', $post->category);
    
        $previousPost = Blog::where('id', '<', $post->id)->orderBy('id', 'desc')->first();
        $nextPost     = Blog::where('id', '>', $post->id)->orderBy('id', 'asc')->first();
    
        return view('categories.post', [
            'post'         => $post,
            'posts'        => $posts,
            'categories'   => $categories,
            'current'      => $currentCategory['slug'] ?? null,
            'previousPost' => $previousPost,
            'nextPost'     => $nextPost,
        ]);
    }
}