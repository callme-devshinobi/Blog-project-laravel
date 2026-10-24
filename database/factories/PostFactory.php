<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PostFactory extends Factory
{
    public function definition(): array
    {
        // Made first so the slug can be built from the title
        $title = rtrim(fake()->unique()->sentence(6), '.');

        return [
            // If no user/category is given, make a new one automatically
            'user_id'        => User::factory(),
            'category_id'    => Category::factory(),

            'title'          => $title,
            'slug'           => Str::slug($title),
            'excerpt'        => fake()->text(150),
            'body'           => '<p>' . implode('</p><p>', fake()->paragraphs(5)) . '</p>',
            'featured_image' => 'https://picsum.photos/seed/' . fake()->unique()->numberBetween(1, 9999) . '/800/450',
            'image_alt'      => fake()->sentence(4),
            'tags'           => fake()->randomElements(
                ['laravel', 'php', 'blade', 'css', 'javascript', 'git', 'tips'],
                3
            ),
            'read_time'      => fake()->numberBetween(3, 15),
            'status'         => 'published',
            'published_at'   => fake()->dateTimeBetween('-1 year', 'now'),
        ];
    }
}