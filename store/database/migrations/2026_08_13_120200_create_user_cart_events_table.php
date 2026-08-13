<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_cart_events', function (Blueprint $table) {
            $table->id();
            $table->string('identity_key', 96)->index();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('event_type', 24)->index();
            $table->unsignedBigInteger('product_id')->nullable()->index();
            $table->unsignedInteger('quantity')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('event_at')->index();
            $table->timestamps();

            $table->index(['identity_key', 'event_at'], 'uce_identity_event_at_idx');
            $table->index(['user_id', 'event_at'], 'uce_user_event_at_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_cart_events');
    }
};
