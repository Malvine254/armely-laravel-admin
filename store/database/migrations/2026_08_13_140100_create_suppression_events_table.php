<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suppression_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('email', 320)->nullable()->index();
            $table->string('event_type', 32)->index();
            $table->string('channel', 16)->default('email');
            $table->string('reason', 64)->nullable();
            $table->string('source', 64)->default('system');
            $table->json('metadata')->nullable();
            $table->timestamp('occurred_at')->index();
            $table->timestamps();

            $table->index(['user_id', 'event_type', 'occurred_at'], 'se_user_event_occurred_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suppression_events');
    }
};
