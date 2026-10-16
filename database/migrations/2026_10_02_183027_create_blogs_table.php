<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('blogs', function (Blueprint $table) {
            $table->id();

            // Used in URLs, so it must be short and unique
            $table->string('slug')->unique();

            $table->string('title');
            $table->text('excerpt');

            // The full article content (long text, optional for now)
            $table->longText('body')->nullable();

            $table->string('image');
            $table->string('image_alt');

            $table->string('category');

            // Stored as JSON, e.g. ["laravel","php"]
            $table->json('tags')->nullable();

            $table->string('author');
            $table->string('author_initials', 5);

            // Minutes to read
            $table->unsignedSmallInteger('read_time');

            // When the post was published (null = still a draft)
            $table->date('published_at')->nullable();

            // created_at and updated_at
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blogs');
    }
};