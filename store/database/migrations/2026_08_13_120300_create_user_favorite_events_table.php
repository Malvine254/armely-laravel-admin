<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_favorite_events', function (Blueprint $table) {
            $table->id();
            $table->string('identity_key', 96)->index();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedBigInteger('product_id')->index();
            $table->string('event_type', 16)->index();
            $table->json('metadata')->nullable();
            $table->timestamp('event_at')->index();
            $table->timestamps();

            $table->index(['identity_key', 'product_id', 'event_at'], 'ufe_identity_product_event_idx');
            $table->index(['user_id', 'product_id', 'event_at'], 'ufe_user_product_event_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_favorite_events');
    }
};
