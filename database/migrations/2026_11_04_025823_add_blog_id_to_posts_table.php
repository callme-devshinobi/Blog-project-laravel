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
    Schema::table('posts', function (Blueprint $table) {
        $table->foreignId('blog_id')->nullable()->constrained()->cascadeOnDelete();
    });
}

public function down(): void
{
    Schema::table('posts', function (Blueprint $table) {
        $table->dropConstrainedForeignId('blog_id');
    });
}
};
