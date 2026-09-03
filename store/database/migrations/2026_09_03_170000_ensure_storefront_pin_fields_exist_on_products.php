<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('products', 'is_storefront_pinned')) {
            Schema::table('products', function (Blueprint $table) {
                $table->boolean('is_storefront_pinned')->default(false)->after('storefront_curated_at');
            });
        }

        if (!Schema::hasColumn('products', 'storefront_pinned_at')) {
            Schema::table('products', function (Blueprint $table) {
                $table->timestamp('storefront_pinned_at')->nullable()->after('is_storefront_pinned');
            });
        }

        $hasIndex = collect(Schema::getIndexes('products'))
            ->contains(fn (array $index) => ($index['name'] ?? null) === 'idx_storefront_pins');

        if (!$hasIndex) {
            Schema::table('products', function (Blueprint $table) {
                $table->index(['is_storefront_pinned', 'storefront_pinned_at'], 'idx_storefront_pins');
            });
        }
    }

    public function down(): void
    {
        // This migration reconciles an existing schema contract and is intentionally irreversible.
    }
};