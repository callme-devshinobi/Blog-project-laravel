<?php

namespace Database\Factories;

use App\Models\Blog;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Blog>
 */
class BlogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = rtrim(fake()->unique()->sentence(6), '.');
        return [
            'title'           => $title,
            'slug'            => Str::slug($title),   // "my first post" -> "my-first-post"
            'excerpt'         => fake()->text(150),
            'body' => '<p>' . implode('</p><p>', fake()->paragraphs(5)) . '</p>',
            'image'           => 'https://picsum.photos/seed/' . fake()->unique()->numberBetween(1, 9999) . '/800/450',
            'image_alt'       => fake()->sentence(4),
            'category'        => fake()->randomElement([
                'Laravel',
                'Web Development',
                'JavaScript',
                'Tools & Resources',
                'Productivity',
                'Tech News',
            ]),
            'tags'            => fake()->randomElements(
                ['laravel', 'php', 'blade', 'css', 'javascript', 'git', 'tips'],
                3
            ),
            'author'          => 'Dennis Onwuegbu',
            'author_initials' => 'DO',
            'read_time'       => fake()->numberBetween(3, 15),
            'published_at'    => fake()->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
// use App\Models\Blog  Blog::factory()->count(50)->create() 