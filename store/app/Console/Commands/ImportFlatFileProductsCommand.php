<?php

namespace App\Console\Commands;

use App\Jobs\EnrichPriceAvailabilityProductImageJob;
use App\Models\Category;
use App\Models\Product;
use App\Support\CatalogTaxonomy;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class ImportFlatFileProductsCommand extends Command
{
    protected $signature = 'tdsynnex:import-flatfile
        {path? : Path to .ap file or directory (defaults to SYNNEX_FLAT_FILE_PATH / flat-files)}
        {--chunk=500 : Number of rows per DB upsert batch}
        {--limit=0 : Max rows to import (0 = unlimited)}
        {--min-price=100 : Skip products whose base price is below this (0 = no floor)}
        {--max-price=3000 : Skip products whose base price exceeds this (0 = no cap)}
        {--min-qty=1 : Skip products with available quantity below this}
        {--insert-only : Skip existing products; only insert brand-new SKUs (preserves curated is_available)}
        {--enrich-images : After import, dispatch image enrichment jobs for all products without images}';

    protected $description = 'Import TD SYNNEX .ap flat file data directly into the products table';

    public function handle(): int
    {
        $apFiles = $this->resolveApFiles();

        if (empty($apFiles)) {
            $this->error('No .ap files found. Provide a path argument or set SYNNEX_FLAT_FILE_PATH in .env.');
            return self::FAILURE;
        }

        $chunkSize  = max(1, (int) $this->option('chunk'));
        $limit      = max(0, (int) $this->option('limit'));
        $minPrice   = (float) $this->option('min-price');
        $maxPrice   = (float) $this->option('max-price');
        $minQty     = max(1, (int) $this->option('min-qty'));
        $insertOnly = (bool) $this->option('insert-only');

        $priceRange = ($minPrice > 0 ? "\${$minPrice}" : '$0') . ($maxPrice > 0 ? " – \${$maxPrice}" : '+');
        $mode = $insertOnly ? 'insert-only (new SKUs only, existing records untouched)' : 'upsert (update existing)';
        $this->info("Filters: in-stock (qty >= {$minQty}), price {$priceRange}, hardware only, active status only");
        $this->info("Mode: {$mode}");

        $totalImported = 0;
        $totalSkipped  = 0;

        foreach ($apFiles as $apFile) {
            $this->info("Importing: {$apFile}");
            $result = $this->importFile($apFile, $chunkSize, $limit > 0 ? $limit - $totalImported : 0, $minPrice, $maxPrice, $minQty, $insertOnly);
            $totalImported += $result['imported'];
            $totalSkipped  += $result['skipped'];

            $skippedDetail = "inactive={$result['skipped_inactive']}, out-of-stock={$result['skipped_stock']}, over-price={$result['skipped_price']}, non-hardware={$result['skipped_hardware']}, no-category={$result['skipped_category']}";
            $this->line("  Inserted: {$result['imported']}, Skipped: {$result['skipped']} ({$skippedDetail})");

            if ($limit > 0 && $totalImported >= $limit) {
                break;
            }
        }

        $this->info("Done. Total inserted: {$totalImported}, Total skipped: {$totalSkipped}");

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

    private function importFile(string $filePath, int $chunkSize, int $limit, float $minPrice, float $maxPrice, int $minQty, bool $insertOnly = false): array
    {
        $file    = new \SplFileObject($filePath, 'r');
        $batch   = [];
        $imported         = 0;
        $skipped          = 0;
        $skippedInactive  = 0;
        $skippedStock     = 0;
        $skippedPrice     = 0;
        $skippedHardware  = 0;
        $skippedCategory  = 0;

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

            $result = $this->mapApRowToProduct($parts, $sku, $minPrice, $maxPrice, $minQty);

            if ($result === null) {
                $skipped++;
                continue;
            }

            if (is_string($result)) {
                $skipped++;
                match ($result) {
                    'inactive'    => $skippedInactive++,
                    'stock'       => $skippedStock++,
                    'price'       => $skippedPrice++,
                    'hardware'    => $skippedHardware++,
                    'category'    => $skippedCategory++,
                    default       => null,
                };
                continue;
            }

            $batch[] = $result;

            if (count($batch) >= $chunkSize) {
                $inserted = $this->writeBatch($batch, $insertOnly);
                $imported += $inserted;
                $bar->advance(count($batch));
                $batch = [];

                if ($limit > 0 && $imported >= $limit) {
                    break;
                }
            }
        }

        if (!empty($batch)) {
            $inserted = $this->writeBatch($batch, $insertOnly);
            $imported += $inserted;
            $bar->advance(count($batch));
        }

        $bar->finish();
        $this->newLine();

        return [
            'imported'         => $imported,
            'skipped'          => $skipped,
            'skipped_inactive' => $skippedInactive,
            'skipped_stock'    => $skippedStock,
            'skipped_price'    => $skippedPrice,
            'skipped_hardware' => $skippedHardware,
            'skipped_category' => $skippedCategory,
        ];
    }

    /**
     * Returns an array (the mapped product row), a string skip-reason, or null for unparseable rows.
     *
     * @return array<string,mixed>|string|null
     */
    private function mapApRowToProduct(array $parts, string $sku, float $minPrice, float $maxPrice, int $minQty): array|string|null
    {
        $dbProductId = (int) $sku;
        if ($dbProductId <= 0) {
            return null;
        }

        $status      = trim((string) ($parts[5] ?? ''));
        $isActive    = strtoupper($status) === 'A';

        // Must be active (status = "A")
        if (!$isActive) {
            return 'inactive';
        }

        $qty = is_numeric(trim((string) ($parts[10] ?? ''))) ? (int) $parts[10] : 0;

        // Must have at least $minQty units available
        if ($qty < $minQty) {
            return 'stock';
        }

        $basePrice   = is_numeric(trim((string) ($parts[12] ?? ''))) ? (float) $parts[12] : 0;
        $retailPrice = is_numeric(trim((string) ($parts[13] ?? ''))) ? (float) $parts[13] : 0;

        // Must have a price (avoid free/internal placeholder items)
        if ($basePrice <= 0) {
            return 'price';
        }

        // Must meet the minimum price floor
        if ($minPrice > 0 && $basePrice < $minPrice) {
            return 'price';
        }

        // Must be at or under the price cap
        if ($maxPrice > 0 && $basePrice > $maxPrice) {
            return 'price';
        }

        $mpn         = trim((string) ($parts[2] ?? ''));
        $description = trim((string) ($parts[6] ?? ''));
        $manufacturer = trim((string) ($parts[7] ?? ''));
        $upc         = trim((string) ($parts[33] ?? ''));
        $categoryCode = trim((string) ($parts[34] ?? ''));
        $categoryName = trim((string) ($parts[35] ?? ''));
        $familyCode  = trim((string) ($parts[36] ?? ''));

        $name = $description !== '' ? $description : $mpn;

        // Must classify as hardware (no software, licenses, warranties, services)
        $isHardware = \App\Http\Controllers\ProductController::isHardwareProduct($name, $description);
        if (!$isHardware) {
            return 'hardware';
        }

        $curatedCategoryName = CatalogTaxonomy::inferCategoryName(
            $categoryName,
            $name,
            $description,
            $categoryCode
        );
        $curatedSegment = CatalogTaxonomy::segmentCodeForCategory($curatedCategoryName);

        // Must resolve to one of the 14 curated B2B categories
        if ($curatedSegment === '' || $curatedCategoryName === 'Uncategorized') {
            return 'category';
        }

        static $categoryIdByName = null;
        if ($categoryIdByName === null) {
            $categoryIdByName = Category::query()
                ->whereNull('parent_id')
                ->pluck('id', 'name')
                ->all();
        }
        $categoryId = $categoryIdByName[$curatedCategoryName] ?? null;

        $imageUrl  = $this->extractImageUrlFromParts($parts);
        $images    = $imageUrl !== '' ? [['imageUrl' => $imageUrl, 'source' => 'flat-file']] : [];
        $timestamp = now();

        return [
            'tdsynnex_product_id' => $dbProductId,
            'tdsynnex_sku_no'     => $dbProductId,
            'vendor_id'           => 'TD SYNNEX',
            'product_name'        => $name,
            'mfg_part_no'         => $mpn,
            'description'         => $description,
            'base_price'          => $basePrice,
            'retail_price'        => $retailPrice,
            'billing_model'       => '',
            'billing_frequency'   => '',
            'is_available'        => true,   // guaranteed: active + qty >= minQty
            'is_discontinued'     => false,
            'is_hardware'         => 1,
            'category_id'         => $categoryId,
            'category_segment'    => $curatedSegment,
            'manufacturer'        => $manufacturer !== '' ? $manufacturer : null,
            'specifications'      => json_encode([
                'sku'                => $sku,
                'status'             => $status,
                'categoryCode'       => $categoryCode !== '' ? $categoryCode : '-',
                'sourceCategoryName' => $categoryName,
                'curatedCategoryName'=> $curatedCategoryName,
                'availableQuantity'  => $qty,
                'upc'                => $upc,
                'manufacturer'       => $manufacturer,
                'categoryName'       => $curatedCategoryName,
                'familyCode'         => $familyCode,
            ], JSON_UNESCAPED_UNICODE),
            'images'          => json_encode($images, JSON_UNESCAPED_UNICODE),
            'last_synced_at'  => $timestamp,
            'updated_at'      => $timestamp,
            'created_at'      => $timestamp,
        ];
    }

    private function writeBatch(array $rows, bool $insertOnly): int
    {
        if ($insertOnly) {
            // insertOrIgnore silently skips rows whose tdsynnex_product_id already exists,
            // preserving the curated is_available value set by CurateCatalogCommand.
            return Product::insertOrIgnore($rows);
        }

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
                'category_id',
                'category_segment',
                'manufacturer',
                'specifications',
                'images',
                'last_synced_at',
                'updated_at',
            ]
        );

        return count($rows);
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
