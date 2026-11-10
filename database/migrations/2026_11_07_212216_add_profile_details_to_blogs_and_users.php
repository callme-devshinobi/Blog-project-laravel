<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('blogs', function (Blueprint $table) {
            if (! Schema::hasColumn('blogs', 'bio'))           $table->text('bio')->nullable();
            if (! Schema::hasColumn('blogs', 'contact_email')) $table->string('contact_email')->nullable();
            if (! Schema::hasColumn('blogs', 'phone'))         $table->string('phone', 40)->nullable();
            if (! Schema::hasColumn('blogs', 'github'))        $table->string('github')->nullable();
            if (! Schema::hasColumn('blogs', 'linkedin'))      $table->string('linkedin')->nullable();
            if (! Schema::hasColumn('blogs', 'twitter'))       $table->string('twitter')->nullable();
            if (! Schema::hasColumn('blogs', 'tech_stack'))    $table->json('tech_stack')->nullable();
            if (! Schema::hasColumn('blogs', 'write_about'))   $table->text('write_about')->nullable();
            if (! Schema::hasColumn('blogs', 'why_write'))     $table->text('why_write')->nullable();
        });

        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'avatar')) $table->string('avatar')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('blogs', function (Blueprint $table) {
            $table->dropColumn(['bio', 'contact_email', 'phone', 'github', 'linkedin', 'twitter', 'tech_stack', 'write_about', 'why_write']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('avatar');
        });
    }
};