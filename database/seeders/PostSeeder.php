<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        $user       = User::first();       // the blog author made in DatabaseSeeder
        $categories = Category::all();     // the six categories

        Post::factory()
            ->count(60)
            ->state(fn () => ['category_id' => $categories->random()->id])
            ->create(['user_id' => $user->id]);
    }
}