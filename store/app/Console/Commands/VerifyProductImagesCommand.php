<?php

namespace App\Console\Commands;

use App\Models\AppSetting;
use App\Models\Product;
use App\Services\RemoteProductImageFetcher;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

/**
 * Checks supplier image links in the background so broken photos are found
 * before a customer sees an empty card. Broken links are remembered (the
 * storefront then treats the product as having no photo and hides it);
 * working links are remembered too, so they are not downloaded every night.
 *
 * Runs in slices: each run continues from where the previous one stopped, so
 * the whole in-stock catalog is covered over a few nights without long runs.
 */
class VerifyProductImagesCommand extends Command
{
    protected $signature = 'products:verify-images
        {--limit=1500 : Maximum products to check in this run}
        {--seconds=600 : Stop after this many seconds}
        {--restart : Start again from the first product}';

    protected $description = 'Check supplier product image links and hide products whose photos are broken';

    private const CURSOR_KEY = 'images.verify.cursor';
    private const HEALTHY_DAYS = 7;
    private const BROKEN_DAYS = 8;

    public function handle(RemoteProductImageFetcher $fetcher): int
    {
        $limit = max(1, (int) $this->option('limit'));
        $deadline = microtime(true) + max(30, (int) $this->option('seconds'));
        $cursor = $this->option('restart') ? 0 : (int) AppSetting::getValue(self::CURSOR_KEY, 0);

        $stats = ['products' => 0, 'links_checked' => 0, 'links_broken' => 0, 'links_cached' => 0, 'products_without_working_photo' => 0];
        $lastId = $cursor;
        $finishedCatalog = false;

        $products = Product::query()
            ->select(['id', 'tdsynnex_product_id', 'images'])
            ->where('id', '>', $cursor)
            ->where('is_available', true)
            ->where(fn ($query) => $query->where('is_discontinued', false)->orWhereNull('is_discontinued'))
            ->where('quantity', '>', 0)
            ->whereRaw("LOWER(CAST(COALESCE(images, '') AS CHAR)) LIKE '%http%'")
            ->orderBy('id')
            ->limit($limit)
            ->get();

        if ($products->count() < $limit) {
            $finishedCatalog = true;
        }

        foreach ($products as $product) {
            if (microtime(true) >= $deadline) {
                $finishedCatalog = false;
                break;
            }

            $lastId = (int) $product->id;
            $stats['products']++;

            if ($this->hasLocalPhoto($product)) {
                continue;
            }

            $working = false;
            foreach (array_slice($this->externalUrls($product), 0, 3) as $url) {
                if (Cache::has($this->healthyKey($url))) {
                    $stats['links_cached']++;
                    $working = true;
                    break;
                }

                $stats['links_checked']++;
                if ($fetcher->fetch($url, now()->addDays(self::BROKEN_DAYS)) !== null) {
                    Cache::put($this->healthyKey($url), true, now()->addDays(self::HEALTHY_DAYS));
                    $working = true;
                    break;
                }

                $stats['links_broken']++;
            }

            if (!$working) {
                $stats['products_without_working_photo']++;
            }
        }

        AppSetting::setValue(self::CURSOR_KEY, $finishedCatalog ? 0 : $lastId);
        AppSetting::setValue('images.verify.last_run', array_merge($stats, [
            'finished_catalog_pass' => $finishedCatalog,
            'resumes_after_product_id' => $finishedCatalog ? 0 : $lastId,
            'finished_at' => now()->toISOString(),
        ]));

        $this->table(['Metric', 'Count'], collect($stats)->map(fn ($value, $key) => [str_replace('_', ' ', $key), $value])->values()->all());
        $this->info($finishedCatalog
            ? 'Reached the end of the in-stock catalog; the next run starts from the beginning.'
            : "Stopped after product #{$lastId}; the next run continues from there.");

        return self::SUCCESS;
    }

    private function healthyKey(string $url): string
    {
        return 'img_verified_ok:' . md5($url);
    }

    private function hasLocalPhoto(Product $product): bool
    {
        $id = (string) $product->tdsynnex_product_id;
        if (!ctype_digit($id)) {
            return false;
        }

        foreach (['jpg', 'jpeg', 'png', 'webp', 'gif', 'avif'] as $extension) {
            if (is_file(public_path("images/products/{$id}.{$extension}"))) {
                return true;
            }
        }

        return false;
    }

    /** @return list<string> */
    private function externalUrls(Product $product): array
    {
        $urls = [];
        foreach ((array) $product->images as $image) {
            $url = is_array($image)
                ? trim((string) ($image['imageUrl'] ?? $image['image_url'] ?? $image['url'] ?? ''))
                : trim((string) $image);
            if (str_starts_with($url, '//')) {
                $url = 'https:' . $url;
            }
            if (preg_match('#^https?://#i', $url) && filter_var($url, FILTER_VALIDATE_URL)) {
                $urls[] = $url;
            }
        }

        return array_values(array_unique($urls));
    }
}
