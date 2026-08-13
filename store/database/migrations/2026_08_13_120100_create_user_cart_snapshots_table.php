<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_cart_snapshots', function (Blueprint $table) {
            $table->id();
            $table->string('identity_key', 96)->unique();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->json('items')->nullable();
            $table->unsignedInteger('item_count')->default(0);
            $table->unsignedInteger('total_quantity')->default(0);
            $table->timestamp('last_synced_at')->index();
            $table->timestamps();

            $table->index(['user_id', 'last_synced_at'], 'ucs_user_synced_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_cart_snapshots');
    }
};
