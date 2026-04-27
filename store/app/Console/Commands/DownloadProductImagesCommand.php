<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DownloadProductImagesCommand extends Command
{
    protected $signature = 'products:download-images
                            {--chunk=100        : Products per DB batch}
                            {--limit=0          : Max products to process (0 = all)}
                            {--force            : Re-download even when a local copy already exists}
                            {--include-bing     : Also attempt images whose source is bing-images}
                            {--source=          : Only process a specific image source (icecat, flat-file, scraped, etc.)}
                            {--no-progress      : Output one log line per product instead of a progress bar (useful for non-TTY/web capture)}';

    protected $description = 'Download external product images to public/images/products/ and update DB with local paths';

    public function handle(): int
    {
        $chunk = max(1, (int) $this->option('chunk'));
        $limit = max(0, (int) $this->option('limit'));
        $force = (bool) $this->option('force');
        $includeBing = (bool) $this->option('include-bing');
        $sourceFilter = strtolower(trim((string) $this->option('source')));
        $timeout = max(5, (int) config('tdsynnex.local_images.download_timeout', 15));
        $destDir = public_path(config('tdsynnex.local_images.dest_dir', 'images/products'));
        $urlPfx = rtrim((string) config('tdsynnex.local_images.url_prefix', '/images/products'), '/');

        if (!is_dir($destDir)) {
            mkdir($destDir, 0775, true);
        }

        $baseQuery = Product::query()
            ->where('vendor_id', 'TD SYNNEX')
            ->whereNotNull('images')
            ->whereRaw("JSON_LENGTH(images) > 0");

        if (!$force) {
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
                $this->info('All product images are already local - nothing to download.');
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
            $this->info('All product images are already local - nothing to download.');
            return self::SUCCESS;
        }

        $this->info("Downloading images for {$total} products into {$destDir}");
        if (!$includeBing) {
            $this->line('Skipping source=bing-images by default. Use --include-bing to try those unstable URLs.');
        }
        if ($sourceFilter !== '') {
            $this->line("Source filter active: {$sourceFilter}");
        }

        $noProgress = (bool) $this->option('no-progress');
        $bar = null;
        if (!$noProgress) {
            $bar = $this->output->createProgressBar($total);
            $bar->setFormat(' %current%/%max% [%bar%] %percent:3s%% - %message%');
            $bar->setMessage('starting...');
            $bar->start();
        }

        $downloaded = 0;
        $alreadyLocal = 0;
        $failed = 0;
        $skipped = 0;
        $processed = 0;
        $ua = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 ArmelyStore/1.0';

        $query->chunkById($chunk, function ($products) use (
            $destDir,
            $urlPfx,
            $force,
            $includeBing,
            $sourceFilter,
            $timeout,
            $ua,
            $noProgress,
            &$downloaded,
            &$alreadyLocal,
            &$failed,
            &$skipped,
            &$processed,
            $bar,
            $total
        ) {
            foreach ($products as $product) {
                $processed++;
                $images = is_array($product->images) ? $product->images : [];
                if (empty($images)) {
                    if ($bar) {
                        $bar->advance();
                    }
                    continue;
                }

                $spec = is_array($product->specifications) ? $product->specifications : [];
                $mpn = trim((string) ($product->mfg_part_no ?? ''));
                $sku = trim((string) ($spec['sku'] ?? $product->tdsynnex_sku_no ?? $product->tdsynnex_product_id ?? $product->id));
                $nameBase = $sku !== '' ? $sku : ($mpn !== '' ? $mpn : (string) $product->id);
                $safeName = strtolower((string) preg_replace('/[^a-zA-Z0-9.\-_]/', '-', $nameBase));
                $safeName = trim($safeName, '-');

                $newImages = [];
                $changed = false;

                foreach ($images as $idx => $img) {
                    if (!is_array($img)) {
                        $newImages[] = $img;
                        continue;
                    }

                    $url = trim((string) ($img['imageUrl'] ?? ''));
                    $source = strtolower(trim((string) ($img['source'] ?? 'unknown')));

                    if ($url === '') {
                        $newImages[] = $img;
                        continue;
                    }

                    if ($sourceFilter !== '' && $source !== $sourceFilter) {
                        $newImages[] = $img;
                        $skipped++;
                        continue;
                    }

                    if (!$includeBing && $source === 'bing-images') {
                        $newImages[] = $img;
                        $skipped++;
                        if ($noProgress) {
                            $this->line("[{$processed}/{$total}] - {$safeName} skipped (source={$source})");
                        }
                        continue;
                    }

                    if (str_starts_with($url, '/images/')) {
                        $newImages[] = $img;
                        $alreadyLocal++;
                        continue;
                    }

                    $urlPath = parse_url($url, PHP_URL_PATH) ?? '';
                    $ext = strtolower(pathinfo($urlPath, PATHINFO_EXTENSION));
                    if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true)) {
                        $ext = 'jpg';
                    }

                    $filename = $idx === 0 ? "{$safeName}.{$ext}" : "{$safeName}-{$idx}.{$ext}";
                    $localPath = $destDir . DIRECTORY_SEPARATOR . $filename;
                    $localUrl = $urlPfx . '/' . $filename;

                    if (!$force && file_exists($localPath) && filesize($localPath) > 500) {
                        $newImages[] = array_merge($img, ['imageUrl' => $localUrl]);
                        $changed = true;
                        $alreadyLocal++;
                        continue;
                    }

                    try {
                        $response = $this->makeImageRequest($ua, $timeout, $url)->get($url);

                        if ($response->successful() && strlen($response->body()) > 500) {
                            file_put_contents($localPath, $response->body());
                            $newImages[] = array_merge($img, ['imageUrl' => $localUrl]);
                            $changed = true;
                            $downloaded++;
                            if ($noProgress) {
                                $this->line("[{$processed}/{$total}] v  {$filename} (source={$source})");
                            }
                        } else {
                            $newImages[] = $img;
                            $failed++;
                            if ($noProgress) {
                                $this->line("[{$processed}/{$total}] x  {$filename} (source={$source}, HTTP {$response->status()})");
                            }
                            Log::debug('products:download-images: bad response', [
                                'product_id' => $product->id,
                                'url' => $url,
                                'status' => $response->status(),
                                'source' => $source,
                            ]);
                        }
                    } catch (\Throwable $e) {
                        $newImages[] = $img;
                        $failed++;
                        if ($noProgress) {
                            $this->line("[{$processed}/{$total}] x  {$filename} (source={$source}, {$e->getMessage()})");
                        }
                        Log::debug('products:download-images: exception', [
                            'product_id' => $product->id,
                            'url' => $url,
                            'error' => $e->getMessage(),
                            'source' => $source,
                        ]);
                    }
                }

                if ($changed) {
                    $product->images = $newImages;
                    $product->save();
                }

                if ($bar) {
                    $bar->setMessage("v {$downloaded}  - {$skipped}  x {$failed}");
                    $bar->advance();
                }
            }
        });

        if ($bar) {
            $bar->setMessage('done');
            $bar->finish();
            $this->newLine(2);
        }

        $this->info("Finished - downloaded: {$downloaded}  already_local: {$alreadyLocal}  skipped: {$skipped}  failed: {$failed}");

        if ($failed > 0) {
            $this->warn("{$failed} images could not be downloaded. External URLs were kept in place.");
        }

        return self::SUCCESS;
    }

    private function makeImageRequest(string $userAgent, int $timeout, string $url): PendingRequest
    {
        $host = (string) parse_url($url, PHP_URL_HOST);
        $referer = $host !== '' ? 'https://' . $host . '/' : 'https://armely.com/';

        return Http::withHeaders([
            'User-Agent' => $userAgent,
            'Accept' => 'image/avif,image/webp,image/apng,image/svg+xml,image/*,*/*;q=0.8',
            'Accept-Language' => 'en-US,en;q=0.9',
            'Referer' => $referer,
            'Cache-Control' => 'no-cache',
            'Pragma' => 'no-cache',
        ])->retry(2, 500)->timeout($timeout);
    }
}
