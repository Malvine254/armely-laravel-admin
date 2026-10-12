<?php

namespace App\Support;

class CatalogTaxonomy
{
    /** @return array{include:array<string>,exclude:array<string>}|null */
    public static function hardwareCategoryRules(string $segment): ?array
    {
        $accessories = ['replacement', 'refill', 'warranty', 'support', 'license', 'subscription', 'service', 'software', 'accessory', 'accessories', 'care pack', 'protection plan'];
        $rules = [
            '01' => [
                'include' => ['laptop', 'notebook', 'ultrabook', 'chromebook', 'macbook', 'thinkpad', 'elitebook', 'probook', 'zbook', 'latitude', 'travelmate', 'zenbook', 'vivobook'],
                'exclude' => ['case', 'sleeve', 'bag', 'dock', 'docking', 'cable', 'adapter', 'battery', 'batteries', 'charger', 'screen protector', 'privacy filter', 'stand', 'mount', 'keyboard', 'memory module', 'memory upgrade'],
            ],
            '02' => [
                'include' => ['desktop', 'workstation', 'optiplex', 'thinkcentre', 'elitedesk', 'prodesk', 'all-in-one', 'aio pc', 'mini pc', 'mac mini', 'mac pro'],
                'exclude' => ['cable', 'adapter', 'mount', 'stand', 'memory module', 'memory upgrade', 'hard drive', 'keyboard', 'mouse', 'combo', 'webcam', 'headset', 'printer', 'scanner', 'multifunction', 'laserjet', 'inkjet', 'switch', 'router', 'firewall', 'access point', 'hub', 'managed', 'unmanaged', 'poe', 'sfp', 'kvm', 'nas', 'ups', 'mobile workstation', 'laptop', 'notebook', 'zbook'],
            ],
            '03' => [
                'include' => ['monitor', 'display', 'ultrasharp'],
                'exclude' => ['cable', 'adapter', 'mount', 'stand', 'arm', 'filter', 'privacy', 'shelf', 'replacement screen', 'laptop', 'notebook', 'desktop', 'workstation', 'tablet', 'projector', 'switch'],
            ],
            '04' => [
                'include' => ['switch', 'router', 'firewall', 'access point', 'wireless ap', 'network hub', 'cbs110', 'cbs250', 'cbs350'],
                'exclude' => ['cable', 'adapter', 'mount', 'module', 'antenna', 'power supply', 'kvm', 'keyboard', 'mouse', 'nintendo', 'gaming', 'console'],
            ],
            '06' => [
                'include' => ['printer', 'scanner', 'multifunction', 'mfp', 'laserjet', 'inkjet', 'imageformula', 'deskjet', 'officejet', 'lj pro', 'ecosys', 'imageclass', 'bizhub', 'plotter'],
                'exclude' => ['cartridge', 'toner', 'ink bottle', 'ink cartridge', 'ribbon', 'drum', 'printhead', 'paper', 'tray', 'maintenance kit', 'fuser', 'roller', 'cable', 'adapter', 'stand'],
            ],
        ];
        if (! isset($rules[$segment])) {
            return null;
        }
        $rules[$segment]['exclude'] = array_merge($accessories, $rules[$segment]['exclude']);

        return $rules[$segment];
    }

    /** @param array<string> $keywords */
    public static function keywordPattern(array $keywords): string
    {
        $phrases = array_map(static function (string $keyword): string {
            $words = explode(' ', self::normalizedSearchText($keyword));

            return implode('[^[:alnum:]]+', array_map(static fn (string $word): string => preg_quote($word, '/'), $words)).'s?';
        }, $keywords);

        return '(^|[^[:alnum:]])('.implode('|', $phrases).')([^[:alnum:]]|$)';
    }

    /** @return array<string> */
    public static function searchTerms(string $search): array
    {
        $terms = preg_split('/\s+/', mb_strtolower(trim($search))) ?: [];
        $terms = array_map(static fn (string $term): string => trim($term, " \t\n\r\0\x0B,.;:!?()[]{}\"'"), $terms);
        $stopWords = ['a', 'an', 'and', 'at', 'by', 'for', 'from', 'in', 'of', 'on', 'the', 'to', 'up', 'with'];

        $meaningful = array_values(array_unique(array_filter($terms, static fn (string $term): bool => $term !== '' && ! in_array($term, $stopWords, true))));

        return $meaningful !== [] ? $meaningful : array_values(array_unique(array_filter($terms, static fn (string $term): bool => $term !== '')));
    }

    public static function searchCategorySegment(string $term): ?string
    {
        return match ($term) {
            'laptop', 'laptops', 'notebook', 'notebooks' => '01',
            'desktop', 'desktops', 'workstation', 'workstations' => '02',
            'monitor', 'monitors', 'display', 'displays' => '03',
            'network', 'networking', 'router', 'routers', 'switch', 'switches' => '04',
            'printer', 'printers', 'scanner', 'scanners' => '06',
            default => null,
        };
    }

    /** @return array<string> */
    public static function searchVariants(string $term): array
    {
        return match ($term) {
            'laptop', 'laptops', 'notebook', 'notebooks',
            'desktop', 'desktops', 'workstation', 'workstations',
            'monitor', 'monitors', 'display', 'displays',
            'printer', 'printers' => self::hardwareCategoryRules(self::searchCategorySegment($term))['include'],
            'scanner', 'scanners' => ['scanner', 'imageformula'],
            'router', 'routers' => ['router'],
            'switch', 'switches' => ['switch'],
            'network', 'networking' => self::hardwareCategoryRules('04')['include'],
            default => [$term],
        };
    }

    public static function equipmentSearchSegment(string $search): ?string
    {
        $terms = self::searchTerms($search);
        $segment = null;
        foreach ($terms as $term) {
            $candidate = self::searchCategorySegment($term);
            if ($candidate !== null) {
                if ($segment !== null && $segment !== $candidate) {
                    return null;
                }
                $segment = $candidate;
            }
        }
        if ($segment === null) {
            return null;
        }
        $rules = self::hardwareCategoryRules($segment);
        if (preg_match('/'.self::keywordPattern(array_merge($rules['exclude'], ['ink', 'supply', 'supplies', 'kit', 'parts'])).'/i', $search)) {
            return null;
        }

        return $segment;
    }

    public const REPRESENTATIVE_IMAGE_EXCLUSIONS = [
        'cartridge', 'toner', 'ink', 'ribbon', 'drum', 'printhead', 'paper',
        'replacement', 'refill', 'maintenance kit', 'supply', 'supplies',
        'warranty', 'support', 'license', 'subscription', 'service',
    ];

    public static function isRepresentativeProduct(string $name, array $include, array $exclude): bool
    {
        if (preg_match('/'.self::keywordPattern(array_merge(self::REPRESENTATIVE_IMAGE_EXCLUSIONS, $exclude)).'/i', $name)) {
            return false;
        }

        return $include !== [] && (bool) preg_match('/'.self::keywordPattern($include).'/i', $name);
    }

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
            ['name' => 'Memory & Storage Upgrades', 'slug' => 'memory-storage-upgrades', 'segment_codes' => ['07'], 'keywords' => ['memory module', 'ram', 'dimm', 'sodimm', 'ddr4', 'ddr5', 'nvme', 'ssd', 'hdd', 'flash drive', 'usb drive', 'external drive', 'hard drive', 'solid state drive', 'sd card', 'microsd']],
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
        return array_map(static fn (array $item) => $item['name'], self::curatedCategories());
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

        $haystack = self::normalizedSearchText($sourceCategoryName.' '.$productName.' '.$description);

        foreach (self::curatedCategories() as $category) {
            foreach ($category['keywords'] as $keyword) {
                $needle = self::normalizedSearchText($keyword);
                if ($needle !== '' && str_contains(" {$haystack} ", " {$needle} ")) {
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

    private static function normalizedSearchText(string $value): string
    {
        $normalized = mb_strtolower(trim($value));
        $normalized = preg_replace('/[^\pL\pN]+/u', ' ', $normalized) ?? $normalized;

        return trim(preg_replace('/\s+/u', ' ', $normalized) ?? $normalized);
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
