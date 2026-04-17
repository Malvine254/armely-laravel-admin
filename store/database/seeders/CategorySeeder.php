<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Seed the categories table with top-level categories (from UNSPSC segments)
     * and brand sub-categories for the navbar.
     */
    public function run(): void
    {
        // Top-level categories ordered by product count (descending).
        // segment_code links to the products.category_segment column.
        $categories = [
            ['name' => 'IT Hardware & Telecom',     'segment_code' => '43', 'sort_order' => 1, 'brands' => ['Cisco', 'HP', 'Dell', 'Lenovo']],
            ['name' => 'Software & IT Services',    'segment_code' => '81', 'sort_order' => 2, 'brands' => ['Microsoft', 'Veeam', 'Cisco', 'Fortinet']],
            ['name' => 'Power & UPS',               'segment_code' => '26', 'sort_order' => 3, 'brands' => ['APC by Schneider', 'Eaton', 'Vertiv', 'CyberPower']],
            ['name' => 'Printing & Imaging',        'segment_code' => '45', 'sort_order' => 4, 'brands' => ['HP', 'Lexmark', 'Epson', 'Canon']],
            ['name' => 'Consumer Electronics',      'segment_code' => '52', 'sort_order' => 5, 'brands' => ['Samsung', 'Sony', 'Logitech', 'Panasonic']],
            ['name' => 'Security Equipment',        'segment_code' => '46', 'sort_order' => 6, 'brands' => ['Cisco', 'Fortinet', 'Barracuda', 'WatchGuard']],
            ['name' => 'Racks & Containment',       'segment_code' => '24', 'sort_order' => 7, 'brands' => []],
            ['name' => 'Cables & Electrical',       'segment_code' => '39', 'sort_order' => 8, 'brands' => []],
            ['name' => 'Office Equipment',          'segment_code' => '44', 'sort_order' => 9, 'brands' => []],
            ['name' => 'Electronic Components',     'segment_code' => '32', 'sort_order' => 10, 'brands' => []],
            ['name' => 'Protective Cases',          'segment_code' => '53', 'sort_order' => 11, 'brands' => []],
            ['name' => 'Monitor Arms & Mounts',     'segment_code' => '41', 'sort_order' => 12, 'brands' => []],
            ['name' => 'Furniture',                 'segment_code' => '56', 'sort_order' => 13, 'brands' => []],
        ];

        // Manufacturer value map: brand label → manufacturer column value in products table
        $mfrMap = [
            'Cisco'              => 'CISCO',
            'HP'                 => 'HP',
            'Dell'               => 'DELL',
            'Lenovo'             => 'LENOVO',
            'Microsoft'          => 'MICROSOFT',
            'Veeam'              => 'VEEAM SOFTWARE CORPORATION',
            'Fortinet'           => 'FORTINET INC.',
            'APC by Schneider'   => 'APC BY SCHNEIDER ELECTRIC',
            'Eaton'              => 'EATON',
            'Vertiv'             => 'VERTIV',
            'CyberPower'         => 'CYBERPOWER SYSTEMS (USA), INC.',
            'Lexmark'            => 'LEXMARK',
            'Epson'              => 'EPSON',
            'Canon'              => 'CANON',
            'Samsung'            => 'SAMSUNG',
            'Sony'               => 'SONY',
            'Logitech'           => 'LOGITECH',
            'Panasonic'          => 'PANASONIC',
            'Barracuda'          => 'BARRACUDA NETWORKS',
            'WatchGuard'         => 'WATCHGUARD TECHNOLOGIES',
        ];

        foreach ($categories as $catData) {
            $parent = Category::updateOrCreate(
                ['slug' => Str::slug($catData['name'])],
                [
                    'name'         => $catData['name'],
                    'segment_code' => $catData['segment_code'],
                    'sort_order'   => $catData['sort_order'],
                    'is_active'    => true,
                    'show_in_menu' => true,
                    'parent_id'    => null,
                ]
            );

            foreach ($catData['brands'] as $i => $brandLabel) {
                $mfrValue = $mfrMap[$brandLabel] ?? strtoupper($brandLabel);
                Category::updateOrCreate(
                    ['slug' => Str::slug($catData['name'] . '-' . $brandLabel)],
                    [
                        'parent_id'    => $parent->id,
                        'name'         => $brandLabel,
                        'segment_code' => null,
                        'sort_order'   => $i + 1,
                        'is_active'    => true,
                        'show_in_menu' => true,
                        'description'  => $mfrValue, // store the manufacturer filter value
                    ]
                );
            }
        }
    }
}
