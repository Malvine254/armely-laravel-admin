<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class StorefrontPinMigrationTest extends TestCase
{
    public function test_pin_migration_resumes_existing_partial_and_complete_schemas_without_losing_data(): void
    {
        foreach (['none', 'column', 'columns', 'complete'] as $state) {
            Schema::create('products', function (Blueprint $table) use ($state) {
                $table->id();
                $table->timestamp('storefront_curated_at')->nullable();
                if ($state !== 'none') {
                    $table->boolean('is_storefront_pinned')->default(false);
                }
                if (in_array($state, ['columns', 'complete'], true)) {
                    $table->timestamp('storefront_pinned_at')->nullable();
                }
                if ($state === 'complete') {
                    $table->index(['is_storefront_pinned', 'storefront_pinned_at'], 'idx_storefront_pins');
                }
            });
            DB::table('products')->insert($state === 'none' ? ['id' => 1] : ['id' => 1, 'is_storefront_pinned' => true]);
            $migration = require database_path('migrations/2026_09_03_160000_add_storefront_pin_fields_to_products.php');
            $migration->up();
            $migration->up();
            $this->assertTrue(Schema::hasColumns('products', ['is_storefront_pinned', 'storefront_pinned_at']));
            $this->assertTrue(Schema::hasIndex('products', 'idx_storefront_pins'));
            $this->assertSame(1, DB::table('products')->count());
            $this->assertEquals($state === 'none' ? 0 : 1, DB::table('products')->value('is_storefront_pinned'));
            Schema::drop('products');
        }
    }
}
