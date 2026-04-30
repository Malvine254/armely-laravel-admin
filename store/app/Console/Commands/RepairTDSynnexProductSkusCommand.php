<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Services\TDSynnexService;
use Illuminate\Console\Command;

class RepairTDSynnexProductSkusCommand extends Command
{
    protected $signature = 'tdsynnex:repair-product-skus
                            {--force : Apply database changes}
                            {--disable-missing : Mark SKUs not returned by TD SYNNEX as unavailable/discontinued}
                            {--clear-images : Clear existing images when repairing product identity mismatches}
                            {--limit=0 : Maximum products to inspect}
                            {--chunk=50 : Live API batch size}
                            {--timeout=30 : PriceAvailability request timeout in seconds}';

    protected $description = 'Verify local TD SYNNEX SKUs against live PriceAvailability and repair mismatched product identity data';

    public function handle(TDSynnexService $service): int
    {
        $apply = (bool) $this->option('force');
        $disableMissing = (bool) $this->option('disable-missing');
        $clearImages = (bool) $this->option('clear-images');
        $limit = max(0, (int) $this->option('limit'));
        $chunkSize = max(1, min(50, (int) $this->option('chunk')));
        $timeout = max(8, (int) $this->option('timeout'));

        config(['tdsynnex.price_availability.request_timeout' => $timeout]);

        $reflector = new \ReflectionClass($service);
        $call = function (string $method, array $args = []) use ($service, $reflector) {
            $reflectedMethod = $reflector->getMethod($method);
            $reflectedMethod->setAccessible(true);

            return $reflectedMethod->invokeArgs($service, $args);
        };

        $query = Product::query()
            ->where('vendor_id', 'TD SYNNEX')
            ->whereNotNull('tdsynnex_sku_no')
            ->where('base_price', '>', 0)
            ->orderBy('id');

        $total = (clone $query)->count();
        if ($limit > 0) {
            $total = min($total, $limit);
        }

        $checked = 0;
        $found = 0;
        $repaired = 0;
        $disabled = 0;
        $mismatched = 0;
        $errors = [];
        $examples = [];

        $this->info(($apply ? 'Repairing' : 'Dry-running') . " {$total} TD SYNNEX products...");

        $query->chunk($chunkSize, function ($products) use (
            $service,
            $call,
            $apply,
            $limit,
            &$checked,
            &$found,
            &$repaired,
            &$disabled,
            &$mismatched,
            &$errors,
            &$examples
        ) {
            if ($limit > 0 && $checked >= $limit) {
                return false;
            }

            if ($limit > 0 && ($checked + $products->count()) > $limit) {
                $products = $products->take($limit - $checked);
            }

            $skus = $products->pluck('tdsynnex_sku_no')->map(fn ($value) => (string) $value)->all();

            try {
                $metadata = $call('readApMetadata', [$skus]);
                $xml = $call('buildPriceAvailabilityXmlPayload', [$skus]);
                $response = $service->postPriceAvailabilityXml($xml, 'us', false);
                $rows = $call('extractPriceAvailabilityRows', [$response]);

                $liveBySku = [];
                foreach ($rows as $row) {
                    $normalized = $call('normalizePriceAvailabilityProduct', [$row, $metadata]);
                    $sku = trim((string) ($normalized['sku'] ?? $normalized['productId'] ?? ''));
                    if ($sku !== '') {
                        $liveBySku[$sku] = $normalized;
                    }
                }

                foreach ($products as $product) {
                    $checked++;
                    $sku = (string) $product->tdsynnex_sku_no;
                    $live = $liveBySku[$sku] ?? null;

                    if (!$live) {
                        $mismatched++;
                        if ($disableMissing) {
                            $disabled++;
                        }

                        if ($apply && $disableMissing) {
                            $spec = is_array($product->specifications) ? $product->specifications : [];
                            $spec['sku'] = $sku;
                            $spec['status'] = 'not found';
                            $spec['repair_note'] = 'Disabled because live TD SYNNEX PriceAvailability did not return this SKU.';

                            Product::query()->whereKey($product->id)->update([
                                'is_available' => false,
                                'is_discontinued' => true,
                                'specifications' => json_encode($spec, JSON_UNESCAPED_UNICODE),
                                'last_synced_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }

                        $this->captureExample($examples, [
                            'id' => $product->id,
                            'sku' => $sku,
                            'action' => $disableMissing ? 'disabled_not_found' : 'reported_not_found',
                            'db_name' => $product->product_name,
                            'db_mfg' => $product->mfg_part_no,
                        ]);
                        continue;
                    }

                    $found++;
                    $payload = $service->mapPriceAvailabilityCatalogProductToDatabaseRow($live);
                    if ($payload === null) {
                        continue;
                    }

                    $liveMfg = strtoupper(trim((string) ($payload['mfg_part_no'] ?? '')));
                    $dbMfg = strtoupper(trim((string) $product->mfg_part_no));
                    $identityMismatch = $liveMfg !== '' && $liveMfg !== $dbMfg;

                    if (!$identityMismatch) {
                        continue;
                    }

                    $mismatched++;
                    $repaired++;

                    $updates = $payload;
                    unset($updates['created_at']);
                    if ($clearImages) {
                        // Optional: clear stale Dell/HP/etc. images that may belong to the old identity.
                        $updates['images'] = json_encode([], JSON_UNESCAPED_UNICODE);
                    } else {
                        unset($updates['images']);
                    }

                    if ($apply) {
                        Product::query()->whereKey($product->id)->update($updates);
                    }

                    $this->captureExample($examples, [
                        'id' => $product->id,
                        'sku' => $sku,
                        'action' => 'repaired_identity',
                        'old_name' => $product->product_name,
                        'new_name' => $payload['product_name'] ?? '',
                        'old_mfg' => $product->mfg_part_no,
                        'new_mfg' => $payload['mfg_part_no'] ?? '',
                    ]);
                }
            } catch (\Throwable $e) {
                $errors[] = [
                    'first_sku' => $skus[0] ?? null,
                    'last_sku' => end($skus) ?: null,
                    'error' => preg_replace('/\s+/', ' ', $e->getMessage()),
                ];
            }

            if ($checked > 0 && $checked % 1000 === 0) {
                $this->line("Checked {$checked}; repaired {$repaired}; disabled {$disabled}; errors " . count($errors));
            }

            return $limit <= 0 || $checked < $limit;
        });

        $this->newLine();
        $this->info('TD SYNNEX SKU repair complete.');
        $this->line('Mode: ' . ($apply ? 'applied' : 'dry-run'));
        $this->line("Checked: {$checked}");
        $this->line("Found live: {$found}");
        $this->line("Identity issues: {$mismatched}");
        $this->line("Repaired: {$repaired}");
        $this->line("Disabled not found: {$disabled}");
        $this->line('API errors: ' . count($errors));

        if (!empty($examples)) {
            $this->table(array_keys($examples[0]), $examples);
        }

        if (!empty($errors)) {
            $this->warn('First API errors:');
            $this->line(json_encode(array_slice($errors, 0, 5), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        }

        return empty($errors) ? self::SUCCESS : self::FAILURE;
    }

    private function captureExample(array &$examples, array $example): void
    {
        if (count($examples) < 10) {
            $examples[] = $example;
        }
    }
}
