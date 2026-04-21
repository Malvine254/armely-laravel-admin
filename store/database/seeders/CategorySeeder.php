<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Support\CatalogTaxonomy;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Seed strict top-level categories from B2B_Hardware_Catalog_2000SKUs.xlsx.
     */
    public function run(): void
    {
        // Hide any legacy categories from menu to enforce strict workbook taxonomy.
        Category::query()->update([
            'is_active' => false,
            'show_in_menu' => false,
        ]);

        $curated = CatalogTaxonomy::curatedCategories();

        foreach ($curated as $index => $catData) {
            Category::updateOrCreate(
                ['slug' => $catData['slug']],
                [
                    'parent_id' => null,
                    'name' => $catData['name'],
                    'segment_code' => implode(',', $catData['segment_codes']),
                    'sort_order' => $index + 1,
                    'is_active' => true,
                    'show_in_menu' => true,
                    'description' => null,
                ]
            );
        }

        // Keep strict top-level only. Any legacy children remain hidden by the update above.
    }
}
