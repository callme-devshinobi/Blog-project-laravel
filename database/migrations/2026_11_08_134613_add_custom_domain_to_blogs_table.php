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
        Schema::table('blogs', function (Blueprint $table) {
            $table->string('custom_domain')->nullable()->unique();
            $table->timestamp('custom_domain_verified_at')->nullable();
            $table->string('domain_token')->nullable();
        });
    }
    
    public function down(): void
    {
        Schema::table('blogs', function (Blueprint $table) {
            $table->dropColumn(['custom_domain', 'custom_domain_verified_at', 'domain_token']);
        });
    }
};
