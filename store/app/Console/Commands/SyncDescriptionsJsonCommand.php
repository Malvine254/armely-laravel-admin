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

            $description = trim((string) ($entry['description'] ?? ''));
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
}
