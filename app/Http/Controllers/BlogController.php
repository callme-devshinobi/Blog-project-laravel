<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

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

    // Show the "new post" form  ( Route: GET /posts/create )
public function create()
{
    return view('create', [
        'categories' => Category::orderBy('name')->get(),
    ]);
}

// Save a new post  ( Route: POST /posts )
public function store(Request $request)
{
    // 1. Check the form. If anything is wrong, Laravel sends the visitor back
    //    to the form with error messages and keeps what they typed.
    $validated = $request->validate([
        'title'          => ['required', 'string', 'max:255'],
        'category_id'    => ['required', 'exists:categories,id'],
        'excerpt'        => ['required', 'string', 'max:300'],
        'body'           => ['required', 'string'],
        'featured_image' => ['nullable', 'image', 'max:2048'],   // up to 2 MB
        'image_alt'      => ['nullable', 'string', 'max:255'],
        'tags'           => ['nullable', 'string'],              // "laravel, php, tips"
        'status'         => ['required', 'in:draft,published'],
    ]);

    // 2. Make a slug from the title, adding -2, -3... if it already exists
    $slug = Str::slug($validated['title']);
    $original = $slug;
    $count = 2;
    while (Post::where('slug', $slug)->exists()) {
        $slug = $original . '-' . $count++;
    }

    // 3. Turn "laravel, php, tips" into ['laravel', 'php', 'tips']
    $tags = collect(explode(',', $validated['tags'] ?? ''))
        ->map(fn ($tag) => Str::lower(trim($tag)))
        ->filter()
        ->values()
        ->all();

    // 4. Turn plain text into safe HTML paragraphs. e() escapes anything
    //    that looks like HTML, so nobody can sneak a script into a post.
    $body = collect(preg_split('/\R{2,}/', trim($validated['body'])))
        ->map(fn ($paragraph) => '<p>' . nl2br(e($paragraph)) . '</p>')
        ->implode('');

    // 5. Estimate the reading time (about 200 words a minute)
    $readTime = max(1, (int) ceil(str_word_count(strip_tags($body)) / 200));

    // 6. Save the uploaded picture, if there is one
    $imagePath = $request->hasFile('featured_image')
        ? $request->file('featured_image')->store('posts', 'public')
        : null;

    // 7. Create the post
    $post = Post::create([
        // TEMPORARY: the logged-in user, or the first user if nobody is logged in
        'user_id'        => auth()->id() ?? User::first()->id,
        'category_id'    => $validated['category_id'],
        'title'          => $validated['title'],
        'slug'           => $slug,
        'excerpt'        => $validated['excerpt'],
        'body'           => $body,
        'featured_image' => $imagePath,
        'image_alt'      => $validated['image_alt'] ?? null,
        'tags'           => $tags,
        'read_time'      => $readTime,
        'status'         => $validated['status'],
        'published_at'   => $validated['status'] === 'published' ? now() : null,
    ]);

    // 8. A draft isn't public yet, so go back to the form instead of its page
    if ($post->status === 'draft') {
        return redirect()->route('posts.create')->with('success', 'Saved as a draft.');
    }

    return redirect('/post/' . $post->id)->with('success', 'Post published!');
}
public function drafts()
{
    $drafts = Post::where('status', 'draft')
        ->with('category')
        ->latest()
        ->get();

    return view('drafts', ['drafts' => $drafts]);
}
public function publish($id)
{
    // only drafts can be published, anything else gives a 404
    $post = Post::where('status', 'draft')->findOrFail($id);

    $post->update([
        'status'       => 'published',
        'published_at' => now(),
    ]);

    return redirect('/post/' . $post->id);
}
public function published()
{
    $posts = Post::published()
        ->with('category')
        ->latest('published_at')
        ->paginate(10);

    return view('published', ['posts' => $posts]);
}
public function destroy($id)
{
    $post = Post::findOrFail($id);

    // also remove the uploaded cover image from storage (not for image links)
    if ($post->featured_image && ! Str::startsWith($post->featured_image, ['http://', 'https://'])) {
        Storage::disk('public')->delete($post->featured_image);
    }

    $post->delete();

    return back()->with('success', 'Post deleted.');
}
}