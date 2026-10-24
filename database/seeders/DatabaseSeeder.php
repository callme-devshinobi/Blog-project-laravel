<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. The blog author
        User::updateOrCreate(
            ['email' => 'dennischiagozem@gmail.com'],
            [
                'name'         => 'Dennis Onwuegbu',
                'display_name' => 'Dennis Onwuegbu',
                'password'     => Hash::make('password'),
            ]
        );

        // 2. Then categories, then posts (posts need both to exist)
        $this->call([
            CategorySeeder::class,
            PostSeeder::class,
        ]);
    }
}