<?php

namespace App\Support;

use App\Models\Blog;
use App\Models\Post;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Seo
{
    // ---------- what a page can set ----------

    private static function bag(): array
    {
        return (array) request()->attributes->get('seo', []);
    }

    public static function set(array $meta): void
    {
        request()->attributes->set('seo', array_merge(static::bag(), $meta));
        request()->attributes->remove('seo_resolved');
    }

    /** A post page: cover image, title, excerpt, author and dates. */
    public static function article(Post $post): void
    {
        $post->loadMissing(['blog.user', 'user', 'category']);

        $tags = is_array($post->tags) ? $post->tags : (json_decode((string) $post->tags, true) ?: []);

        static::set([
            'type'        => 'article',
            'title'       => $post->title,
            'page_title'  => $post->title . ' — ' . ($post->blog?->title ?? config('devnotes.site.name')),
            'description' => $post->excerpt ?: strip_tags((string) $post->body),
            'url'         => $post->share_url,
            'image'       => static::postImage($post),
            'published'   => $post->published_at,
            'modified'    => $post->updated_at,
            'section'     => $post->category?->name,
            'tags'        => $tags,
            'author'      => $post->user?->name,
        ]);
    }

    /** A blog's pages (home, posts, about...). */
    public static function blog(Blog $blog): void
    {
        $site   = config('devnotes.site.name');
        $avatar = $blog->user?->avatar_url;

        static::set([
            'type'        => 'website',
            'title'       => $blog->title,
            'page_title'  => $blog->title . ' — on ' . $site,
            'description' => $blog->bio ?: $blog->title . ', a blog on ' . $site,
            'image'       => $avatar
                ? ['url' => static::absolute($avatar), 'alt' => (string) $blog->user->name]
                : static::siteImage(),
        ]);
    }

    // ---------- what the layout reads ----------

    public static function pageTitle(): string
    {
        return static::resolved()['page_title'];
    }

    public static function tags(): string
    {
        $m = static::resolved();

        $tag = fn (string $attr, string $name, ?string $value) => ($value !== null && $value !== '')
            ? '<meta ' . $attr . '="' . $name . '" content="' . e($value) . '">'
            : null;

        $img    = $m['image'];
        $hasImg = ! empty($img['url']);

        $lines = [
            '<meta name="description" content="' . e($m['description']) . '">',
            '<link rel="canonical" href="' . e($m['url']) . '">',
            $tag('property', 'og:site_name', $m['site']),
            $tag('property', 'og:type', $m['type']),
            $tag('property', 'og:title', $m['title']),
            $tag('property', 'og:description', $m['description']),
            $tag('property', 'og:url', $m['url']),
        ];

        if ($hasImg) {
            $lines[] = $tag('property', 'og:image', $img['url']);
            $lines[] = $tag('property', 'og:image:width', isset($img['width']) ? (string) $img['width'] : null);
            $lines[] = $tag('property', 'og:image:height', isset($img['height']) ? (string) $img['height'] : null);
            $lines[] = $tag('property', 'og:image:alt', $img['alt'] ?? $m['title']);
        }

        // X (Twitter), and the apps that read the same tags
        $lines[] = $tag('name', 'twitter:card', $hasImg ? 'summary_large_image' : 'summary');
        $lines[] = $tag('name', 'twitter:title', $m['title']);
        $lines[] = $tag('name', 'twitter:description', $m['description']);

        if ($hasImg) {
            $lines[] = $tag('name', 'twitter:image', $img['url']);
            $lines[] = $tag('name', 'twitter:image:alt', $img['alt'] ?? $m['title']);
        }

        if ($m['type'] === 'article') {
            $lines[] = $tag('property', 'article:published_time', $m['published']?->toIso8601String());
            $lines[] = $tag('property', 'article:modified_time', $m['modified']?->toIso8601String());
            $lines[] = $tag('property', 'article:section', $m['section']);
            $lines[] = $tag('name', 'author', $m['author']);

            foreach ((array) $m['tags'] as $t) {
                $lines[] = $tag('property', 'article:tag', (string) $t);
            }
        }

        return implode("\n  ", array_filter($lines)) . static::jsonLd($m);
    }

    // ---------- the details ----------

    private static function resolved(): array
    {
        $cached = request()->attributes->get('seo_resolved');

        if ($cached) {
            return $cached;
        }

        $blog = static::currentBlog();
        $site = (string) config('devnotes.site.name');

        $defaults = [
            'type'        => 'website',
            'site'        => $site,
            'title'       => $blog ? $blog->title : $site,
            'page_title'  => $blog ? $blog->title . ' — on ' . $site : $site . ' — ' . config('devnotes.site.hero_title'),
            'description' => $blog ? ($blog->bio ?: $blog->title . ', a blog on ' . $site) : config('devnotes.site.hero_desc'),
            'url'         => static::currentUrl(),
            'image'       => static::siteImage(),
            'published'   => null,
            'modified'    => null,
            'section'     => null,
            'tags'        => [],
            'author'      => null,
        ];

        $m = array_merge($defaults, static::bag());

        $m['title']       = Str::limit(trim((string) $m['title']), 110, '…');
        $m['description'] = (string) Str::of(strip_tags((string) $m['description']))->squish()->limit(200, '…');

        if ($m['description'] === '') {
            $m['description'] = (string) config('devnotes.site.hero_desc');
        }

        request()->attributes->set('seo_resolved', $m);

        return $m;
    }

    private static function currentBlog(): ?Blog
    {
        $blog = app()->bound('customDomainBlog') ? app('customDomainBlog') : request()->route('blog');

        return $blog instanceof Blog ? $blog : null;
    }

    private static function currentUrl(): string
    {
        $blog = static::currentBlog();

        // on a custom domain the request is rewritten to /@name/..., so rebuild the real address
        if ($blog && app()->bound('customDomainBlog')) {
            $path = '/' . ltrim(Str::after('/' . request()->path(), '/@' . $blog->subdomain), '/');

            return $blog->public_base . ($path === '/' ? '' : $path);
        }

        return url()->current();
    }

    private static function postImage(Post $post): array
    {
        $path = $post->featured_image;
        $alt  = $post->image_alt ?: $post->title;

        if (! $path) {
            return static::siteImage();
        }

        // a cover that is a web link: we can't know its size
        if (preg_match('#^https?://#i', $path)) {
            return ['url' => $path, 'alt' => $alt];
        }

        $size = static::imageSize($path);

        return [
            'url'    => static::storageUrl($path),
            'width'  => $size[0] ?? null,
            'height' => $size[1] ?? null,
            'alt'    => $alt,
        ];
    }

    /** The site's own picture, used when a post has no cover. */
    private static function siteImage(): array
    {
        $path = config('devnotes.site.hero_image_path');
        $alt  = (string) config('devnotes.site.name');

        if ($path) {
            $size = static::imageSize($path);

            return [
                'url'    => static::storageUrl($path),
                'width'  => $size[0] ?? null,
                'height' => $size[1] ?? null,
                'alt'    => $alt,
            ];
        }

        return ['url' => (string) config('devnotes.site.hero_image'), 'alt' => $alt];
    }

    /** Built from the address the visitor used, so it also works behind tunnels and custom domains. */
    private static function storageUrl(string $path): string
    {
        return url('storage/' . ltrim($path, '/'));
    }

    private static function absolute(?string $url): ?string
    {
        if (! $url) {
            return null;
        }

        return preg_match('#^https?://#i', $url) ? $url : url($url);
    }

    private static function imageSize(string $path): ?array
    {
        return Cache::remember('og-size:' . md5($path), 86400, function () use ($path) {
            try {
                $info = @getimagesize(Storage::disk('public')->path($path));

                return $info ? [$info[0], $info[1]] : null;
            } catch (\Throwable $e) {
                return null;
            }
        });
    }

    private static function jsonLd(array $m): string
    {
        if ($m['type'] !== 'article') {
            return '';
        }

        $data = array_filter([
            '@context'         => 'https://schema.org',
            '@type'            => 'BlogPosting',
            'headline'         => Str::limit($m['title'], 110, ''),
            'description'      => $m['description'],
            'image'            => ! empty($m['image']['url']) ? [$m['image']['url']] : null,
            'datePublished'    => $m['published']?->toIso8601String(),
            'dateModified'     => ($m['modified'] ?? $m['published'])?->toIso8601String(),
            'mainEntityOfPage' => $m['url'],
            'articleSection'   => $m['section'],
            'keywords'         => $m['tags'] ? implode(', ', (array) $m['tags']) : null,
            'author'           => ! empty($m['author']) ? ['@type' => 'Person', 'name' => $m['author']] : null,
            'publisher'        => ['@type' => 'Organization', 'name' => $m['site']],
        ]);

        // the JSON flags make sure nothing in a post can close the script tag early
        return "\n  " . '<script type="application/ld+json">'
            . json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP)
            . '</script>';
    }
}