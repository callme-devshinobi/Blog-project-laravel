<?php

namespace App\Support;

use App\Models\Blog;
use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class SiteContent
{
    private static array $counts = [];

    public static function about(): array
    {
        return static::section('about');
    }

    public static function contact(): array
    {
        return static::section('contact');
    }

    public static function faq(): array
    {
        return static::section('faq');
    }

    /** What an admin saved, laid over the default text. */
    private static function section(string $name): array
    {
        $defaults = (array) config("devnotes.pages.{$name}", []);
        $saved    = json_decode((string) PlanSettings::get("page_{$name}"), true);

        return is_array($saved) ? array_merge($defaults, $saved) : $defaults;
    }

    public static function portraitUrl(): ?string
    {
        $path = PlanSettings::get('about_portrait_path');

        return $path ? Storage::disk('public')->url($path) : null;
    }

    /** Split a text into paragraphs at blank lines. */
    public static function paragraphs(?string $text): array
    {
        return array_values(array_filter(array_map('trim', preg_split('/\R{2,}/', (string) $text))));
    }

    /** Replace {posts}, {categories}, {blogs}, {writers} and {year} with live numbers. */
    public static function fill(?string $text): string
    {
        return preg_replace_callback(
            '/\{(posts|categories|blogs|writers|year)\}/',
            fn ($m) => (string) static::count($m[1]),
            (string) $text
        );
    }

    private static function count(string $what)
    {
        return static::$counts[$what] ??= match ($what) {
            'posts'      => number_format(Post::visible()->count()),
            'categories' => number_format(Category::count()),
            'blogs'      => number_format(Blog::whereNull('suspended_at')->count()),
            'writers'    => number_format(User::whereHas('posts', fn ($q) => $q->published())->count()),
            'year'       => date('Y'),
        };
    }

    public static function isExternal(?string $url): bool
    {
        return (bool) preg_match('#^https?://#i', (string) $url);
    }
}