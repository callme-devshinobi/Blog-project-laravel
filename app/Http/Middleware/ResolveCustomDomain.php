<?php

namespace App\Http\Middleware;

use App\Models\Blog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ResolveCustomDomain
{
    public function handle(Request $request, Closure $next)
    {
        $host = strtolower($request->getHost());

        // DevNotes' own addresses behave as normal
        if (in_array($host, config('devnotes.main_hosts'), true)) {
            return $next($request);
        }

        // otherwise the host must be a verified custom domain
        $blog = Blog::where('custom_domain', $host)
            ->whereNotNull('custom_domain_verified_at')
            ->first();

        abort_if(! $blog, 404);

        app()->instance('customDomainBlog', $blog);

        $path = '/' . ltrim($request->path(), '/');   // "/" or "/posts"

        // links such as pagination may already contain /@name
        if (Str::startsWith($path, '/@' . $blog->subdomain)) {
            return $next($request);
        }

        // only the public pages are served on a custom domain
        // (logging in and the dashboard stay on the main site)
        $isPublic = in_array($path, ['/', '/posts', '/categories', '/about', '/contact', '/search'], true)|| Str::startsWith($path, ['/show/', '/post/']);

        abort_unless($isPublic, 404);

        // quietly rewrite "/posts" into "/@name/posts" so the blog routes handle it
        $uri = '/@' . $blog->subdomain . ($path === '/' ? '' : $path);
        if ($query = $request->getQueryString()) {
            $uri .= '?' . $query;
        }

        $request->server->set('REQUEST_URI', $uri);
        $request->initialize(
            $request->query->all(), $request->request->all(), $request->attributes->all(),
            $request->cookies->all(), $request->files->all(), $request->server->all(),
            $request->getContent()
        );

        return $next($request);
    }
}