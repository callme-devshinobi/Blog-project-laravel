<?php

namespace App\Http\Controllers;

use App\Ai\Agents\PostAssistant;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Ai\Agents\PostWriter;
use Illuminate\Support\Facades\RateLimiter;

class PostAssistController extends Controller
{
    public function suggest(Request $request)
    {
        $data = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'body'  => ['required', 'string', 'min:200', 'max:20000'],
        ]);

        $categories = Category::orderBy('name')->get(['id', 'name']);

        try {
            $response = (new PostAssistant($categories->pluck('name')->all()))
                ->prompt('Title: ' . ($data['title'] ?? '') . "\n\n" . $data['body']);
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'message' => 'The assistant is unavailable right now. Please try again later.',
            ], 503);
        }

        $category = $categories->firstWhere('name', $response['category']);

        return response()->json([
            'excerpt'     => Str::limit(trim($response['excerpt']), 300, ''),
            'tags'        => collect($response['tags'])
                                ->map(fn ($t) => Str::lower(trim($t)))
                                ->filter()->unique()->take(6)->values(),
            'category_id' => $category?->id,
        ]);
    }

    public function generate(Request $request)
{
    $data = $request->validate([
        'title' => ['required', 'string', 'min:8', 'max:150'],
    ]);

    // daily cap: 10 successful generations per user
    $key = 'ai-generate:' . $request->user()->id;

    if (RateLimiter::tooManyAttempts($key, 10)) {
        return response()->json([
            'message' => 'You\'ve reached today\'s limit for AI-written drafts. Please try again tomorrow.',
        ], 429);
    }

    set_time_limit(120);   // a full article can take a while

    $categories = Category::orderBy('name')->get(['id', 'name']);

    try {
        $response = (new PostWriter($categories->pluck('name')->all()))
            ->prompt('Title: ' . $data['title']);
    } catch (\Throwable $e) {
        report($e);

        return response()->json([
            'message' => 'The assistant is unavailable right now. Please try again later.',
        ], 503);
    }

    if ($response['status'] !== 'ok' || trim($response['article']) === '') {
        return response()->json([
            'message' => 'The assistant can\'t write about that topic. Please try a different title.',
        ], 422);
    }

    RateLimiter::hit($key, 86400);   // counts only successful generations, for 24 hours

    // tidy the article into plain paragraphs separated by blank lines
    $article = str_replace(["\r\n", "\r"], "\n", $response['article']);
    $article = preg_replace('/^\s*#{1,6}\s*/m', '', $article);   // stray # headings
    $article = str_replace(['**', '__', '`'], '', $article);     // stray Markdown marks
    $article = trim(preg_replace("/\n{3,}/", "\n\n", $article));

    $category = $categories->firstWhere('name', $response['category']);

    return response()->json([
        'article'     => $article,
        'excerpt'     => Str::limit(trim($response['excerpt']), 300, ''),
        'tags'        => collect($response['tags'])
                            ->map(fn ($t) => Str::lower(trim($t)))
                            ->filter()->unique()->take(6)->values(),
        'category_id' => $category?->id,
    ]);
}
}