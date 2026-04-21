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
            ['name' => 'Laptops & Notebooks',      'slug' => 'laptops-notebooks',       'segment_codes' => ['01'], 'keywords' => ['laptop', 'notebook', 'ultrabook', 'chromebook', 'macbook']],
            ['name' => 'Desktops & Workstations',  'slug' => 'desktops-workstations',   'segment_codes' => ['02'], 'keywords' => ['desktop', 'workstation', 'all-in-one', 'aio pc', 'mac mini', 'mac pro', 'optiplex', 'thinkcentre', 'elitedesk', 'prodesk']],
            ['name' => 'Monitors & Displays',       'slug' => 'monitors-displays',       'segment_codes' => ['03'], 'keywords' => ['monitor', 'display', 'screen', 'lcd', 'led monitor', 'oled', 'ultrasharp', 'e-series display']],
            ['name' => 'Networking',                'slug' => 'networking',              'segment_codes' => ['04'], 'keywords' => ['switch', 'router', 'firewall', 'access point', 'wi-fi', 'wifi', 'wireless ap', 'network hub', 'meraki', 'fortinet', 'ubiquiti', 'aruba', 'cbs350', 'usw-pro', 'sg350']],
            ['name' => 'Servers & Storage',         'slug' => 'servers-storage',         'segment_codes' => ['05'], 'keywords' => ['server', 'poweredge', 'proliant', 'thinksystem', 'nas', 'san', 'raid', 'network attached', 'synology', 'qnap', 'storage array', 'ssd array']],
            ['name' => 'Printers & Scanners',       'slug' => 'printers-scanners',       'segment_codes' => ['06'], 'keywords' => ['printer', 'scanner', 'multifunction', 'mfp', 'plotter', 'laserjet', 'inkjet', 'label printer', 'document scanner', 'imageFORMULA', 'fi-']],
            ['name' => 'Memory & Storage Upgrades', 'slug' => 'memory-storage-upgrades', 'segment_codes' => ['07'], 'keywords' => ['memory module', 'ram', 'dimm', 'sodimm', 'nvme', 'flash drive', 'usb drive', 'external drive', 'hard drive', 'solid state drive']],
            ['name' => 'Docking Stations & Hubs',   'slug' => 'docking-stations-hubs',  'segment_codes' => ['08'], 'keywords' => ['docking station', 'dock station', 'thunderbolt dock', 'usb-c dock', 'universal dock', 'wd19', 'wd22', 'thinkpad dock', 'hp dock', 'caldigit']],
            // Video Conferencing must be checked before Peripherals so bar/speakerphone products
            // are not swallowed by the generic 'speaker' or 'headset' keywords below.
            ['name' => 'Video Conferencing',        'slug' => 'video-conferencing',      'segment_codes' => ['11'], 'keywords' => [
                'video conferencing', 'video conference', 'conference camera', 'conference room',
                'teams room', 'zoom room', 'meeting room', 'room system', 'room kit',
                'rally bar', 'rally plus', 'meetingbar', 'meeting bar', 'video bar',
                'speakerphone', 'speak2', 'jabra speak', 'jabra panacast', 'panacast',
                'poly studio', 'poly sync', 'poly eagle', 'poly trio',
                'logitech tap', 'logitech scribe', 'logitech sight',
                'neat bar', 'neat board', 'neat frame', 'neat pad',
                'yealink', 'cisco webex', 'cisco room', 'cisco codec',
                'aver cam', 'huddly', 'dten', 'barco clickshare', 'owl labs',
                'collab bar', 'all-in-one bar', 'video soundbar',
            ]],
            ['name' => 'Peripherals',               'slug' => 'peripherals',             'segment_codes' => ['09'], 'keywords' => ['keyboard', 'mouse', 'mice', 'headset', 'headphone', 'webcam', 'web camera', 'peripheral', 'trackball', 'stylus', 'ergonomic', 'numpad', 'presenter remote']],
            ['name' => 'Power & UPS',               'slug' => 'power-ups',               'segment_codes' => ['10'], 'keywords' => ['ups', 'uninterruptible', 'power supply', 'battery backup', 'surge protector', 'pdu', 'power strip', 'power distribution']],
            ['name' => 'Cables & Adapters',         'slug' => 'cables-adapters',         'segment_codes' => ['12'], 'keywords' => ['cable', 'adapter', 'dongle', 'hdmi', 'displayport', 'usb-c cable', 'thunderbolt cable', 'patch cable', 'cat6', 'cat6a', 'fiber']],
            ['name' => 'Security Hardware',         'slug' => 'security-hardware',       'segment_codes' => ['13'], 'keywords' => ['security camera', 'surveillance', 'ip camera', 'nvr', 'dvr', 'biometric', 'access control', 'smart card', 'yubikey', 'security key', 'cable lock', 'kensington lock', 'badge reader', 'card reader']],
            ['name' => 'Mobile & Tablets',          'slug' => 'mobile-tablets',          'segment_codes' => ['14'], 'keywords' => ['tablet', 'ipad', 'android tablet', 'rugged tablet', 'toughbook', 'galaxy tab', 'surface go', 'surface pro']],
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
