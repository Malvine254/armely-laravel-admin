<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

/**
 * Sync manually saved product images from a folder to the database.
 *
 * Usage:
 *   php artisan products:sync-manual-images
 *     Scans public/uploads/products/ and updates images in database
 *
 *   php artisan products:sync-manual-images --folder=custom/path
 *     Scans custom folder and updates images
 *
 *   php artisan products:sync-manual-images --dry-run
 *     Preview changes without updating database
 *
 *   php artisan products:sync-manual-images --by-id
 *     Match images by product ID (e.g., 15204339.jpg)
 *
 *   php artisan products:sync-manual-images --by-sku
 *     Match images by TD SYNNEX SKU (e.g., 135048.jpg)
 */
class SyncManualProductImagesCommand extends Command
{
    protected $signature = 'products:sync-manual-images
                            {--folder=public/uploads/products : Folder to scan for images}
                            {--by-id                          : Match images by product ID (default)}
                            {--by-sku                         : Match images by TD SYNNEX SKU}
                            {--dry-run                        : Preview changes without updating}
                            {--quiet-output                   : Suppress per-product lines}';

    protected $description = 'Sync manually saved product images from a folder to the database';

    public function handle(): int
    {
        $folderPath = $this->option('folder');
        $byId       = $this->option('by-id') || !$this->option('by-sku');
        $bySku      = $this->option('by-sku');
        $dryRun     = $this->option('dry-run');
        $quiet      = $this->option('quiet-output');

        // Normalize folder path
        if (!str_starts_with($folderPath, '/')) {
            $folderPath = base_path($folderPath);
        }

        // Verify folder exists
        if (!is_dir($folderPath)) {
            $this->error("Folder not found: {$folderPath}");
            return self::FAILURE;
        }

        $this->info("Scanning folder: {$folderPath}");
        $this->info("Match mode: " . ($byId ? 'BY PRODUCT ID' : 'BY TD SYNNEX SKU'));
        if ($dryRun) {
            $this->warn('DRY-RUN MODE — no database changes will be made');
        }
        $this->newLine();

        // Scan folder for image files
        $imageFiles = $this->scanImageFiles($folderPath);
        $total      = count($imageFiles);

        if ($total === 0) {
            $this->info('No image files found in folder.');
            return self::SUCCESS;
        }

        $this->info("Found {$total} image files to process.");
        $this->newLine();

        $updated  = 0;
        $failed   = 0;
        $skipped  = 0;

        foreach ($imageFiles as $filename => $fullPath) {
            // Extract identifier (filename without extension)
            $identifier = pathinfo($filename, PATHINFO_FILENAME);

            // Build image URL/path that will be stored
            $imagePath = 'uploads/products/' . $filename;
            $imageUrl  = url($imagePath);

            // Find product
            $product = null;

            if ($byId) {
                // Try matching by product ID
                $product = Product::where('id', $identifier)->first();
            } else {
                // Try matching by TD SYNNEX SKU
                $product = Product::where('tdsynnex_sku_no', $identifier)->first();
            }

            if (!$product) {
                if (!$quiet) {
                    $this->line("⊘ SKIP   {$filename} — no matching product");
                }
                $skipped++;
                continue;
            }

            // Check if product already has images
            $existingImages = is_array($product->images) ? $product->images : [];
            $imageCount     = count($existingImages);

            if ($imageCount > 0 && !$this->option('force')) {
                if (!$quiet) {
                    $this->line("⊘ SKIP   {$filename} — product already has {$imageCount} image(s)");
                }
                $skipped++;
                continue;
            }

            // Prepare new image entry
            $newImage = [
                'fileName'  => $filename,
                'imagePath' => $imagePath,
                'imageUrl'  => $imageUrl,
                'source'    => 'manual',
                'addedAt'   => now()->toIso8601String(),
            ];

            // Update product images
            $updatedImages = array_merge($existingImages, [$newImage]);

            if (!$dryRun) {
                try {
                    $product->update(['images' => $updatedImages]);
                    if (!$quiet) {
                        $this->line("✓ SAVED   {$filename} → " . $product->product_name);
                    }
                    $updated++;
                } catch (\Exception $e) {
                    if (!$quiet) {
                        $this->line("✗ ERROR   {$filename} — " . $e->getMessage());
                    }
                    $failed++;
                }
            } else {
                if (!$quiet) {
                    $this->line("→ WOULD UPDATE  {$filename} → " . $product->product_name);
                }
                $updated++;
            }
        }

        $this->newLine();
        $this->info("Summary:");
        $this->line("  ✓ Updated:  {$updated}");
        $this->line("  ⊘ Skipped:  {$skipped}");
        $this->line("  ✗ Failed:   {$failed}");

        if ($dryRun) {
            $this->warn('  (DRY-RUN — no changes made)');
        }

        return self::SUCCESS;
    }

    /**
     * Scan folder for image files and return array of [filename => fullPath]
     */
    private function scanImageFiles(string $folderPath): array
    {
        $images = [];
        $extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (!is_readable($folderPath)) {
            $this->error("Folder is not readable: {$folderPath}");
            return $images;
        }

        $files = scandir($folderPath);
        if ($files === false) {
            $this->error("Failed to scan folder: {$folderPath}");
            return $images;
        }

        foreach ($files as $file) {
            if (in_array($file, ['.', '..']) || is_dir("{$folderPath}/{$file}")) {
                continue;
            }

            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            if (!in_array($ext, $extensions)) {
                continue;
            }

            $images[$file] = "{$folderPath}/{$file}";
        }

        return $images;
    }
}
