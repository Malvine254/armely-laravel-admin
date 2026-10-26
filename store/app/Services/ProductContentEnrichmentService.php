<?php

namespace App\Services;

use App\Jobs\EnrichProductContentJob;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ProductContentEnrichmentService
{
    public function __construct(
        private readonly TDSynnexService $supplier,
        private readonly AzureOpenAiChatService $ai,
    ) {}

    public function queueMissing(int $limit = 0): int
    {
        $queued = 0;
        // Oldest/unattempted products go first so unresolved items cannot starve the rest.
        $products = Product::query()
            ->where(fn (Builder $q) => $q->whereNull('content_enrichment_attempted_at')
                ->orWhere('content_enrichment_attempted_at', '<=', now()->subDay()))
            ->orderBy('content_enrichment_attempted_at')->orderBy('id')
            ->cursor();
        foreach ($products as $product) {
            if (! $product->needsDescription() && $product->hasUsableImage()) {
                continue;
            }
            EnrichProductContentJob::dispatch($product->id);
            $queued++;
            if ($limit > 0 && $queued >= $limit) {
                break;
            }
        }

        return $queued;
    }

    public function enrich(Product $product): void
    {
        if (! $product->needsDescription() && $product->hasUsableImage()) {
            return;
        }

        $details = $this->supplier->verifiedOnlineProductDetails($product);
        $description = trim((string) ($details['description'] ?? ''));
        $descriptionSource = 'openai-verified-online';
        $warnings = (array) ($details['warnings'] ?? []);
        if ($product->needsDescription() && $description === '') {
            $description = $this->ai->generateProductDescription([
                'product_name' => (string) $product->product_name,
                'manufacturer' => (string) $product->manufacturer,
                'part_number' => (string) $product->mfg_part_no,
                'verified_specifications' => (array) (($details['specifications'] ?? []) ?: data_get($product->specifications, 'online_specifications', [])),
            ]) ?? '';
            $descriptionSource = 'ai-generated-from-catalog-facts';
            if ($description === '') {
                $warnings[] = 'AI could not generate a factual product description. Check the AI configuration and retry.';
            }
        }

        DB::transaction(function () use ($product, $details, $description, $descriptionSource, $warnings): void {
            $current = Product::query()->lockForUpdate()->findOrFail($product->id);
            foreach (['product_name', 'manufacturer', 'mfg_part_no'] as $field) {
                if ($current->$field !== $product->$field) {
                    throw new \RuntimeException('Product identity changed during enrichment; retry with the current identity.');
                }
            }
            // Recheck under the lock: an administrator may have supplied content during the lookup.
            $spec = (array) $current->specifications;
            if ($current->needsDescription() && $description !== '') {
                $current->description = $description;
                $spec['description_source'] = $descriptionSource;
            }
            if (! $current->hasUsableImage() && ! empty($details['image_url'])) {
                $current->images = [['imageUrl' => $details['image_url'], 'source' => 'openai-verified-online']];
            }
            if (! empty($details['specifications'])) {
                $spec['online_specifications'] = $details['specifications'];
            }
            if (! empty($details['sources'])) {
                $spec['online_sources'] = $details['sources'];
            }
            if (! $current->hasUsableImage()) {
                $warnings[] = 'No confidently matched product image was found. Check the search provider configuration or supply a verified image manually.';
            }
            $spec['content_enrichment_status'] = ! $current->needsDescription() && $current->hasUsableImage()
                ? 'complete' : 'needs_review';
            $spec['content_enrichment_warnings'] = array_values(array_unique($warnings));
            $current->specifications = $spec;
            $current->content_enrichment_attempted_at = now();
            $current->save();
        });
        Cache::forever('catalog:price_version', (string) now()->getTimestampMs());
    }
}
