<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('is_storefront_curated')->default(false)->after('category_segment');
            $table->decimal('storefront_score', 14, 2)->default(0)->after('is_storefront_curated');
            $table->unsignedInteger('storefront_rank')->nullable()->after('storefront_score');
            $table->timestamp('storefront_curated_at')->nullable()->after('storefront_rank');
            $table->timestamp('image_enrichment_attempted_at')->nullable()->after('storefront_curated_at');
            $table->index(['is_storefront_curated', 'category_segment', 'storefront_rank'], 'idx_storefront_assortment');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('idx_storefront_assortment');
            $table->dropColumn(['is_storefront_curated', 'storefront_score', 'storefront_rank', 'storefront_curated_at', 'image_enrichment_attempted_at']);
        });
    }
};
