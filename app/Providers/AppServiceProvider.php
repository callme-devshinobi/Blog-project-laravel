<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Blog;
use Illuminate\Support\Facades\View;
use App\Models\ActivityLog;
use App\Models\Post;
use App\Models\User;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Event;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // ---------- Shared view data ----------
        View::composer('*', function ($view) {
            $onCustomDomain = app()->bound('customDomainBlog');
            $blog = $onCustomDomain ? app('customDomainBlog') : request()->route('blog');
            $isBlog = $blog instanceof Blog;

            $view->with('currentBlog', $isBlog ? $blog : null);
            $view->with('base', ($isBlog && ! $onCustomDomain) ? '/@' . $blog->subdomain : '');

            // empty on the main site; the main site's address on a custom domain
            $view->with('mainUrl', $onCustomDomain ? rtrim(config('app.url'), '/') : '');
        });

        // ---------- Activity log ----------
        Event::listen(Login::class, fn (Login $e) =>
            ActivityLog::record('auth.login', $e->user->name . ' signed in', $e->user));

        User::created(fn (User $u) =>
            ActivityLog::record('user.created', 'New account: ' . $u->name, $u));

        Blog::created(fn (Blog $b) =>
            ActivityLog::record('blog.created', 'New blog: ' . $b->title . ' (@' . $b->subdomain . ')', $b));

        Post::created(fn (Post $p) =>
            ActivityLog::record('post.created', 'Post "' . $p->title . '" created as ' . $p->status, $p));

        Post::updated(function (Post $p) {
            if ($p->wasChanged('status')) {
                ActivityLog::record('post.status', 'Post "' . $p->title . '" is now ' . $p->status, $p);
            }
        });

        Post::deleted(fn (Post $p) =>
            ActivityLog::record('post.deleted', 'Post "' . $p->title . '" was deleted', $p));
    }
}