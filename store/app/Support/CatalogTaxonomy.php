<?php

namespace App\Support;

class CatalogTaxonomy
{
    /**
     * Curated category set from B2B_Hardware_Catalog_2000SKUs.xlsx.
     * segment_codes are internal browse filter codes used by the store APIs.
     *
     * @return array<int, array{name:string,slug:string,segment_codes:array<int,string>,keywords:array<int,string>}>
     */
    public static function curatedCategories(): array
    {
        return [
            ['name' => 'Laptops & Notebooks', 'slug' => 'laptops-notebooks', 'segment_codes' => ['01'], 'keywords' => ['laptop', 'notebook', 'ultrabook', 'chromebook']],
            ['name' => 'Desktops & Workstations', 'slug' => 'desktops-workstations', 'segment_codes' => ['02'], 'keywords' => ['desktop', 'workstation', 'all-in-one', 'aio pc']],
            ['name' => 'Monitors & Displays', 'slug' => 'monitors-displays', 'segment_codes' => ['03'], 'keywords' => ['monitor', 'display', 'screen', 'lcd', 'led', 'oled']],
            ['name' => 'Networking', 'slug' => 'networking', 'segment_codes' => ['04'], 'keywords' => ['switch', 'router', 'firewall', 'network', 'access point', 'wi-fi', 'wifi']],
            ['name' => 'Servers & Storage', 'slug' => 'servers-storage', 'segment_codes' => ['05'], 'keywords' => ['server', 'storage', 'nas', 'san', 'raid', 'ssd array']],
            ['name' => 'Printers & Scanners', 'slug' => 'printers-scanners', 'segment_codes' => ['06'], 'keywords' => ['printer', 'scanner', 'multifunction', 'mfp', 'plotter']],
            ['name' => 'Memory & Storage Upgrades', 'slug' => 'memory-storage-upgrades', 'segment_codes' => ['07'], 'keywords' => ['memory', 'ram', 'ssd', 'hdd', 'nvme', 'flash drive']],
            ['name' => 'Docking Stations & Hubs', 'slug' => 'docking-stations-hubs', 'segment_codes' => ['08'], 'keywords' => ['dock', 'docking station', 'usb-c hub', 'hub']],
            ['name' => 'Peripherals', 'slug' => 'peripherals', 'segment_codes' => ['09'], 'keywords' => ['keyboard', 'mouse', 'headset', 'webcam', 'speaker', 'peripheral']],
            ['name' => 'Power & UPS', 'slug' => 'power-ups', 'segment_codes' => ['10'], 'keywords' => ['ups', 'power supply', 'battery backup', 'surge', 'pdu']],
            ['name' => 'Video Conferencing', 'slug' => 'video-conferencing', 'segment_codes' => ['11'], 'keywords' => ['video conference', 'conference camera', 'teams room', 'zoom room']],
            ['name' => 'Cables & Adapters', 'slug' => 'cables-adapters', 'segment_codes' => ['12'], 'keywords' => ['cable', 'adapter', 'dongle', 'hdmi', 'displayport', 'usb-c']],
            ['name' => 'Security Hardware', 'slug' => 'security-hardware', 'segment_codes' => ['13'], 'keywords' => ['security', 'surveillance', 'camera', 'nvr', 'dvr', 'biometric']],
            ['name' => 'Mobile & Tablets', 'slug' => 'mobile-tablets', 'segment_codes' => ['14'], 'keywords' => ['tablet', 'mobile', 'smartphone', 'ipad', 'android tablet']],
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function allowedCategoryNames(): array
    {
        return array_map(static fn(array $item) => $item['name'], self::curatedCategories());
    }

    public static function normalizeCategoryName(string $value): ?string
    {
        $needle = trim(mb_strtolower($value));
        if ($needle === '') {
            return null;
        }

        foreach (self::curatedCategories() as $category) {
            if (mb_strtolower($category['name']) === $needle || mb_strtolower($category['slug']) === $needle) {
                return $category['name'];
            }
        }

        return null;
    }

    public static function inferCategoryName(
        string $sourceCategoryName,
        string $productName,
        string $description,
        string $categoryCode = ''
    ): string {
        $normalizedSource = self::normalizeCategoryName($sourceCategoryName);
        if ($normalizedSource !== null) {
            return $normalizedSource;
        }

        $haystack = mb_strtolower(trim($sourceCategoryName . ' ' . $productName . ' ' . $description));

        foreach (self::curatedCategories() as $category) {
            foreach ($category['keywords'] as $keyword) {
                if ($keyword !== '' && str_contains($haystack, mb_strtolower($keyword))) {
                    return $category['name'];
                }
            }
        }

        // Fallback using common TD SYNNEX UNSPSC-style prefixes when available.
        $prefix = substr(trim($categoryCode), 0, 2);
        $map = [
            '45' => 'Printers & Scanners',
            '46' => 'Security Hardware',
            '39' => 'Cables & Adapters',
            '26' => 'Power & UPS',
            '43' => 'Peripherals',
            '81' => 'Peripherals',
            '52' => 'Mobile & Tablets',
        ];

        return $map[$prefix] ?? 'Peripherals';
    }

    public static function segmentCodeForCategory(string $categoryName): ?string
    {
        foreach (self::curatedCategories() as $category) {
            if ($category['name'] === $categoryName) {
                return $category['segment_codes'][0] ?? null;
            }
        }

        return null;
    }
}
