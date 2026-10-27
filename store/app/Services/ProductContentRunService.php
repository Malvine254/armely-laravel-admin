<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductContentRun;
use App\Models\ProductContentRunItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Server-side AI content runs. Progress lives in the database, so a run survives page
 * refreshes and can be advanced by the admin page (several parallel requests) and by the
 * scheduler at the same time. Each product is claimed under a row lock, never twice.
 */
class ProductContentRunService
{
    public const MODES = ['description', 'images', 'repair_descriptions'];

    public function __construct(private readonly ProductContentEnrichmentService $enrichment) {}

    public function active(): ?ProductContentRun
    {
        return ProductContentRun::query()->where('status', ProductContentRun::ACTIVE)->latest('id')->first();
    }

    public function latest(): ?ProductContentRun
    {
        return ProductContentRun::query()->latest('id')->first();
    }

    public function start(string $mode, int $minLength, ?int $userId): ProductContentRun
    {
        if (! in_array($mode, self::MODES, true)) {
            throw new \InvalidArgumentException('Unsupported product content mode.');
        }
        $minLength = $mode === 'description' ? max(0, min(1000, $minLength)) : 0;

        return DB::transaction(function () use ($mode, $minLength, $userId): ProductContentRun {
            if (ProductContentRun::query()->where('status', ProductContentRun::ACTIVE)->lockForUpdate()->exists()) {
                throw new \RuntimeException('Another product content run is already in progress. Stop it before starting a new one.');
            }

            return ProductContentRun::create([
                'mode' => $mode,
                'min_description_length' => $minLength,
                'status' => ProductContentRun::ACTIVE,
                'total_candidates' => $this->estimateCandidates($mode, $minLength),
                'started_by' => $userId,
                'heartbeat_at' => now(),
            ]);
        });
    }

    public function stop(ProductContentRun $run): ProductContentRun
    {
        if ($run->status === ProductContentRun::ACTIVE) {
            $run->update(['status' => 'stopped', 'finished_at' => now(), 'current_product_name' => null]);
        }

        return $run->refresh();
    }

    /** Process one product of the active run. Returns null when there is nothing to do. */
    public function processOne(): ?ProductContentRunItem
    {
        $claim = $this->claim();
        if ($claim === null) {
            return null;
        }
        [$run, $product] = $claim;
        $mode = $run->mode === 'repair_descriptions' ? 'description' : $run->mode;
        $repair = $run->mode === 'repair_descriptions';
        $oldDescription = $product->description;
        $hadImage = $mode !== 'description' && $product->hasUsableImage();
        $started = hrtime(true);
        $warnings = [];
        try {
            $this->enrichment->enrich($product, $mode, $repair, $run->min_description_length);
            $product->refresh();
            $result = $this->enrichment->isReady($product, $run->mode, $run->min_description_length) ? 'complete' : 'needs_review';
            $warnings = (array) data_get($product->specifications, 'content_enrichment_warnings', []);
        } catch (\Throwable $error) {
            Log::error('Product content run item failed.', ['run_id' => $run->id, 'product_id' => $product->id, 'exception' => $error]);
            $result = 'failed';
            $warnings = ['Processing failed: '.mb_strimwidth($error->getMessage(), 0, 240, '...').' Earlier saved content is unchanged.'];
            rescue(fn () => $product->refresh(), null, false);
        }

        $imageUrl = null;
        if ($mode !== 'description' && ! $hadImage) {
            $first = ((array) $product->images)[0] ?? null;
            $imageUrl = is_array($first) ? ($first['imageUrl'] ?? $first['url'] ?? null) : $first;
        }
        $item = ProductContentRunItem::create([
            'run_id' => $run->id,
            'product_id' => $product->id,
            'product_name' => mb_strimwidth((string) $product->product_name, 0, 250, '...'),
            'result' => $result,
            'old_description' => $oldDescription,
            'new_description' => $mode === 'images' ? null : $product->description,
            'description_source' => data_get($product->specifications, 'description_source'),
            'image_url' => is_string($imageUrl) && $imageUrl !== '' ? $imageUrl : null,
            'warnings' => array_values(array_unique($warnings)),
            'duration_ms' => (int) ((hrtime(true) - $started) / 1_000_000),
        ]);

        $counter = ['complete' => 'ready', 'needs_review' => 'needs_review', 'failed' => 'failed'][$result];
        ProductContentRun::query()->whereKey($run->id)->update([
            'processed' => DB::raw('processed + 1'),
            $counter => DB::raw($counter.' + 1'),
            'heartbeat_at' => now(),
        ]);

        return $item;
    }

    /** Keep processing the active run until the time budget is spent. */
    public function work(int $seconds): int
    {
        $deadline = microtime(true) + max(1, $seconds);
        $count = 0;
        while (microtime(true) < $deadline && $this->processOne() !== null) {
            $count++;
        }

        return $count;
    }

    /** @return array{0: ProductContentRun, 1: Product}|null */
    private function claim(): ?array
    {
        // Bounded number of scans per claim keeps the run row lock short.
        for ($scan = 0; $scan < 10; $scan++) {
            $claim = DB::transaction(function (): array {
                $run = ProductContentRun::query()->where('status', ProductContentRun::ACTIVE)
                    ->latest('id')->lockForUpdate()->first();
                if (! $run) {
                    return ['done' => true];
                }
                $next = $this->enrichment->nextCandidate($run->mode, $run->after_id, $run->min_description_length);
                $run->after_id = $next['after_id'];
                $run->heartbeat_at = now();
                if ($next['product']) {
                    $run->current_product_name = mb_strimwidth((string) $next['product']->product_name, 0, 250, '...');
                    $run->save();

                    return ['done' => true, 'claim' => [$run, $next['product']]];
                }
                if ($next['exhausted']) {
                    $run->status = 'completed';
                    $run->finished_at = now();
                    $run->current_product_name = null;
                    $run->save();

                    return ['done' => true];
                }
                $run->save();

                return ['done' => false];
            });
            if ($claim['done']) {
                return $claim['claim'] ?? null;
            }
        }

        return null;
    }

    private function estimateCandidates(string $mode, int $minLength): int
    {
        if ($mode === 'images') {
            return Product::query()->where(fn ($q) => $q->whereNull('images')->orWhere('images', 'not like', '%http%'))->count();
        }

        return $this->enrichment->candidateQuery($mode, $minLength)->reorder()->count();
    }
}
