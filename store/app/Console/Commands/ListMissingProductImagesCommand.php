<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;

/**
 * Generate a list of products that need images (export to CSV or display).
 *
 * Usage:
 *   php artisan products:list-missing-images
 *     Display products needing images
 *
 *   php artisan products:list-missing-images --export=products-no-images.csv
 *     Export to CSV file
 *
 *   php artisan products:list-missing-images --limit=100
 *     Show only first 100 products
 *
 *   php artisan products:list-missing-images --by-id
 *     Include product ID (for naming: 15204339.jpg)
 *
 *   php artisan products:list-missing-images --by-sku
 *     Include TD SYNNEX SKU (for naming: 135048.jpg)
 */
class ListMissingProductImagesCommand extends Command
{
    protected $signature = 'products:list-missing-images
                            {--export=            : Export to CSV file path}
                            {--by-id              : Include product ID (default)}
                            {--by-sku             : Include TD SYNNEX SKU}
                            {--limit=0            : Max products to show (0 = all)}
                            {--format=table       : Output format (table, csv, csv-only)}';

    protected $description = 'List products that need images (helper for manual image addition)';

    public function handle(): int
    {
        $limit      = max(0, (int) $this->option('limit'));
        $export     = $this->option('export');
        $byId       = $this->option('by-id') || !$this->option('by-sku');
        $bySku      = $this->option('by-sku');
        $format     = $this->option('format') ?? 'table';

        $query = Product::whereNull('images')
                    ->orWhereRaw('json_length(images) = 0')
                    ->orderBy('id');

        if ($limit > 0) {
            $query = $query->limit($limit);
        }

        $products = $query->get(['id', 'tdsynnex_product_id', 'tdsynnex_sku_no', 'product_name', 'vendor_id']);
        $total    = $products->count();

        // Prepare data for output
        $rows = [];
        foreach ($products as $product) {
            $row = [];

            if ($byId) {
                $row['Product ID'] = $product->tdsynnex_product_id;
            }
            if ($bySku) {
                $row['SKU'] = $product->tdsynnex_sku_no ?? '—';
            }

            $row['Product Name'] = mb_substr($product->product_name, 0, 80);
            $row['Vendor']       = $product->vendor_id;
            $row['File Name']    = ($byId ? $product->tdsynnex_product_id : $product->tdsynnex_sku_no) . '.jpg';

            $rows[] = $row;
        }

        // Output to table
        if ($format !== 'csv-only') {
            $this->info("Products needing images: {$total}");
            $this->newLine();

            if ($total === 0) {
                $this->info('All products have images!');
                return self::SUCCESS;
            }

            $this->table(
                array_keys($rows[0] ?? []),
                array_slice($rows, 0, min(50, count($rows)))
            );

            if ($total > 50) {
                $this->line("... and " . ($total - 50) . " more");
            }
        }

        // Export to CSV if requested
        if ($export) {
            $this->exportToCsv($export, $rows, $total);
        }

        $this->newLine();
        $this->info("💡 Naming convention: Use PRODUCT_ID.jpg for your image files");
        $this->info("   Example: 15204339.jpg");
        $this->newLine();
        $this->info("📁 Place images in: public/uploads/products/");
        $this->info("⏱️  Then run: php artisan products:sync-manual-images --by-id");

        return self::SUCCESS;
    }

    private function exportToCsv(string $filePath, array $rows, int $total): void
    {
        if (empty($rows)) {
            $this->warn("No products to export.");
            return;
        }

        $fp = fopen($filePath, 'w');
        if ($fp === false) {
            $this->error("Failed to open file: {$filePath}");
            return;
        }

        // Write header
        fputcsv($fp, array_keys($rows[0]));

        // Write rows
        foreach ($rows as $row) {
            fputcsv($fp, $row);
        }

        fclose($fp);

        $this->info("✓ Exported {$total} products to: {$filePath}");
    }
}
