<?php

namespace App\Console\Commands;

use App\Models\AppSetting;
use App\Services\AzureGraphMailService;
use App\Services\TDSynnexService;
use Illuminate\Console\Command;

class RefreshLivePricesCommand extends Command
{
    public const STATE_KEY = 'price_sync.run_state';

    protected $signature = 'tdsynnex:refresh-live-prices
        {--scope=      : Override scope: "all" or "specific" (defaults to saved setting)}
        {--skus=       : Comma/newline-separated SKUs when scope=specific (overrides saved setting)}';

    protected $description = 'Poll TD SYNNEX price/availability, update products immediately, and send status emails.';

    public function handle(TDSynnexService $service, AzureGraphMailService $mailer): int
    {
        if (!$service->usesPriceAvailabilityAsProductSource()) {
            $this->warn('TDSYNNEX_PRODUCTS_SOURCE is not priceavailability. Skipped.');
            $this->writeState('completed', 'Skipped — TDSYNNEX_PRODUCTS_SOURCE is not priceavailability.', '');
            return self::SUCCESS;
        }

        // No execution time limit — full sync of 19k+ products may take many minutes
        @ini_set('max_execution_time', '0');

        // --scope / --skus override saved settings (used by "Run Now" without saving)
        $scopeArg = trim((string) ($this->option('scope') ?? ''));
        $skusArg  = trim((string) ($this->option('skus') ?? ''));

        if ($scopeArg !== '') {
            $syncScope    = $scopeArg;
            $specificSkus = $syncScope === 'specific' ? $this->parseSkuString($skusArg) : null;
        } else {
            $syncScope    = (string) AppSetting::getValue('price_sync.scope', 'all');
            $specificSkus = $syncScope === 'specific' ? $this->configuredSkuList() : null;
        }

        $skuCount   = $specificSkus !== null ? count($specificSkus) : null;
        $scopeLabel = $specificSkus === null
            ? 'All products in database'
            : "Specific products ({$skuCount} SKU" . ($skuCount !== 1 ? 's' : '') . ')';

        $startedAt = now()->format('Y-m-d H:i:s T');
        $this->writeState('running', "Starting — {$scopeLabel}", "Started at {$startedAt}\nScope: {$scopeLabel}");
        $this->info("Starting live price refresh — scope: {$scopeLabel}");

        $mailer->sendSyncStatusEmail('Live Price Refresh', 'started', [
            'Started At'         => $startedAt,
            'Process'            => 'Checking TD SYNNEX price, stock & availability',
            'Sync Scope'         => $scopeLabel,
            'Requested Products' => $specificSkus === null ? 'All products in database' : $skuCount,
        ]);

        try {
            $start = microtime(true);

            $onBatch = function (int $batchNum, int $totalBatches, int $checked) use ($scopeLabel, $startedAt): void {
                $elapsed = round(microtime(true) - LARAVEL_START, 1);
                $pct     = $totalBatches > 0 ? round(($batchNum / $totalBatches) * 100) : 0;
                $this->writeState(
                    'running',
                    "Batch {$batchNum}/{$totalBatches} ({$pct}%) — {$checked} products updated",
                    implode("\n", [
                        "Started at {$startedAt}",
                        "Scope: {$scopeLabel}",
                        "Progress: batch {$batchNum} of {$totalBatches} ({$pct}%)",
                        "Products updated so far: {$checked}",
                        "Elapsed: {$elapsed}s",
                    ])
                );
            };

            $result  = $service->refreshLivePricesInDatabase($specificSkus, $onBatch);
            $elapsed = round(microtime(true) - $start, 1);

            $batchErrors  = $result['batch_errors'] ?? [];
            $errorCount   = count($batchErrors);
            $statusLabel  = $errorCount > 0 ? 'completed_with_errors' : 'completed';
            $summaryLines = [
                "Finished at " . now()->format('Y-m-d H:i:s T'),
                "Scope:            {$scopeLabel}",
                "Products updated: {$result['checked']} of {$result['requested']} requested",
                "Duration:         {$elapsed}s",
                "Price, stock & availability columns updated immediately.",
            ];
            if ($errorCount > 0) {
                $summaryLines[] = "";
                $summaryLines[] = "Batch errors ({$errorCount}) — skipped batches (network/timeout):";
                foreach (array_slice($batchErrors, 0, 10) as $err) {
                    $summaryLines[] = "  • {$err}";
                }
                if ($errorCount > 10) {
                    $summaryLines[] = "  … and " . ($errorCount - 10) . " more (see server logs)";
                }
            }
            $summary = implode("\n", $summaryLines);

            $this->info("Products updated: {$result['checked']} of {$result['requested']}");
            if ($errorCount > 0) {
                $this->warn("Batch errors (skipped): {$errorCount}");
            }
            $this->line("Duration: {$elapsed}s");
            $this->writeState($statusLabel, "Done — {$result['checked']} product(s) updated in {$elapsed}s" . ($errorCount > 0 ? " ({$errorCount} batch error(s))" : ''), $summary);

            $mailer->sendSyncStatusEmail('Live Price Refresh', $errorCount > 0 ? 'completed_with_errors' : 'completed', [
                'Finished At'      => now()->format('Y-m-d H:i:s T'),
                'Sync Scope'       => $scopeLabel,
                'Products Updated' => "{$result['checked']} of {$result['requested']} requested",
                'Batch Errors'     => $errorCount > 0 ? "{$errorCount} batch(es) skipped (timeout/network)" : 'None',
                'Duration (s)'     => $elapsed,
                'Columns Updated'  => 'price, stock quantity, availability — applied immediately',
            ]);

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('Live price refresh failed: ' . $e->getMessage());
            $this->writeState('failed', 'Failed: ' . $e->getMessage(), "Error: " . $e->getMessage());

            $mailer->sendSyncStatusEmail('Live Price Refresh', 'failed', [
                'Error' => $e->getMessage(),
            ]);

            return self::FAILURE;
        }
    }

    private function writeState(string $status, string $message, string $output): void
    {
        try {
            $existing = AppSetting::getValue(self::STATE_KEY, []);
            AppSetting::setValue(self::STATE_KEY, [
                'status'      => $status,
                'message'     => $message,
                'output'      => $output,
                'started_at'  => is_array($existing) ? ($existing['started_at'] ?? now()->toDateTimeString()) : now()->toDateTimeString(),
                'updated_at'  => now()->toDateTimeString(),
                'finished_at' => in_array($status, ['completed', 'failed']) ? now()->toDateTimeString() : null,
            ]);
        } catch (\Throwable) {}
    }

    private function parseSkuString(string $raw): array
    {
        $tokens = preg_split('/[\s,;]+/', $raw) ?: [];
        return array_values(array_unique(array_filter(array_map('trim', $tokens), fn ($v) => $v !== '')));
    }

    private function configuredSkuList(): array
    {
        $raw = (string) AppSetting::getValue('price_sync.skus', '');
        return $this->parseSkuString($raw);
    }
}
