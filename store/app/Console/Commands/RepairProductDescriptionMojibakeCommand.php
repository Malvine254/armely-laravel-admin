<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Services\CatalogOperationStateService;
use App\Support\MojibakeRepairer;
use Illuminate\Console\Command;

class RepairProductDescriptionMojibakeCommand extends Command
{
    protected $signature = 'products:repair-description-mojibake
        {--chunk=500 : Number of products to load per batch}
        {--report-progress : Publish live progress for the Catalog Ops screen}';

    protected $description = 'Repair "??" / "???" mojibake artifacts (from lossy feed encodings) in every product description, regardless of source';

    public function handle(CatalogOperationStateService $stateService): int
    {
        $chunkSize = max(50, (int) $this->option('chunk'));
        $reportProgress = (bool) $this->option('report-progress');

        $total = Product::query()
            ->where('description', 'like', '%??%')
            ->count();

        $scanned = 0;
        $updated = 0;
        $unchanged = 0;
        $startedAt = microtime(true);
        $lastReportAt = 0.0;

        Product::query()
            ->where('description', 'like', '%??%')
            ->select(['id', 'description'])
            ->chunkById($chunkSize, function ($products) use (&$scanned, &$updated, &$unchanged, $total, $reportProgress, $stateService, $startedAt, &$lastReportAt) {
                foreach ($products as $product) {
                    $scanned++;

                    $repaired = MojibakeRepairer::repair((string) $product->description);
                    if ($repaired !== $product->description) {
                        $product->forceFill(['description' => $repaired])->save();
                        $updated++;
                    } else {
                        $unchanged++;
                    }

                    $now = microtime(true);
                    if ($reportProgress && ($scanned === 1 || $scanned % 200 === 0 || ($now - $lastReportAt) >= 5)) {
                        $lastReportAt = $now;
                        $percent = $total > 0 ? min(99.9, round(($scanned / $total) * 100, 1)) : 0;
                        $elapsed = max(0.1, $now - $startedAt);

                        $stateService->progress(
                            sprintf('Description repair %.1f%% - scanned %s, updated %s', $percent, number_format($scanned), number_format($updated)),
                            [
                                'percent' => $percent,
                                'scanned' => $scanned,
                                'matched' => $scanned,
                                'updated' => $updated,
                                'unchanged' => $unchanged,
                                'elapsed_seconds' => (int) round($elapsed),
                                'remaining_seconds' => $total > 0 ? max(0, (int) round(($elapsed / $scanned) * ($total - $scanned))) : null,
                                'records_per_second' => round($scanned / $elapsed, 1),
                            ]
                        );
                    }
                }
            });

        if ($reportProgress) {
            $elapsed = max(0.1, microtime(true) - $startedAt);
            $stateService->progress(
                sprintf('Description repair complete - scanned %s, updated %s', number_format($scanned), number_format($updated)),
                [
                    'percent' => 100,
                    'scanned' => $scanned,
                    'matched' => $scanned,
                    'updated' => $updated,
                    'unchanged' => $unchanged,
                    'elapsed_seconds' => (int) round($elapsed),
                    'remaining_seconds' => 0,
                    'records_per_second' => round($scanned / $elapsed, 1),
                ]
            );
        }

        $this->info("Scanned {$scanned} product(s) with '??' in the description; repaired {$updated}; unchanged {$unchanged}.");

        return self::SUCCESS;
    }
}
