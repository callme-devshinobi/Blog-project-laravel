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
            if (! Schema::hasColumn('posts', 'moderated_at')) {
                $table->timestamp('moderated_at')->nullable();
            }
            if (! Schema::hasColumn('posts', 'moderation_reason')) {
                $table->string('moderation_reason')->nullable();
            }
        });
    }
    
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn(['moderated_at', 'moderation_reason']);
        });
    }
};
