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
    Schema::create('subscriptions', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        $table->string('plan', 30)->default('pro');
        $table->string('provider', 20)->default('manual');      // manual | paystack | stripe
        $table->string('provider_subscription_id')->nullable();
        $table->string('provider_customer_id')->nullable();
        $table->text('provider_token')->nullable();             // encrypted by the model
        $table->string('status', 20)->default('active');        // active | past_due | canceled | expired
        $table->timestamp('current_period_end')->nullable();    // empty + active = no end date
        $table->boolean('cancel_at_period_end')->default(false);
        $table->timestamp('canceled_at')->nullable();
        $table->string('note')->nullable();
        $table->timestamps();

        $table->unique(['provider', 'provider_subscription_id']);
        $table->index(['status', 'current_period_end']);
    });
}

public function down(): void
{
    Schema::dropIfExists('subscriptions');
}
};
