<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Services\CatalogOperationStateService;
use Illuminate\Console\Command;

class SyncDescriptionsJsonCommand extends Command
{
    protected $signature = 'descriptions:sync-json
        {path? : Path to descriptions.json (defaults to descriptions/descriptions.json)}
        {--limit=0 : Maximum matching existing products to update; 0 updates all}
        {--report-progress : Publish live progress for the Catalog Ops screen}';

    protected $description = 'Sync product descriptions and manufacturer metadata from descriptions/descriptions.json, matched by TD SYNNEX SKU';

    public function handle(CatalogOperationStateService $stateService): int
    {
        $path = $this->argument('path') ?: base_path('descriptions/descriptions.json');
        $path = $this->absolutePath((string) $path);
        if (!is_file($path)) {
            $this->error("Descriptions file not found: {$path}");
            return self::FAILURE;
        }

        $entries = json_decode((string) file_get_contents($path), true);
        if (!is_array($entries)) {
            $this->error('Descriptions file is not valid JSON array.');
            return self::FAILURE;
        }

        $products = Product::query()
            ->where('vendor_id', 'TD SYNNEX')
            ->whereNotNull('tdsynnex_sku_no')
            ->get(['id', 'tdsynnex_sku_no', 'description', 'manufacturer', 'mfg_part_no', 'specifications'])
            ->keyBy(fn (Product $product) => (string) $product->tdsynnex_sku_no);

        $limit = max(0, (int) $this->option('limit'));
        $reportProgress = (bool) $this->option('report-progress');
        $total = count($entries);
        $scanned = 0;
        $matched = 0;
        $updated = 0;
        $unchanged = 0;
        $startedAt = microtime(true);
        $lastReportAt = 0.0;

        foreach ($entries as $entry) {
            $scanned++;

            $now = microtime(true);
            if ($reportProgress && ($scanned === 1 || $scanned % 500 === 0 || ($now - $lastReportAt) >= 5)) {
                $lastReportAt = $now;
                $percent = $total > 0 ? min(99.9, round(($scanned / $total) * 100, 1)) : 0;
                $elapsed = max(0.1, $now - $startedAt);

                $stateService->progress(
                    sprintf('Descriptions sync %.1f%% - scanned %s, matched %s, updated %s', $percent, number_format($scanned), number_format($matched), number_format($updated)),
                    [
                        'percent' => $percent,
                        'scanned' => $scanned,
                        'matched' => $matched,
                        'updated' => $updated,
                        'unchanged' => $unchanged,
                        'elapsed_seconds' => (int) round($elapsed),
                        'remaining_seconds' => $total > 0 ? max(0, (int) round(($elapsed / $scanned) * ($total - $scanned))) : null,
                        'records_per_second' => round($scanned / $elapsed, 1),
                    ]
                );
            }

            $sku = trim((string) ($entry['sku'] ?? ''));
            if ($sku === '') {
                continue;
            }

            /** @var Product|null $product */
            $product = $products->get($sku);
            if (!$product) {
                continue;
            }

            $matched++;

            $description = $this->repairMojibake(trim((string) ($entry['description'] ?? '')));
            $manufacturer = trim((string) ($entry['manufacturer'] ?? ''));
            $mfgPartNo = trim((string) ($entry['manufacturerPartNumber'] ?? ''));

            $specifications = is_array($product->specifications) ? $product->specifications : [];
            if (isset($entry['catalogData']) && is_array($entry['catalogData'])) {
                $specifications = array_merge($specifications, [
                    'descriptionsJsonMetadata' => [
                        'source' => $entry['descriptionSource'] ?? null,
                        'upc' => $entry['catalogData']['upc'] ?? null,
                        'categoryCode' => $entry['catalogData']['categoryCode'] ?? null,
                        'productUrl' => $entry['armelyProductUrl'] ?? null,
                    ],
                ]);
            }

            $product->forceFill([
                'description' => $description !== '' ? $description : $product->description,
                'manufacturer' => $manufacturer !== '' ? $manufacturer : $product->manufacturer,
                'mfg_part_no' => $mfgPartNo !== '' ? $mfgPartNo : $product->mfg_part_no,
                'specifications' => $specifications,
            ]);

            if ($product->isDirty()) {
                $product->save();
                $updated++;
            } else {
                $unchanged++;
            }

            if ($limit > 0 && $matched >= $limit) {
                break;
            }
        }

        if ($reportProgress) {
            $stateService->progress(
                sprintf('Descriptions sync complete - matched %s, updated %s', number_format($matched), number_format($updated)),
                [
                    'percent' => 100,
                    'scanned' => $scanned,
                    'matched' => $matched,
                    'updated' => $updated,
                    'unchanged' => $unchanged,
                    'elapsed_seconds' => (int) round(microtime(true) - $startedAt),
                    'remaining_seconds' => 0,
                    'records_per_second' => round($scanned / max(0.1, microtime(true) - $startedAt), 1),
                ]
            );
        }

        $this->info("Scanned {$scanned} entries; matched {$matched} existing products; updated {$updated}; unchanged {$unchanged}.");

        return self::SUCCESS;
    }

    private function absolutePath(string $path): string
    {
        if (preg_match('/^[A-Za-z]:[\\\\\/]/', $path) === 1 || str_starts_with($path, DIRECTORY_SEPARATOR)) {
            return $path;
        }

        return base_path($path);
    }

    /**
     * Feed vendors sometimes export descriptions through a lossy single-byte codec that
     * replaces each byte of a multi-byte UTF-8 character with '?'. Restore the most common
     * offenders (contractions, registered/trademark marks, em dashes) so text renders cleanly.
     */
    private function repairMojibake(string $text): string
    {
        if (!str_contains($text, '??')) {
            return $text;
        }

        // Known multi-letter word that always corrupts to this exact pattern.
        $text = str_replace('T??V', 'TÜV', $text);

        // Contractions: don???t, isn???t, that???s, we???ve, you???re, today???s, I???m.
        $text = preg_replace('/(\w)\?\?\?(t|s|d|ll|re|ve|m)\b/i', "$1'$2", $text) ?? $text;

        // Lowercase plural possessive: displays??? brightness -> displays' brightness.
        $text = preg_replace('/([a-z]s)\?\?\?(?=\s)/', "$1'", $text) ?? $text;

        // Em dash surrounded by spaces: day ??? night -> day (em dash) night.
        $text = preg_replace('/(?<=\s)\?\?\?(?=\s)/', '—', $text) ?? $text;

        // Remaining triple marks after a word are trademark symbols.
        $text = preg_replace('/(?<=[A-Za-z0-9])\?\?\?/', '™', $text) ?? $text;

        // Remaining double marks after a word (not followed by another letter) are registered marks.
        $text = preg_replace('/(?<=[A-Za-z0-9])\?\?(?![A-Za-z0-9])/', '®', $text) ?? $text;

        return $text;
    }
}
