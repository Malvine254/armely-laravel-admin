<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ExportProductsToCsvCommand extends Command
{
    protected $signature = 'products:export-db
        {--path= : Output directory for CSV files}
        {--chunk=1000 : Number of rows to process per chunk}';

    protected $description = 'Export all products from the database to Excel-friendly CSV files, including vendor summary and detected datasheet URLs';

    public function handle(): int
    {
        $outputDir = $this->resolveOutputDirectory();
        $chunkSize = max(100, (int) $this->option('chunk'));
        $timestamp = now()->format('Ymd_His');

        File::ensureDirectoryExists($outputDir);

        $productsPath = $outputDir . DIRECTORY_SEPARATOR . "products_export_{$timestamp}.csv";
        $vendorsPath = $outputDir . DIRECTORY_SEPARATOR . "products_vendor_summary_{$timestamp}.csv";

        $productsHandle = fopen($productsPath, 'wb');
        if ($productsHandle === false) {
            $this->error('Unable to open product export file for writing: ' . $productsPath);
            return self::FAILURE;
        }

        fwrite($productsHandle, "\xEF\xBB\xBF");
        fputcsv($productsHandle, [
            'id',
            'tdsynnex_product_id',
            'tdsynnex_sku_no',
            'vendor_id',
            'manufacturer',
            'product_name',
            'mfg_part_no',
            'description',
            'category_id',
            'category_code',
            'category_name',
            'family_code',
            'base_price',
            'retail_price',
            'billing_model',
            'billing_frequency',
            'billing_term',
            'is_available',
            'is_discontinued',
            'last_synced_at',
            'primary_image_url',
            'has_image',
            'datasheet_url',
            'has_open_datasheet',
            'datasheet_source',
            'specifications_json',
        ]);

        $vendorSummary = [];
        $rowsWritten = 0;

        Product::query()
            ->orderBy('id')
            ->chunkById($chunkSize, function ($products) use ($productsHandle, &$vendorSummary, &$rowsWritten) {
                foreach ($products as $product) {
                    $spec = is_array($product->specifications) ? $product->specifications : [];
                    $images = is_array($product->images) ? $product->images : [];

                    $manufacturer = trim((string) ($spec['manufacturer'] ?? ''));
                    $vendorName = $manufacturer !== '' ? $manufacturer : trim((string) ($product->vendor_id ?? ''));
                    if ($vendorName === '') {
                        $vendorName = 'UNKNOWN';
                    }

                    $primaryImageUrl = $this->extractPrimaryImageUrl($images, $spec);
                    $datasheetMeta = $this->detectDatasheetUrl($spec);

                    fputcsv($productsHandle, [
                        $product->id,
                        $product->tdsynnex_product_id,
                        $product->tdsynnex_sku_no,
                        $product->vendor_id,
                        $manufacturer,
                        $product->product_name,
                        $product->mfg_part_no,
                        $product->description,
                        $product->category_id,
                        (string) ($spec['categoryCode'] ?? ''),
                        (string) ($spec['categoryName'] ?? ''),
                        (string) ($spec['familyCode'] ?? ''),
                        $product->base_price,
                        $product->retail_price,
                        $product->billing_model,
                        $product->billing_frequency,
                        $product->billing_term,
                        $product->is_available ? '1' : '0',
                        $product->is_discontinued ? '1' : '0',
                        optional($product->last_synced_at)->toDateTimeString(),
                        $primaryImageUrl,
                        $primaryImageUrl !== '' ? 'yes' : 'no',
                        $datasheetMeta['url'],
                        $datasheetMeta['url'] !== '' ? 'yes' : 'no',
                        $datasheetMeta['source'],
                        json_encode($spec, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                    ]);

                    if (!isset($vendorSummary[$vendorName])) {
                        $vendorSummary[$vendorName] = [
                            'vendor_name' => $vendorName,
                            'product_count' => 0,
                            'with_datasheet_count' => 0,
                            'without_datasheet_count' => 0,
                        ];
                    }

                    $vendorSummary[$vendorName]['product_count']++;
                    if ($datasheetMeta['url'] !== '') {
                        $vendorSummary[$vendorName]['with_datasheet_count']++;
                    } else {
                        $vendorSummary[$vendorName]['without_datasheet_count']++;
                    }

                    $rowsWritten++;
                }
            });

        fclose($productsHandle);

        uasort($vendorSummary, function (array $left, array $right) {
            if ($right['product_count'] !== $left['product_count']) {
                return $right['product_count'] <=> $left['product_count'];
            }

            return strcasecmp($left['vendor_name'], $right['vendor_name']);
        });

        $vendorsHandle = fopen($vendorsPath, 'wb');
        if ($vendorsHandle === false) {
            $this->error('Unable to open vendor summary file for writing: ' . $vendorsPath);
            return self::FAILURE;
        }

        fwrite($vendorsHandle, "\xEF\xBB\xBF");
        fputcsv($vendorsHandle, [
            'vendor_name',
            'product_count',
            'with_datasheet_count',
            'without_datasheet_count',
        ]);

        foreach ($vendorSummary as $row) {
            fputcsv($vendorsHandle, [
                $row['vendor_name'],
                $row['product_count'],
                $row['with_datasheet_count'],
                $row['without_datasheet_count'],
            ]);
        }

        fclose($vendorsHandle);

        $this->info("Export complete. Rows written: {$rowsWritten}");
        $this->line('Products CSV: ' . $productsPath);
        $this->line('Vendor summary CSV: ' . $vendorsPath);
        $this->warn('Open-datasheet detection is best-effort. This database does not appear to have a dedicated datasheet column, so the command scans product metadata for datasheet-like URLs.');

        return self::SUCCESS;
    }

    private function resolveOutputDirectory(): string
    {
        $optionPath = trim((string) $this->option('path'));
        if ($optionPath !== '') {
            return $optionPath;
        }

        return storage_path('app/exports');
    }

    private function extractPrimaryImageUrl(array $images, array $spec): string
    {
        foreach ($images as $image) {
            if (!is_array($image)) {
                continue;
            }

            $url = trim((string) ($image['imageUrl'] ?? $image['url'] ?? ''));
            if ($url !== '') {
                return $url;
            }
        }

        $specImage = trim((string) ($spec['image_url'] ?? $spec['imageUrl'] ?? ''));
        return $specImage;
    }

    private function detectDatasheetUrl(array $spec): array
    {
        $candidateKeys = [
            'datasheet',
            'datasheet_url',
            'datasheetUrl',
            'open_datasheet',
            'open_datasheet_url',
            'openDatasheetUrl',
            'specsheet',
            'specsheet_url',
            'specSheetUrl',
            'pdf_url',
            'pdfUrl',
            'brochure_url',
            'brochureUrl',
            'manual_url',
            'manualUrl',
            'document_url',
            'documentUrl',
        ];

        $stack = [['node' => $spec, 'path' => 'specifications']];

        while (!empty($stack)) {
            $current = array_pop($stack);
            $node = $current['node'];
            $path = $current['path'];

            if (!is_array($node)) {
                continue;
            }

            foreach ($node as $key => $value) {
                $currentPath = $path . '.' . $key;

                if (is_array($value)) {
                    $stack[] = ['node' => $value, 'path' => $currentPath];
                    continue;
                }

                $normalizedKey = (string) $key;
                $candidateUrl = trim((string) $value);

                if ($candidateUrl === '') {
                    continue;
                }

                $looksLikeUrl = filter_var($candidateUrl, FILTER_VALIDATE_URL) !== false;
                $looksLikePdf = str_ends_with(strtolower(parse_url($candidateUrl, PHP_URL_PATH) ?? ''), '.pdf');
                $isCandidateKey = in_array($normalizedKey, $candidateKeys, true);

                if (($isCandidateKey || $looksLikePdf) && $looksLikeUrl) {
                    return [
                        'url' => $candidateUrl,
                        'source' => $currentPath,
                    ];
                }
            }
        }

        return [
            'url' => '',
            'source' => '',
        ];
    }
}