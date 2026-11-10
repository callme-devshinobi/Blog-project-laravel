<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Blog;
use Illuminate\Support\Facades\View;

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
        View::composer('*', function ($view) {
            $onCustomDomain = app()->bound('customDomainBlog');
            $blog = $onCustomDomain ? app('customDomainBlog') : request()->route('blog');
            $isBlog = $blog instanceof Blog;
        
            $view->with('currentBlog', $isBlog ? $blog : null);
            $view->with('base', ($isBlog && ! $onCustomDomain) ? '/@' . $blog->subdomain : '');
         // empty on the main site; the main site's address on a custom domain
            $view->with('mainUrl', $onCustomDomain ? rtrim(config('app.url'), '/') : '');
        });
    }
}
