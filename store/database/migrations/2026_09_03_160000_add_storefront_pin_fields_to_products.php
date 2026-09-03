<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('is_storefront_pinned')->default(false)->after('storefront_curated_at');
            $table->timestamp('storefront_pinned_at')->nullable()->after('is_storefront_pinned');
            $table->index(['is_storefront_pinned', 'storefront_pinned_at'], 'idx_storefront_pins');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('idx_storefront_pins');
            $table->dropColumn(['is_storefront_pinned', 'storefront_pinned_at']);
        });
    }
};