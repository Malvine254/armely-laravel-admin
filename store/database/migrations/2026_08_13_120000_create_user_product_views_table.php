<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_product_views', function (Blueprint $table) {
            $table->id();
            $table->string('identity_key', 96)->index();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedBigInteger('product_id');
            $table->timestamp('viewed_at')->index();
            $table->timestamps();

            $table->index(['identity_key', 'product_id', 'viewed_at'], 'upv_identity_product_viewed_idx');
            $table->index(['user_id', 'product_id', 'viewed_at'], 'upv_user_product_viewed_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_product_views');
    }
};
