<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $q          = trim(str_replace(['%', '_'], '', (string) $request->query('q', '')));
        $status     = $request->query('status');
        $categoryId = $request->integer('category');

        $posts = Post::with(['blog', 'user', 'category'])
            ->when($q !== '', fn ($query) => $query->where(function ($w) use ($q) {
                $w->where('title', 'like', "%{$q}%")
                  ->orWhereHas('blog', fn ($b) => $b->where('title', 'like', "%{$q}%")
                                                     ->orWhere('subdomain', 'like', "%{$q}%"))
                  ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$q}%")
                                                     ->orWhere('email', 'like', "%{$q}%"));
            }))
            ->when($status === 'published', fn ($query) => $query->where('status', 'published'))
            ->when($status === 'draft', fn ($query) => $query->where('status', 'draft')->whereNull('moderated_at'))
            ->when($status === 'moderated', fn ($query) => $query->whereNotNull('moderated_at'))
            ->when($categoryId, fn ($query) => $query->where('category_id', $categoryId))
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        return view('admin.posts.index', [
            'posts'      => $posts,
            'categories' => Category::orderBy('name')->get(['id', 'name']),
            'q'          => $q,
            'status'     => $status,
            'categoryId' => $categoryId,
        ]);
    }

    public function show(Post $post)
    {
        $post->load(['blog', 'user', 'category']);

        return view('admin.posts.show', [
            'post'       => $post,
            'categories' => Category::orderBy('name')->get(['id', 'name']),
            'logs'       => ActivityLog::where('subject_type', $post->getMorphClass())
                                ->where('subject_id', $post->id)
                                ->latest('id')->take(10)->get(),
        ]);
    }

    public function update(Request $request, Post $post)
    {
        $data = $request->validate([
            'title'       => ['required', 'string', 'min:3', 'max:255'],
            'excerpt'     => ['nullable', 'string', 'max:300'],
            'category_id' => ['required', 'exists:categories,id'],
            'tags'        => ['nullable', 'string', 'max:300'],
        ]);

        $tags = collect(explode(',', $data['tags'] ?? ''))
            ->map(fn ($t) => Str::lower(trim($t)))
            ->filter()->unique()->take(6)->values()->all();

        $post->forceFill([
            'title'       => trim($data['title']),
            'excerpt'     => $data['excerpt'] ?? null,
            'category_id' => $data['category_id'],
            'tags'        => $tags,
        ])->save();

        ActivityLog::record('post.edited', 'An admin edited the details of "' . $post->title . '"', $post);

        return back()->with('success', 'Post details saved.');
    }

    public function unpublish(Request $request, Post $post)
    {
        if ($post->status !== 'published') {
            return back()->withErrors(['action' => 'That post is not published.']);
        }

        $data = $request->validate([
            'reason' => ['required', 'string', 'min:3', 'max:200'],
        ]);

        $post->forceFill([
            'status'            => 'draft',
            'moderated_at'      => now(),
            'moderation_reason' => trim($data['reason']),
        ])->save();

        ActivityLog::record('post.unpublished', '"' . $post->title . '" was unpublished by an admin: ' . $data['reason'], $post);

        return back()->with('success', '"' . $post->title . '" is unpublished and hidden from readers.');
    }

    public function restore(Post $post)
    {
        if (! $post->moderated_at) {
            return back()->withErrors(['action' => 'That post was not removed by an admin.']);
        }

        $post->forceFill([
            'status'            => 'published',
            'published_at'      => $post->published_at ?? now(),
            'moderated_at'      => null,
            'moderation_reason' => null,
        ])->save();

        ActivityLog::record('post.restored', '"' . $post->title . '" was restored and published again', $post);

        return back()->with('success', '"' . $post->title . '" is published again.');
    }

    public function clearFlag(Post $post)
    {
        $post->forceFill(['moderated_at' => null, 'moderation_reason' => null])->save();

        ActivityLog::record('post.flag_cleared', 'The owner may publish "' . $post->title . '" again', $post);

        return back()->with('success', 'The owner can now publish this post again.');
    }

    public function destroy(Post $post)
    {
        $title = $post->title;

        // remove the uploaded cover image (not image links)
        if ($post->featured_image && ! Str::startsWith($post->featured_image, ['http://', 'https://'])) {
            Storage::disk('public')->delete($post->featured_image);
        }

        $post->delete();   // the delete is logged automatically

        return redirect()->route('admin.posts')->with('success', '"' . $title . '" was deleted.');
    }
}