<?php

namespace App\Console\Commands;

use App\Models\Blog;
use App\Models\Post;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class BackfillBlogs extends Command
{
    protected $signature = 'blogs:backfill';

    protected $description = 'Create a blog for every user without one and attach their posts';

    public function handle()
    {
        User::doesntHave('blog')->each(function ($user) {
            $base = Str::slug($user->name) ?: 'user';
            $slug = $base;
            $i = 1;

            while (Blog::where('subdomain', $slug)->exists()) {
                $slug = $base . '-' . $i++;
            }

            $blog = Blog::create([
                'user_id'   => $user->id,
                'subdomain' => $slug,
                'title'     => $user->name . "'s Blog",
            ]);

            $this->info("{$user->name} -> {$slug}");
        });

        // Attach every post that has no blog yet to its author's blog
        Post::whereNull('blog_id')->each(function ($post) {
            $blog = Blog::where('user_id', $post->user_id)->first();

            if ($blog) {
                $post->update(['blog_id' => $blog->id]);
            }
        });

        $this->info('Done.');
    }
}