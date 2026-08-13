<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reminder_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->string('identity_key', 96)->index();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedBigInteger('product_id')->nullable()->index();
            $table->string('trigger_type', 32)->index();
            $table->string('channel', 16)->default('email');
            $table->unsignedInteger('delay_minutes')->default(120);
            $table->unsignedInteger('cooldown_minutes')->default(1440);
            $table->timestamp('last_notified_at')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'trigger_type', 'is_active'], 'rs_user_trigger_active_idx');
            $table->index(['identity_key', 'trigger_type', 'is_active'], 'rs_identity_trigger_active_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reminder_subscriptions');
    }
};
