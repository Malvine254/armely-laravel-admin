<?php

namespace App\Console\Commands;

use App\Jobs\EnrichPriceAvailabilityProductImageJob;
use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class RebuildStorefrontAssortmentCommand extends Command
{
    protected $signature = 'storefront:rebuild-assortment {--dry-run : Score products without writing changes}';
    protected $description = 'Build the balanced, purchase-informed 3,000-product storefront assortment';

    public function handle(): int
    {
        $quotas = (array) config('storefront.category_quotas', []);
        $limit = max(1, (int) config('storefront.assortment_size', 3000));
        $since = now()->subDays(90);

        $orderUnits = DB::table('order_line_items')
            ->join('orders', 'orders.id', '=', 'order_line_items.order_id')
            ->whereNotNull('order_line_items.product_id')
            ->where('orders.created_at', '>=', $since)
            ->where(function ($query) {
                $query->whereNull('orders.status')->orWhereNotIn('orders.status', ['cancelled', 'refunded']);
            })
            ->groupBy('order_line_items.product_id')
            ->selectRaw('order_line_items.product_id, SUM(order_line_items.quantity) as units, COUNT(DISTINCT orders.user_id) as buyers')
            ->get()->keyBy('product_id');

        $quoteUnits = DB::table('quote_line_items')
            ->join('quotes', 'quotes.id', '=', 'quote_line_items.quote_id')
            ->whereNotNull('quote_line_items.product_id')
            ->where('quotes.created_at', '>=', $since)
            ->groupBy('quote_line_items.product_id')
            ->selectRaw('quote_line_items.product_id, SUM(quote_line_items.quantity) as units')
            ->pluck('units', 'product_id');

        $candidates = Product::query()
            ->where('vendor_id', 'TD SYNNEX')
            ->where('is_hardware', true)
            ->where(function ($query) use ($quotas) {
                $query->where('is_storefront_pinned', true)
                    ->orWhereIn('category_segment', array_keys($quotas));
            })
            ->where('is_available', true)
            ->where(function ($q) { $q->where('is_discontinued', false)->orWhereNull('is_discontinued'); })
            ->whereRaw('COALESCE(NULLIF(retail_price, 0), NULLIF(base_price, 0), 0) > 0')
            ->whereRaw("COALESCE(quantity, CAST(JSON_UNQUOTE(JSON_EXTRACT(specifications, '$.availableQuantity')) AS UNSIGNED), 0) > 0")
            ->get();

        $preferredBrands = array_map('strtoupper', (array) config('storefront.preferred_brands', []));
        $scored = $candidates->map(function (Product $product) use ($orderUnits, $quoteUnits, $preferredBrands) {
            $orders = $orderUnits->get($product->id);
            $purchasedUnits = (int) ($orders->units ?? 0);
            $buyers = (int) ($orders->buyers ?? 0);
            $quotedUnits = (int) ($quoteUnits[$product->id] ?? 0);
            $images = is_array($product->images) ? $product->images : [];
            $hasImage = count($images) > 0;
            $manufacturer = strtoupper(trim((string) ($product->manufacturer ?: data_get($product->specifications, 'manufacturer', ''))));
            $preferredBrand = collect($preferredBrands)->contains(fn ($brand) => str_contains($manufacturer, $brand));
            $name = strtolower((string) $product->product_name);
            $accessory = preg_match('/\b(cable|cord|adapter|mount|bracket|case|sleeve|bag|battery|charger|toner|ink|warranty|license|renewal|support)\b/', $name) === 1;
            $recent = $product->created_at?->gte(now()->subDays(120));
            $descriptionComplete = mb_strlen(trim((string) $product->description)) >= 80;

            $product->storefront_score =
                min($purchasedUnits, 100) * 30
                + min($buyers, 30) * 20
                + min($quotedUnits, 100) * 8
                + ($hasImage ? 30 : 0)
                + ($descriptionComplete ? 8 : 0)
                + ($preferredBrand ? 20 : 0)
                + ($recent ? 10 : 0)
                - ($accessory ? 35 : 0);

            return $product;
        });

        $selected = $scored
            ->where('is_storefront_pinned', true)
            ->sortBy(fn (Product $product) => [$product->storefront_pinned_at?->timestamp ?? PHP_INT_MAX, $product->id])
            ->take($limit)
            ->values();
        foreach ($quotas as $segment => $quota) {
            $remainingQuota = max(0, (int) $quota - $selected->where('category_segment', (string) $segment)->count());
            $selected = $selected->concat(
                $scored->where('category_segment', (string) $segment)
                    ->whereNotIn('id', $selected->pluck('id'))
                    ->sortByDesc(fn (Product $p) => [(float) $p->storefront_score, (int) $p->quantity, -$p->id])
                    ->take($remainingQuota)
            );
        }

        // The configured size is a ceiling. We intentionally leave unused slots
        // when a category lacks enough quality products instead of filling them
        // with hundreds of peripherals, cables, or service-like feed records.
        $selected = $selected->take($limit)->values();
        if ($this->option('dry-run')) {
            $this->table(['Segment', 'Selected'], $selected->groupBy('category_segment')->map(fn ($rows, $segment) => [$segment, $rows->count()])->values());
            return self::SUCCESS;
        }

        DB::transaction(function () use ($selected) {
            Product::query()->where('is_storefront_curated', true)->update([
                'is_storefront_curated' => false, 'storefront_rank' => null,
            ]);
            foreach ($selected as $index => $product) {
                Product::query()->whereKey($product->id)->update([
                    'is_storefront_curated' => true,
                    'storefront_score' => $product->storefront_score,
                    'storefront_rank' => $index + 1,
                    'storefront_curated_at' => now(),
                ]);
            }
        });

        Cache::flush();
        $imageBatch = max(0, (int) config('storefront.image_enrichment_batch', 50));
        $queuedImages = 0;
        if ($imageBatch > 0) {
            Product::query()
                ->where('is_storefront_curated', true)
                ->where(function ($query) {
                    $query->whereNull('images')->orWhere('images', '')->orWhere('images', '[]')->orWhere('images', 'null');
                })
                ->where(function ($query) {
                    $query->whereNull('image_enrichment_attempted_at')
                        ->orWhere('image_enrichment_attempted_at', '<=', now()->subDays(30));
                })
                ->orderBy('storefront_rank')
                ->limit($imageBatch)
                ->pluck('id')
                ->each(function ($productId) use (&$queuedImages) {
                    EnrichPriceAvailabilityProductImageJob::dispatch((int) $productId);
                    $queuedImages++;
                });
        }

        $this->info("Storefront assortment rebuilt with {$selected->count()} products; {$queuedImages} image lookups queued.");
        return self::SUCCESS;
    }
}
