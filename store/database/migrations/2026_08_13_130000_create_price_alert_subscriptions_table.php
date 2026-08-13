<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('price_alert_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->string('identity_key', 96)->index();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->decimal('baseline_price', 15, 2)->nullable();
            $table->decimal('min_drop_amount', 15, 2)->default(0);
            $table->decimal('min_drop_percent', 7, 2)->default(0);
            $table->unsignedInteger('cooldown_minutes')->default(1440);
            $table->timestamp('last_notified_at')->nullable();
            $table->string('source', 24)->default('manual');
            $table->boolean('is_active')->default(true)->index();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['identity_key', 'product_id'], 'pas_identity_product_unique');
            $table->index(['user_id', 'is_active'], 'pas_user_active_idx');
            $table->index(['product_id', 'is_active'], 'pas_product_active_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('price_alert_subscriptions');
    }
};
