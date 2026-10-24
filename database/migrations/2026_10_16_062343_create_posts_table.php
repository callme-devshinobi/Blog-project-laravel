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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
    
            // Foreign keys: link each post to its author and its category
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->restrictOnDelete();
    
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt');
            $table->longText('body');
    
            $table->string('featured_image')->nullable();
            $table->string('image_alt')->nullable();
    
            // Stored as JSON, e.g. ["laravel","php"]
            $table->json('tags')->nullable();
    
            // Minutes to read
            $table->unsignedSmallInteger('read_time')->nullable();
    
            $table->string('status')->default('draft');
            $table->timestamp('published_at')->nullable();
    
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
