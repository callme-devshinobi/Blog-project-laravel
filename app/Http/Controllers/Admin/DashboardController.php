<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Blog;
use App\Models\Post;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'users'         => User::count(),
            'new_users'     => User::where('created_at', '>=', now()->subDays(7))->count(),
            'blogs'         => Blog::count(),
            'published'     => Post::where('status', 'published')->count(),
            'drafts'        => Post::where('status', 'draft')->count(),
            'domains'       => Blog::whereNotNull('custom_domain_verified_at')->count(),
        ];

        return view('admin.dashboard', [
            'stats'        => $stats,
            'latestUsers'  => User::latest()->take(5)->get(),
            'latestPosts'  => Post::with(['blog', 'user'])->latest()->take(5)->get(),
            'latestLogs'   => ActivityLog::with('user')->latest('id')->take(8)->get(),
        ]);
    }
}