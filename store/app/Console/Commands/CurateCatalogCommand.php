<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Support\CatalogTaxonomy;
use Illuminate\Console\Command;

class CurateCatalogCommand extends Command
{
    protected $signature = 'tdsynnex:curate-catalog
        {--dry-run : Show what would change without writing to the database}
        {--total=10000 : Approximate total products to keep across all categories}';

    protected $description = 'Narrow the product catalog to ~10 000 high-demand B2B items by scoring each product on brand, stock, and price';

    // ── Brand tier lookup ─────────────────────────────────────────────────────
    private const BRAND_TIER_1 = [
        'CISCO', 'HEWLETT PACKARD', 'HP INC', 'DELL', 'LENOVO',
        'MICROSOFT', 'SAMSUNG', 'APPLE', 'JUNIPER', 'FORTINET',
        'ARUBA', 'PALO ALTO', 'CHECK POINT', 'NETSCOUT',
    ];

    private const BRAND_TIER_2 = [
        'LOGITECH', 'APC BY SCHNEIDER', 'EATON', 'JABRA', 'KENSINGTON',
        'LG ELECTRONICS', 'VIEWSONIC', 'CANON USA', 'XEROX', 'LEXMARK',
        'SYNOLOGY', 'AXIS COMMUNICATIONS', 'RICOH', 'RUCKUS', 'NETGEAR',
        'PLANTRONICS', 'POLY ', 'YEALINK', 'VERTIV', 'CYBERPOWER',
        'EPSON', 'BROTHER', 'ZEBRA', 'HONEYWELL', 'BARCO',
    ];

    private const BRAND_TIER_3 = [
        'STARTECH', 'C2G', 'BELKIN', 'PANDUIT', 'CHIEF MANUF',
        'BLACK BOX', 'BUFFALO', 'MICRON', 'HAVIS', 'ROCSTOR',
        'INTEL', 'CRUCIAL', 'WESTERN DIGITAL', 'SEAGATE', 'TOSHIBA',
    ];

    /**
     * Per-segment targets that sum to approximately --total.
     * Segments with fewer products than the target keep everything they have.
     */
    private function segmentTargets(int $total): array
    {
        // Proportional allocation tuned for B2B demand.
        // Adjust the weights here to shift emphasis between categories.
        $weights = [
            '09' => 17,  // Peripherals          – large pool, trim hard
            '04' => 13,  // Networking            – Cisco, enterprise switches
            '03' => 12,  // Monitors & Displays
            '10' =>  8,  // Power & UPS
            '06' =>  7,  // Printers & Scanners
            '07' =>  7,  // Memory & Storage Upgrades
            '14' =>  5,  // Mobile & Tablets
            '12' =>  5,  // Cables & Adapters     – trim cables significantly
            '02' =>  5,  // Desktops & Workstations
            '05' =>  5,  // Servers & Storage
            '01' =>  5,  // Laptops & Notebooks   – keep all (only 153)
            '13' =>  3,  // Security Hardware
            '11' =>  2,  // Video Conferencing
            '08' =>  1,  // Docking Stations & Hubs
            ''   =>  1,  // Other / uncategorised
        ];

        $sum = array_sum($weights);
        $targets = [];
        foreach ($weights as $seg => $w) {
            $targets[$seg] = (int) round(($w / $sum) * $total);
        }

        return $targets;
    }

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $total  = max(100, (int) $this->option('total'));

        $targets = $this->segmentTargets($total);

        $segMap = [];
        foreach (CatalogTaxonomy::curatedCategories() as $c) {
            foreach ($c['segment_codes'] as $s) {
                $segMap[$s] = $c['name'];
            }
        }

        $this->info($dryRun ? '[DRY RUN] Catalog curation preview' : 'Curating catalog…');
        $this->newLine();

        $keepIds   = [];
        $dropIds   = [];
        $tableRows = [];

        foreach ($targets as $segment => $target) {
            $products = Product::where('vendor_id', 'TD SYNNEX')
                ->where('is_available', true)
                ->where('category_segment', $segment)
                ->get(['id', 'manufacturer', 'base_price', 'specifications']);

            if ($products->isEmpty()) {
                continue;
            }

            // Score every product in this segment
            $scored = $products->map(function ($p) {
                return ['id' => $p->id, 'score' => $this->score($p)];
            })->sortByDesc('score')->values();

            $keep = $scored->take($target)->pluck('id')->all();
            $drop = $scored->skip($target)->pluck('id')->all();

            $keepIds = array_merge($keepIds, $keep);
            $dropIds = array_merge($dropIds, $drop);

            $catName = $segMap[$segment] ?? ($segment === '' ? 'Other' : "Segment {$segment}");
            $tableRows[] = [
                $catName,
                $products->count(),
                count($keep),
                count($drop),
            ];
        }

        $this->table(
            ['Category', 'Available now', 'Kept', 'Dropped'],
            $tableRows
        );

        $this->newLine();
        $this->info('Total kept: ' . count($keepIds) . ' | Total dropped: ' . count($dropIds));

        if ($dryRun) {
            $this->warn('Dry run – no changes written.');
            return self::SUCCESS;
        }

        if (!$this->confirm('Apply curation and mark dropped products unavailable?', true)) {
            $this->line('Cancelled.');
            return self::SUCCESS;
        }

        // Mark dropped products unavailable in batches of 500
        foreach (array_chunk($dropIds, 500) as $chunk) {
            Product::whereIn('id', $chunk)->update(['is_available' => false]);
        }

        $this->info('Done. ' . count($keepIds) . ' products remain available.');
        return self::SUCCESS;
    }

    // ── Scoring ───────────────────────────────────────────────────────────────

    private function score(Product $product): float
    {
        $score        = 0.0;
        $manufacturer = strtoupper(trim((string) ($product->manufacturer ?? '')));
        $specs        = is_array($product->specifications) ? $product->specifications : [];
        $qty          = (int) ($specs['availableQuantity'] ?? 0);
        $price        = (float) $product->base_price;

        // Brand tier bonus
        $score += $this->brandBonus($manufacturer);

        // Stock quantity: log scale so a product with 1 000 units isn't 1 000× better than 1
        $score += log($qty + 1) * 12;

        // Price sweet-spot for B2B ($200 – $2 000 is the typical corporate procurement range)
        if ($price >= 200 && $price <= 2000) {
            $score += 20;
        } elseif ($price >= 100 && $price < 200) {
            $score += 8;
        }

        return $score;
    }

    private function brandBonus(string $manufacturer): float
    {
        foreach (self::BRAND_TIER_1 as $brand) {
            if (str_contains($manufacturer, $brand)) {
                return 100.0;
            }
        }
        foreach (self::BRAND_TIER_2 as $brand) {
            if (str_contains($manufacturer, $brand)) {
                return 60.0;
            }
        }
        foreach (self::BRAND_TIER_3 as $brand) {
            if (str_contains($manufacturer, $brand)) {
                return 30.0;
            }
        }
        return 0.0;
    }
}
