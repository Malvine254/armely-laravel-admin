<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->boolean('transactional_enabled')->default(true);
            $table->boolean('marketing_enabled')->default(true)->index();
            $table->boolean('price_alerts_enabled')->default(true);
            $table->boolean('cart_reminders_enabled')->default(true);
            $table->boolean('browse_reminders_enabled')->default(true);
            $table->string('timezone', 64)->nullable();
            $table->unsignedSmallInteger('quiet_hours_start')->nullable();
            $table->unsignedSmallInteger('quiet_hours_end')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'marketing_enabled'], 'ep_user_marketing_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_preferences');
    }
};
