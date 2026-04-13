<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DownloadProductImagesCommand extends Command
{
    protected $signature = 'products:download-images
                            {--chunk=100        : Products per DB batch}
                            {--limit=0          : Max products to process (0 = all)}
                            {--force            : Re-download even when a local copy already exists}
                            {--no-progress      : Output one log line per product instead of a progress bar (useful for non-TTY/web capture)}';

    protected $description = 'Download external product images to public/images/products/ and update DB with local paths';

    public function handle(): int
    {
        $chunk   = max(1, (int) $this->option('chunk'));
        $limit   = max(0, (int) $this->option('limit'));
        $force   = (bool) $this->option('force');
        $timeout = max(5, (int) config('tdsynnex.local_images.download_timeout', 15));
        $destDir = public_path(config('tdsynnex.local_images.dest_dir', 'images/products'));
        $urlPfx  = rtrim((string) config('tdsynnex.local_images.url_prefix', '/images/products'), '/');

        if (!is_dir($destDir)) {
            mkdir($destDir, 0775, true);
        }

        // Scope to products with at least one external image URL.
        $baseQuery = Product::query()
            ->where('vendor_id', 'TD SYNNEX')
            ->whereNotNull('images')
            ->whereRaw("JSON_LENGTH(images) > 0");

        if (!$force) {
            // Exclude products whose first image is already a local path.
            $baseQuery->whereRaw(
                "JSON_UNQUOTE(JSON_EXTRACT(images, '$[0].imageUrl')) NOT LIKE '/images/%'"
            );
        }

        $query = null;
        if ($limit > 0) {
            $productIds = (clone $baseQuery)
                ->orderBy('id')
                ->limit($limit)
                ->pluck('id')
                ->all();

            if (empty($productIds)) {
                $this->info('All product images are already local — nothing to download.');
                return self::SUCCESS;
            }

            $query = Product::query()
                ->whereIn('id', $productIds)
                ->orderBy('id');

            $total = count($productIds);
        } else {
            $query = (clone $baseQuery)->orderBy('id');
            $total = (clone $baseQuery)->count();
        }

        if ($total === 0) {
            $this->info('All product images are already local — nothing to download.');
            return self::SUCCESS;
        }

        $this->info("Downloading images for {$total} products into {$destDir}");

        $noProgress = (bool) $this->option('no-progress');
        $bar = null;
        if (!$noProgress) {
            $bar = $this->output->createProgressBar($total);
            $bar->setFormat(' %current%/%max% [%bar%] %percent:3s%% — %message%');
            $bar->setMessage('starting…');
            $bar->start();
        }

        $downloaded   = 0;
        $alreadyLocal = 0;
        $failed       = 0;
        $processed    = 0;
        $ua           = 'Mozilla/5.0 (compatible; ArmelyStore/1.0; +https://armely.com)';

        $query->chunkById($chunk, function ($products) use (
            $destDir, $urlPfx, $force, $timeout, $ua, $noProgress,
            &$downloaded, &$alreadyLocal, &$failed, &$processed, $bar, $total
        ) {
            foreach ($products as $product) {
                $processed++;
                $images = is_array($product->images) ? $product->images : [];
                if (empty($images)) {
                    if ($bar) $bar->advance();
                    continue;
                }

                $spec     = is_array($product->specifications) ? $product->specifications : [];
                $mpn      = trim((string) ($product->mfg_part_no ?? ''));
                $sku      = trim((string) ($spec['sku'] ?? $product->tdsynnex_sku_no ?? $product->tdsynnex_product_id ?? $product->id));
                // mfg_part_no is the most human-readable stable key; fall back to sku then DB id.
                $nameBase = $mpn !== '' ? $mpn : ($sku !== '' ? $sku : (string) $product->id);
                $safeName = strtolower(preg_replace('/[^a-zA-Z0-9.\-_]/', '-', $nameBase));
                $safeName = trim($safeName, '-');

                $newImages = [];
                $changed   = false;

                foreach ($images as $idx => $img) {
                    if (!is_array($img)) {
                        $newImages[] = $img;
                        continue;
                    }

                    $url = trim((string) ($img['imageUrl'] ?? ''));

                    if ($url === '') {
                        $newImages[] = $img;
                        continue;
                    }

                    // Already pointing at a local path — nothing to do.
                    if (str_starts_with($url, '/images/')) {
                        $newImages[] = $img;
                        $alreadyLocal++;
                        continue;
                    }

                    // Derive file extension from URL path.
                    $urlPath = parse_url($url, PHP_URL_PATH) ?? '';
                    $ext     = strtolower(pathinfo($urlPath, PATHINFO_EXTENSION));
                    if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true)) {
                        $ext = 'jpg';
                    }

                    $filename  = $idx === 0 ? "{$safeName}.{$ext}" : "{$safeName}-{$idx}.{$ext}";
                    $localPath = $destDir . DIRECTORY_SEPARATOR . $filename;
                    $localUrl  = $urlPfx . '/' . $filename;

                    // Reuse already-downloaded file unless --force is set.
                    if (!$force && file_exists($localPath) && filesize($localPath) > 500) {
                        $newImages[] = array_merge($img, ['imageUrl' => $localUrl]);
                        $changed     = true;
                        $alreadyLocal++;
                        continue;
                    }

                    try {
                        $response = Http::withHeaders(['User-Agent' => $ua])
                            ->timeout($timeout)
                            ->get($url);

                        if ($response->successful() && strlen($response->body()) > 500) {
                            file_put_contents($localPath, $response->body());
                            $newImages[] = array_merge($img, ['imageUrl' => $localUrl]);
                            $changed     = true;
                            $downloaded++;
                            if ($noProgress) $this->line("[{$processed}/{$total}] ↓  {$filename}");
                        } else {
                            $newImages[] = $img; // keep external URL
                            $failed++;
                            if ($noProgress) $this->line("[{$processed}/{$total}] ✗  {$filename} (HTTP {$response->status()})");
                            Log::debug('products:download-images: bad response', [
                                'product_id' => $product->id,
                                'url'        => $url,
                                'status'     => $response->status(),
                            ]);
                        }
                    } catch (\Throwable $e) {
                        $newImages[] = $img;
                        $failed++;
                        if ($noProgress) $this->line("[{$processed}/{$total}] ✗  {$filename} ({$e->getMessage()})");
                        Log::debug('products:download-images: exception', [
                            'product_id' => $product->id,
                            'url'        => $url,
                            'error'      => $e->getMessage(),
                        ]);
                    }
                }

                if ($changed) {
                    $product->images = $newImages;
                    $product->save();
                }

                if ($bar) {
                    $bar->setMessage("↓ {$downloaded}  ✗ {$failed}");
                    $bar->advance();
                }
            }
        });

        if ($bar) {
            $bar->setMessage('done');
            $bar->finish();
            $this->newLine(2);
        }

        $this->info("Finished — downloaded: {$downloaded}  already_local: {$alreadyLocal}  failed: {$failed}");

        if ($failed > 0) {
            $this->warn("{$failed} images could not be downloaded (external URLs kept). Run with -v or check laravel.log for details.");
        }

        return self::SUCCESS;
    }
}
