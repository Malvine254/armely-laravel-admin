<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $columns = [
            'search_imported_at' => fn (Blueprint $table) => $table->timestamp('search_imported_at')->nullable()->after('image_enrichment_attempted_at'),
            'search_import_query' => fn (Blueprint $table) => $table->string('search_import_query', 500)->nullable()->after('search_imported_at'),
            'search_import_review_status' => fn (Blueprint $table) => $table->string('search_import_review_status', 30)->nullable()->after('search_import_query'),
            'search_import_reviewed_at' => fn (Blueprint $table) => $table->timestamp('search_import_reviewed_at')->nullable()->after('search_import_review_status'),
            'search_import_reviewed_by' => fn (Blueprint $table) => $table->unsignedBigInteger('search_import_reviewed_by')->nullable()->after('search_import_reviewed_at'),
        ];
        foreach ($columns as $column => $definition) {
            if (!Schema::hasColumn('products', $column)) {
                Schema::table('products', $definition);
            }
        }

        $indexes = collect(Schema::getIndexes('products'))->pluck('name');
        if (!$indexes->contains('idx_search_import_review')) {
            Schema::table('products', fn (Blueprint $table) => $table->index(
                ['search_import_review_status', 'search_imported_at'],
                'idx_search_import_review'
            ));
        }
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('idx_search_import_review');
            $table->dropColumn(['search_imported_at', 'search_import_query', 'search_import_review_status', 'search_import_reviewed_at', 'search_import_reviewed_by']);
        });
    }
};
