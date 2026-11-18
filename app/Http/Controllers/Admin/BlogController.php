<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Blog;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $q      = trim(str_replace(['%', '_'], '', (string) $request->query('q', '')));
        $filter = $request->query('filter');

        $blogs = Blog::with('user')
            ->withCount(['posts', 'posts as published_count' => fn ($p) => $p->where('status', 'published')])
            ->when($q !== '', fn ($query) => $query->where(function ($w) use ($q) {
                $w->where('title', 'like', "%{$q}%")
                  ->orWhere('subdomain', 'like', "%{$q}%")
                  ->orWhere('custom_domain', 'like', "%{$q}%")
                  ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$q}%")
                                                     ->orWhere('email', 'like', "%{$q}%"));
            }))
            ->when($filter === 'suspended', fn ($query) => $query->whereNotNull('suspended_at'))
            ->when($filter === 'domains', fn ($query) => $query->whereNotNull('custom_domain'))
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        return view('admin.blogs.index', compact('blogs', 'q', 'filter'));
    }

    public function show(Blog $blog)
    {
        $blog->load('user');

        return view('admin.blogs.show', [
            'blog'           => $blog,
            'posts'          => $blog->posts()->latest()->take(8)->get(),
            'publishedCount' => $blog->posts()->where('status', 'published')->count(),
            'draftCount'     => $blog->posts()->where('status', 'draft')->count(),
            'logs'           => ActivityLog::where('subject_type', $blog->getMorphClass())
                                    ->where('subject_id', $blog->id)
                                    ->latest('id')->take(10)->get(),
        ]);
    }

    public function update(Request $request, Blog $blog)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'min:2', 'max:60'],
            'bio'   => ['nullable', 'string', 'max:300'],
        ]);

        $blog->forceFill([
            'title' => trim($data['title']),
            'bio'   => $data['bio'] ?? null,
        ])->save();

        ActivityLog::record('blog.updated', 'An admin edited the title or bio of "' . $blog->title . '"', $blog);

        return back()->with('success', 'Blog details saved.');
    }

    public function suspend(Request $request, Blog $blog)
    {
        $blog->loadMissing('user');

        if ($blog->user?->is_admin) {
            return back()->withErrors(['action' => 'An admin\'s blog can\'t be suspended. Remove their admin rights first.']);
        }

        $data = $request->validate([
            'reason' => ['required', 'string', 'min:3', 'max:200'],
        ]);

        $blog->forceFill([
            'suspended_at'     => now(),
            'suspended_reason' => trim($data['reason']),
        ])->save();

        ActivityLog::record('blog.suspended', '"' . $blog->title . '" was suspended: ' . $data['reason'], $blog);

        return back()->with('success', '"' . $blog->title . '" is suspended. It and its posts are now hidden from readers.');
    }

    public function unsuspend(Blog $blog)
    {
        $blog->forceFill(['suspended_at' => null, 'suspended_reason' => null])->save();

        ActivityLog::record('blog.unsuspended', '"' . $blog->title . '" was reinstated', $blog);

        return back()->with('success', '"' . $blog->title . '" is visible again.');
    }

    public function removeDomain(Blog $blog)
    {
        $domain = $blog->custom_domain;

        $blog->forceFill([
            'custom_domain'             => null,
            'custom_domain_verified_at' => null,
            'domain_token'              => null,
        ])->save();

        ActivityLog::record('blog.domain_removed', 'Custom domain ' . $domain . ' was removed from "' . $blog->title . '"', $blog);

        return back()->with('success', 'The custom domain was removed.');
    }
}