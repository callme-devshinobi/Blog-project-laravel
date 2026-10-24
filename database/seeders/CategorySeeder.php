<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Laravel', 'slug' => 'laravel',
             'description' => 'Framework tutorials, project builds, Eloquent tips and everything PHP artisan.'],
            ['name' => 'Web Development', 'slug' => 'web-development',
             'description' => 'Full-stack building blocks — HTML, CSS, tooling, deployment and modern workflows.'],
            ['name' => 'JavaScript', 'slug' => 'javascript',
             'description' => 'Vanilla JS fundamentals, browser APIs, patterns and the occasional framework note.'],
            ['name' => 'Tools & Resources', 'slug' => 'tools-resources',
             'description' => 'VS Code extensions, packages, CLI tools and setups that make dev life easier.'],
            ['name' => 'Productivity', 'slug' => 'productivity',
             'description' => 'Habits, routines and systems that keep you consistent as a developer.'],
            ['name' => 'Tech News', 'slug' => 'tech-news',
             'description' => 'What matters in the dev world — releases, updates and honest takes.'],
        ];

        foreach ($categories as $category) {
            // Update it if the slug already exists, otherwise create it
            Category::updateOrCreate(['slug' => $category['slug']], $category);
        }
    }
}