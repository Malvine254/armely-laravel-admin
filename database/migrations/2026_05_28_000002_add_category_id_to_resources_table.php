<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('resources') && !Schema::hasColumn('resources', 'category_id')) {
            Schema::table('resources', function (Blueprint $table) {
                $table->foreignId('category_id')
                    ->nullable()
                    ->after('description')
                    ->constrained('resource_categories')
                    ->nullOnDelete();

                $table->index('category_id');
            });

            $serviceTitles = DB::table('services_lists')
                ->select('title')
                ->whereNotNull('title')
                ->where('title', '!=', '')
                ->pluck('title')
                ->all();

            $legacyCategories = DB::table('resources')
                ->select('category')
                ->whereNotNull('category')
                ->where('category', '!=', '')
                ->distinct()
                ->pluck('category')
                ->all();

            $defaults = array_values(array_unique(array_filter(array_merge($serviceTitles, $legacyCategories))));

            foreach ($defaults as $index => $name) {
                DB::table('resource_categories')->updateOrInsert(
                    ['slug' => Str::slug($name)],
                    [
                        'name' => $name,
                        'source' => in_array($name, $serviceTitles, true) ? 'service' : 'legacy',
                        'is_active' => 1,
                        'sort_order' => $index,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }

            foreach (DB::table('resources')->select('id', 'category')->get() as $resource) {
                $categoryId = DB::table('resource_categories')
                    ->where('name', $resource->category)
                    ->value('id');

                if ($categoryId) {
                    DB::table('resources')
                        ->where('id', $resource->id)
                        ->update(['category_id' => $categoryId]);
                }
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('resources') && Schema::hasColumn('resources', 'category_id')) {
            Schema::table('resources', function (Blueprint $table) {
                $table->dropConstrainedForeignId('category_id');
            });
        }
    }
};
