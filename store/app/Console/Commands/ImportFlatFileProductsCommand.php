<?php

namespace App\Console\Commands;

use App\Jobs\EnrichPriceAvailabilityProductImageJob;
use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class ImportFlatFileProductsCommand extends Command
{
    protected $signature = 'tdsynnex:import-flatfile
        {path? : Path to .ap file or directory (defaults to SYNNEX_FLAT_FILE_PATH / flat-files)}
        {--chunk=500 : Number of rows per DB upsert batch}
        {--limit=0 : Max rows to import (0 = unlimited)}
        {--enrich-images : After import, dispatch image enrichment jobs for all products without images}';

    protected $description = 'Import TD SYNNEX .ap flat file data directly into the products table';

    public function handle(): int
    {
        $apFiles = $this->resolveApFiles();

        if (empty($apFiles)) {
            $this->error('No .ap files found. Provide a path argument or set SYNNEX_FLAT_FILE_PATH in .env.');
            return self::FAILURE;
        }

        $chunkSize = max(1, (int) $this->option('chunk'));
        $limit = max(0, (int) $this->option('limit'));

        $totalImported = 0;
        $totalSkipped = 0;

        foreach ($apFiles as $apFile) {
            $this->info("Importing: {$apFile}");
            $result = $this->importFile($apFile, $chunkSize, $limit > 0 ? $limit - $totalImported : 0);
            $totalImported += $result['imported'];
            $totalSkipped += $result['skipped'];

            $this->line("  Imported: {$result['imported']}, Skipped: {$result['skipped']}");

            if ($limit > 0 && $totalImported >= $limit) {
                break;
            }
        }

        $this->info("Done. Total imported: {$totalImported}, Total skipped: {$totalSkipped}");

        if ($this->option('enrich-images')) {
            $this->dispatchImageEnrichmentJobs();
        }

        return self::SUCCESS;
    }

    private function dispatchImageEnrichmentJobs(): void
    {
        $this->info('Queuing image enrichment jobs for products without images...');

        $productIds = Product::query()
            ->where('vendor_id', 'TD SYNNEX')
            ->where(function ($q) {
                $q->whereNull('images')->orWhere('images', '[]');
            })
            ->orderBy('id')
            ->pluck('id');

        $dispatched = 0;
        foreach ($productIds as $productId) {
            EnrichPriceAvailabilityProductImageJob::dispatch((int) $productId);
            $dispatched++;
        }

        $this->info("Queued {$dispatched} image enrichment jobs.");
        $this->line('Run workers to process them:');
        $this->line('  php artisan queue:work database --queue=products-sync --sleep=1 --timeout=120');
    }

    private function importFile(string $filePath, int $chunkSize, int $limit): array
    {
        $file = new \SplFileObject($filePath, 'r');
        $batch = [];
        $imported = 0;
        $skipped = 0;
        $bar = $this->output->createProgressBar();
        $bar->setFormat(' %current% rows [%bar%] %elapsed%');
        $bar->start();

        while (!$file->eof()) {
            $line = trim((string) $file->fgets());
            if ($line === '') {
                continue;
            }

            $parts = explode('~', $line);
            if (count($parts) < 14) {
                continue;
            }

            $recordType = strtoupper(trim((string) ($parts[1] ?? '')));
            if ($recordType !== 'DTL') {
                continue;
            }

            $sku = trim((string) ($parts[4] ?? ''));
            if ($sku === '' || !ctype_digit($sku)) {
                $skipped++;
                continue;
            }

            $row = $this->mapApRowToProduct($parts, $sku);
            if ($row === null) {
                $skipped++;
                continue;
            }

            $batch[] = $row;

            if (count($batch) >= $chunkSize) {
                $this->upsertBatch($batch);
                $imported += count($batch);
                $bar->advance(count($batch));
                $batch = [];

                if ($limit > 0 && $imported >= $limit) {
                    break;
                }
            }
        }

        if (!empty($batch)) {
            $this->upsertBatch($batch);
            $imported += count($batch);
            $bar->advance(count($batch));
        }

        $bar->finish();
        $this->newLine();

        return ['imported' => $imported, 'skipped' => $skipped];
    }

    private function mapApRowToProduct(array $parts, string $sku): ?array
    {
        $dbProductId = (int) $sku;
        if ($dbProductId <= 0) {
            return null;
        }

        $mpn = trim((string) ($parts[2] ?? ''));
        $description = trim((string) ($parts[6] ?? ''));
        $manufacturer = trim((string) ($parts[7] ?? ''));
        $status = trim((string) ($parts[5] ?? ''));
        $qty = is_numeric(trim((string) ($parts[10] ?? ''))) ? (int) $parts[10] : 0;
        $basePrice = is_numeric(trim((string) ($parts[12] ?? ''))) ? (float) $parts[12] : 0;
        $retailPrice = is_numeric(trim((string) ($parts[13] ?? ''))) ? (float) $parts[13] : 0;
        $upc = trim((string) ($parts[33] ?? ''));
        $categoryCode = trim((string) ($parts[34] ?? ''));
        $categoryName = trim((string) ($parts[35] ?? ''));
        $familyCode = trim((string) ($parts[36] ?? ''));

        $isActive = strtoupper($status) === 'A';
        $imageUrl = $this->extractImageUrlFromParts($parts);
        $images = $imageUrl !== '' ? [['imageUrl' => $imageUrl, 'source' => 'flat-file']] : [];
        $timestamp = now();

        return [
            'tdsynnex_product_id' => $dbProductId,
            'tdsynnex_sku_no' => $dbProductId,
            'vendor_id' => 'TD SYNNEX',
            'product_name' => $description !== '' ? $description : $mpn,
            'mfg_part_no' => $mpn,
            'description' => $description,
            'base_price' => $basePrice,
            'retail_price' => $retailPrice,
            'billing_model' => '',
            'billing_frequency' => '',
            'is_available' => $isActive,
            'is_discontinued' => !$isActive,
            'is_hardware' => \App\Http\Controllers\ProductController::isHardwareProduct(
                $description !== '' ? $description : $mpn,
                $description
            ) ? 1 : 0,
            'category_segment' => substr($categoryCode, 0, 2) ?: null,
            'manufacturer' => $manufacturer !== '' ? $manufacturer : null,
            'specifications' => json_encode([
                'sku' => $sku,
                'status' => $status,
                'categoryCode' => $categoryCode !== '' ? $categoryCode : '-',
                'availableQuantity' => $qty,
                'upc' => $upc,
                'manufacturer' => $manufacturer,
                'categoryName' => $categoryName,
                'familyCode' => $familyCode,
            ], JSON_UNESCAPED_UNICODE),
            'images' => json_encode($images, JSON_UNESCAPED_UNICODE),
            'last_synced_at' => $timestamp,
            'updated_at' => $timestamp,
            'created_at' => $timestamp,
        ];
    }

    private function upsertBatch(array $rows): void
    {
        Product::upsert(
            $rows,
            ['tdsynnex_product_id'],
            [
                'tdsynnex_sku_no',
                'vendor_id',
                'product_name',
                'mfg_part_no',
                'description',
                'base_price',
                'retail_price',
                'billing_model',
                'billing_frequency',
                'is_available',
                'is_discontinued',
                'is_hardware',
                'category_segment',
                'manufacturer',
                'specifications',
                'images',
                'last_synced_at',
                'updated_at',
            ]
        );
    }

    private function extractImageUrlFromParts(array $parts): string
    {
        foreach ($parts as $part) {
            $candidate = trim((string) $part);
            if ($candidate === '') {
                continue;
            }

            if (preg_match('/^https?:\/\/[^\s]+\.(?:jpg|jpeg|png|webp|gif)(?:\?.*)?$/i', $candidate)) {
                return $candidate;
            }
        }

        return '';
    }

    private function resolveApFiles(): array
    {
        $argPath = $this->argument('path');
        if ($argPath) {
            $resolved = $this->resolvePath((string) $argPath);
            if (!$resolved || !File::exists($resolved)) {
                $this->error("Path not found: {$argPath}");
                return [];
            }
            if (File::isDirectory($resolved)) {
                return array_merge(
                    File::glob($resolved . DIRECTORY_SEPARATOR . '*.ap'),
                    File::glob($resolved . DIRECTORY_SEPARATOR . '*.AP')
                );
            }
            return [$resolved];
        }

        $configPath = trim((string) config('tdsynnex.price_availability.flat_file_path', ''));
        if ($configPath !== '') {
            $resolved = $this->resolvePath($configPath);
            if ($resolved && File::exists($resolved)) {
                if (File::isDirectory($resolved)) {
                    return array_merge(
                        File::glob($resolved . DIRECTORY_SEPARATOR . '*.ap'),
                        File::glob($resolved . DIRECTORY_SEPARATOR . '*.AP')
                    );
                }
                return [$resolved];
            }
        }

        $flatFilesDir = $this->resolvePath((string) config('tdsynnex.price_availability.flat_files_dir', 'flat-files'));
        if ($flatFilesDir && File::isDirectory($flatFilesDir)) {
            return array_merge(
                File::glob($flatFilesDir . DIRECTORY_SEPARATOR . '*.ap'),
                File::glob($flatFilesDir . DIRECTORY_SEPARATOR . '*.AP')
            );
        }

        return [];
    }

    private function resolvePath(string $value): ?string
    {
        $trimmed = trim($value);
        if ($trimmed === '') {
            return null;
        }

        if (preg_match('/^[A-Za-z]:[\\\\\/]/', $trimmed) || str_starts_with($trimmed, '/') || str_starts_with($trimmed, '\\\\')) {
            return $trimmed;
        }

        return base_path($trimmed);
    }
}
