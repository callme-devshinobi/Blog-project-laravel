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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('subscription_id')->nullable()->constrained()->nullOnDelete();
            $table->string('provider', 20);
            $table->string('reference');
            $table->unsignedBigInteger('amount')->default(0);   // smallest unit, e.g. kobo
            $table->string('currency', 3)->default('NGN');
            $table->string('status', 20)->default('pending');   // pending | success | failed
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
    
            $table->unique(['provider', 'reference']);
            $table->index(['status', 'paid_at']);
        });
    }
    
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
