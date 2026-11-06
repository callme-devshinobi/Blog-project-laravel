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
            $blog = request()->route('blog');
            $isBlog = $blog instanceof Blog;
        
            $view->with('currentBlog', $isBlog ? $blog : null);
            $view->with('base', $isBlog ? '/@' . $blog->subdomain : '');
        });
    }
}
