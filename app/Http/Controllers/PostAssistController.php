<?php

namespace App\Http\Controllers;

use App\Ai\Agents\PostAssistant;
use App\Ai\Agents\PostWriter;
use App\Models\Category;
use App\Services\UserAiProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostAssistController extends Controller
{
    /** @return array{0: mixed, 1: \Illuminate\Http\JsonResponse|null} the writer's provider, or an error response */
    private function providerFor(Request $request): array
    {
        $user = $request->user();

        if (! $user->hasPro()) {
            return [null, response()->json(['message' => 'AI writing help is part of the Pro plan.'], 403)];
        }

        $provider = UserAiProvider::for($user);

        if (! $provider) {
            return [null, response()->json(['message' => 'Add your AI key in Settings first.'], 403)];
        }

        return [$provider, null];
    }

    private function providerFailed(\Throwable $e, Request $request)
    {
        logger()->warning('AI request failed', [
            'user'     => $request->user()->id,
            'provider' => $request->user()->aiCredential?->provider,
            'error'    => class_basename($e),
        ]);

        return response()->json([
            'message' => 'Your AI provider didn\'t accept the request. Check your key and billing in Settings, then try again.',
        ], 502);
    }

    public function suggest(Request $request)
    {
        [$provider, $error] = $this->providerFor($request);
        if ($error) {
            return $error;
        }

        $data = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'body'  => ['required', 'string', 'min:200', 'max:20000'],
        ]);

        $categories = Category::orderBy('name')->get(['id', 'name']);

        try {
            $response = (new PostAssistant($categories->pluck('name')->all()))
                ->prompt('Title: ' . ($data['title'] ?? '') . "\n\n" . $data['body'], provider: [$provider]);
        } catch (\Throwable $e) {
            return $this->providerFailed($e, $request);
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
        [$provider, $error] = $this->providerFor($request);
        if ($error) {
            return $error;
        }

        $data = $request->validate([
            'title' => ['required', 'string', 'min:8', 'max:150'],
        ]);

        set_time_limit(120);   // a full article can take a while

        $categories = Category::orderBy('name')->get(['id', 'name']);

        try {
            $response = (new PostWriter($categories->pluck('name')->all()))
                ->prompt('Title: ' . $data['title'], provider: [$provider]);
        } catch (\Throwable $e) {
            return $this->providerFailed($e, $request);
        }

        if ($response['status'] !== 'ok' || trim($response['article']) === '') {
            return response()->json([
                'message' => 'The assistant can\'t write about that topic. Please try a different title.',
            ], 422);
        }

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