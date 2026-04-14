<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;

class SyncLocalProductImagesBySkuCommand extends Command
{
    protected $signature = 'products:sync-local-images-by-sku
                            {--vendor=TD SYNNEX : Vendor scope in products.vendor_id}
                            {--dir=images/products : Public image directory relative to store/public}
                            {--limit=0 : Max number of product rows to update (0 = no limit)}
                            {--dry-run : Preview updates without writing to the database}';

    protected $description = 'Map local images named by SKU (e.g. public/images/products/12345.jpg) back into products.images';

    public function handle(): int
    {
        $vendor = trim((string) $this->option('vendor'));
        $dir = trim((string) $this->option('dir'));
        $limit = max(0, (int) $this->option('limit'));
        $dryRun = (bool) $this->option('dry-run');

        if ($vendor === '') {
            $this->error('Vendor cannot be empty.');
            return self::FAILURE;
        }

        $relativeDir = trim(str_replace('\\', '/', $dir), '/');
        $absoluteDir = public_path($relativeDir);

        if (!is_dir($absoluteDir)) {
            $this->error("Image directory not found: {$absoluteDir}");
            return self::FAILURE;
        }

        $skuToFile = $this->buildSkuToFileMap($absoluteDir);
        if (empty($skuToFile)) {
            $this->warn('No SKU-named image files found in directory.');
            return self::SUCCESS;
        }

        $this->info('Found ' . count($skuToFile) . ' SKU image files.');

        $updated = 0;
        $scanned = 0;
        $matchedProducts = 0;

        $skuKeys = array_keys($skuToFile);
        foreach (array_chunk($skuKeys, 500) as $skuChunk) {
            $intChunk = [];
            foreach ($skuChunk as $sku) {
                if (ctype_digit($sku)) {
                    $intChunk[] = (int) $sku;
                }
            }

            $query = Product::query()
                ->where('vendor_id', $vendor)
                ->where(function ($q) use ($skuChunk, $intChunk) {
                    if (!empty($intChunk)) {
                        $q->orWhereIn('tdsynnex_sku_no', $intChunk)
                          ->orWhereIn('tdsynnex_product_id', $intChunk);
                    }

                    $q->orWhereIn('specifications->sku', $skuChunk);
                });

            $products = $query->get();
            foreach ($products as $product) {
                $scanned++;

                $skuCandidates = $this->extractSkuCandidates($product);
                $matchedSku = null;
                foreach ($skuCandidates as $candidate) {
                    if (isset($skuToFile[$candidate])) {
                        $matchedSku = $candidate;
                        break;
                    }
                }

                if ($matchedSku === null) {
                    continue;
                }

                $matchedProducts++;
                $localUrl = '/' . $relativeDir . '/' . $skuToFile[$matchedSku];
                $newImages = $this->injectPrimaryLocalImage($product->images, $localUrl);

                if ($newImages === $product->images) {
                    continue;
                }

                if (!$dryRun) {
                    Product::query()
                        ->whereKey((int) ($product->id ?? 0))
                        ->update(['images' => $newImages]);
                }

                $updated++;
                if ($limit > 0 && $updated >= $limit) {
                    break 2;
                }
            }
        }

        $mode = $dryRun ? 'DRY RUN' : 'APPLIED';
        $this->info("{$mode}: scanned={$scanned} matched_products={$matchedProducts} updated={$updated}");

        return self::SUCCESS;
    }

    private function buildSkuToFileMap(string $absoluteDir): array
    {
        $map = [];
        $priority = [
            'jpg' => 1,
            'jpeg' => 2,
            'png' => 3,
            'webp' => 4,
            'gif' => 5,
            'avif' => 6,
        ];

        foreach (scandir($absoluteDir) ?: [] as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            $fullPath = $absoluteDir . DIRECTORY_SEPARATOR . $file;
            if (!is_file($fullPath)) {
                continue;
            }

            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            if (!isset($priority[$ext])) {
                continue;
            }

            $name = trim((string) pathinfo($file, PATHINFO_FILENAME));
            if ($name === '' || !ctype_digit($name)) {
                continue;
            }

            if (!isset($map[$name])) {
                $map[$name] = $file;
                continue;
            }

            $currentExt = strtolower(pathinfo($map[$name], PATHINFO_EXTENSION));
            if (($priority[$ext] ?? 999) < ($priority[$currentExt] ?? 999)) {
                $map[$name] = $file;
            }
        }

        return $map;
    }

    private function extractSkuCandidates($product): array
    {
        $spec = is_array($product->specifications) ? $product->specifications : [];
        $candidates = [
            trim((string) ($product->tdsynnex_sku_no ?? '')),
            trim((string) ($product->tdsynnex_product_id ?? '')),
            trim((string) ($spec['sku'] ?? '')),
        ];

        $out = [];
        foreach ($candidates as $candidate) {
            if ($candidate !== '') {
                $out[$candidate] = true;
            }
        }

        return array_keys($out);
    }

    private function injectPrimaryLocalImage($images, string $localUrl): array
    {
        $normalized = [];
        $seen = [];

        $localUrl = trim($localUrl);
        if ($localUrl !== '') {
            $normalized[] = ['imageUrl' => $localUrl];
            $seen[$localUrl] = true;
        }

        if (!is_array($images)) {
            return $normalized;
        }

        foreach ($images as $image) {
            $url = '';
            if (is_string($image)) {
                $url = trim($image);
            } elseif (is_array($image)) {
                $url = trim((string) ($image['imageUrl'] ?? $image['url'] ?? ''));
            }

            if ($url === '' || isset($seen[$url])) {
                continue;
            }

            if (!$this->isAcceptableImageRef($url)) {
                continue;
            }

            $seen[$url] = true;
            $normalized[] = ['imageUrl' => $url];
        }

        return $normalized;
    }

    private function isAcceptableImageRef(string $url): bool
    {
        if ($url === '') {
            return false;
        }

        if (str_starts_with($url, '/')) {
            return (bool) preg_match('/^\/images\/.+\.(?:jpg|jpeg|png|webp|gif|avif)(?:\?.*)?$/i', $url);
        }

        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }

        return (bool) preg_match('/\.(?:jpg|jpeg|png|webp|gif|avif)(?:\?.*)?$/i', $url);
    }
}
