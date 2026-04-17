<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('products', 'category_segment') || !Schema::hasColumn('products', 'manufacturer')) {
            Schema::table('products', function (Blueprint $table) {
                if (!Schema::hasColumn('products', 'category_segment')) {
                    $table->char('category_segment', 2)->default('')->after('is_hardware')
                        ->comment('First 2 chars of UNSPSC categoryCode for fast GROUP BY');
                }

                if (!Schema::hasColumn('products', 'manufacturer')) {
                    $table->string('manufacturer', 255)->default('')->after('category_segment')
                        ->comment('Denormalized from specifications.manufacturer for fast GROUP BY');
                }
            });
        }

        if (!$this->indexExists('products', 'idx_vendor_hw_price_seg')) {
            Schema::table('products', function (Blueprint $table) {
                $table->index(['vendor_id', 'is_hardware', 'base_price', 'category_segment'], 'idx_vendor_hw_price_seg');
            });
        }

        if (!$this->indexExists('products', 'idx_vendor_hw_price_mfr')) {
            Schema::table('products', function (Blueprint $table) {
                $table->index(['vendor_id', 'is_hardware', 'base_price', 'manufacturer'], 'idx_vendor_hw_price_mfr');
            });
        }

        // Backfill category_segment from specifications JSON
        DB::statement("
            UPDATE products
            SET category_segment = LEFT(COALESCE(JSON_UNQUOTE(JSON_EXTRACT(specifications, '$.categoryCode')), ''), 2),
                manufacturer = TRIM(COALESCE(JSON_UNQUOTE(JSON_EXTRACT(specifications, '$.manufacturer')), ''))
            WHERE vendor_id = 'TD SYNNEX'
        ");
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if ($this->indexExists('products', 'idx_vendor_hw_price_seg')) {
                $table->dropIndex('idx_vendor_hw_price_seg');
            }

            if ($this->indexExists('products', 'idx_vendor_hw_price_mfr')) {
                $table->dropIndex('idx_vendor_hw_price_mfr');
            }

            if (Schema::hasColumn('products', 'category_segment')) {
                $table->dropColumn('category_segment');
            }

            if (Schema::hasColumn('products', 'manufacturer')) {
                $table->dropColumn('manufacturer');
            }
        });
    }

    private function indexExists(string $table, string $indexName): bool
    {
        return DB::table('information_schema.statistics')
            ->where('table_schema', DB::getDatabaseName())
            ->where('table_name', $table)
            ->where('index_name', $indexName)
            ->exists();
    }
};
