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
                            {--priority-min-price=100 : Prioritize products whose preferred price is at least this amount}
                            {--dry-run : Preview updates without writing to the database}';

    protected $description = 'Map local images named by SKU/MPN/title back into products.images using product-ID filenames';

    public function handle(): int
    {
        $vendor = trim((string) $this->option('vendor'));
        $dir = trim((string) $this->option('dir'));
        $limit = max(0, (int) $this->option('limit'));
        $priorityMinPrice = max(0, (float) $this->option('priority-min-price'));
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

        $imageFiles = $this->buildImageFileMap($absoluteDir);
        if (empty($imageFiles)) {
            $this->warn('No local image files found in directory.');
            return self::SUCCESS;
        }

        $this->info('Found ' . count($imageFiles) . ' local image files.');

        $updated = 0;
        $scanned = 0;
        $matchedProducts = 0;
        $copied = 0;

        $skuToFile = $this->numericSkuFileMap($imageFiles);
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
            $products = $products
                ->sortByDesc(fn (Product $product) => $this->isPriorityCandidate($product, $priorityMinPrice) ? 1 : 0)
                ->values();

            foreach ($products as $product) {
                $scanned++;

                $matchCandidates = $this->extractImageMatchCandidates($product);
                $matchedKey = null;
                foreach ($matchCandidates as $candidate) {
                    if (isset($imageFiles[$candidate])) {
                        $matchedKey = $candidate;
                        break;
                    }
                }

                if ($matchedKey === null) {
                    continue;
                }

                $matchedProducts++;
                $sourceFile = $imageFiles[$matchedKey];
                $localFile = $this->productIdImageFile($product, $sourceFile);

                if ($localFile !== $sourceFile && !$dryRun) {
                    $sourcePath = $absoluteDir . DIRECTORY_SEPARATOR . $sourceFile;
                    $targetPath = $absoluteDir . DIRECTORY_SEPARATOR . $localFile;

                    if (!file_exists($targetPath) && file_exists($sourcePath)) {
                        copy($sourcePath, $targetPath);
                        $copied++;
                    }
                }

                $localUrl = '/' . $relativeDir . '/' . $localFile;
                $newImages = $this->injectPrimaryLocalImage(
                    $product->images,
                    $localUrl,
                    (string) ($product->tdsynnex_product_id ?? ''),
                    (string) ($product->tdsynnex_sku_no ?? '')
                );

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
        if (!$dryRun) {
            $this->line("Copied product-ID image files: {$copied}");
        }
        $this->line("Priority rule: price >= {$priorityMinPrice} and availableQuantity > 0 were processed first.");

        return self::SUCCESS;
    }

    private function buildImageFileMap(string $absoluteDir): array
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
            if ($name === '') {
                continue;
            }

            foreach ($this->fileMatchKeys($name) as $key) {
                if (!isset($map[$key])) {
                    $map[$key] = $file;
                    continue;
                }

                $currentExt = strtolower(pathinfo($map[$key], PATHINFO_EXTENSION));
                if (($priority[$ext] ?? 999) < ($priority[$currentExt] ?? 999)) {
                    $map[$key] = $file;
                }
            }
        }

        return $map;
    }

    private function numericSkuFileMap(array $imageFiles): array
    {
        return array_filter(
            $imageFiles,
            static fn (string $file, string $key): bool => ctype_digit($key),
            ARRAY_FILTER_USE_BOTH
        );
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

    private function extractImageMatchCandidates(Product $product): array
    {
        $spec = is_array($product->specifications) ? $product->specifications : [];
        $rawCandidates = [
            (string) ($product->tdsynnex_product_id ?? ''),
            (string) ($product->tdsynnex_sku_no ?? ''),
            (string) ($spec['sku'] ?? ''),
            (string) ($product->mfg_part_no ?? ''),
            (string) ($spec['mpn'] ?? ''),
            (string) ($spec['manufacturerPartNumber'] ?? ''),
            (string) ($product->product_name ?? ''),
            (string) ($product->name ?? ''),
        ];

        $keys = [];
        foreach ($rawCandidates as $candidate) {
            foreach ($this->fileMatchKeys($candidate) as $key) {
                $keys[$key] = true;
            }
        }

        return array_keys($keys);
    }

    private function fileMatchKeys(string $value): array
    {
        $value = strtolower(trim($value));
        if ($value === '') {
            return [];
        }

        $withoutSizeSuffix = preg_replace('/-\d+$/', '', $value) ?: $value;
        $slug = preg_replace('/[^a-z0-9]+/', '-', $withoutSizeSuffix) ?: '';
        $slug = trim($slug, '-');
        $compact = preg_replace('/[^a-z0-9]+/', '', $withoutSizeSuffix) ?: '';

        $keys = [];
        foreach ([$value, $withoutSizeSuffix, $slug, $compact] as $key) {
            $key = strtolower(trim((string) $key));
            if ($key !== '') {
                $keys[$key] = true;
            }
        }

        return array_keys($keys);
    }

    private function injectPrimaryLocalImage($images, string $localUrl, string $productId = '', string $sku = ''): array
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

            if ($this->isSkuNamedLocalProductImage($url, $productId, $sku)) {
                continue;
            }

            $seen[$url] = true;
            $normalized[] = ['imageUrl' => $url];
        }

        return $normalized;
    }

    private function isSkuNamedLocalProductImage(string $url, string $productId, string $sku): bool
    {
        $productId = trim($productId);
        $sku = trim($sku);

        if ($productId === '' || $sku === '' || $productId === $sku) {
            return false;
        }

        if (!str_starts_with($url, '/images/products/')) {
            return false;
        }

        $path = parse_url($url, PHP_URL_PATH) ?: $url;
        $stem = pathinfo(basename($path), PATHINFO_FILENAME);

        return $stem === $sku;
    }

    private function productIdImageFile(Product $product, string $sourceFile): string
    {
        $productId = trim((string) ($product->tdsynnex_product_id ?? ''));
        $extension = strtolower(pathinfo($sourceFile, PATHINFO_EXTENSION));

        if ($productId === '' || $extension === '') {
            return $sourceFile;
        }

        return $productId . '.' . $extension;
    }

    private function isPriorityCandidate(Product $product, float $minPrice): bool
    {
        $spec = is_array($product->specifications) ? $product->specifications : [];
        $availableQuantity = (int) ($spec['availableQuantity'] ?? $spec['totalQuantity'] ?? 0);
        $price = max(
            (float) ($product->retail_price ?? 0),
            (float) ($product->base_price ?? 0)
        );

        return $availableQuantity > 0 && $price >= $minPrice;
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
