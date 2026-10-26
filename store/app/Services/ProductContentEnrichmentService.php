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

    public function queueMissing(int $limit = 0, string $mode = 'both'): int
    {
        $this->validateMode($mode);
        $attemptColumn = $mode === 'both' ? 'content_enrichment_attempted_at' : $mode.'_enrichment_attempted_at';
        $queued = 0;
        // Oldest/unattempted products go first so unresolved items cannot starve the rest.
        $products = Product::query()
            ->where(fn (Builder $q) => $q->whereNull($attemptColumn)
                ->orWhere($attemptColumn, '<=', now()->subDay()))
            ->orderBy($attemptColumn)->orderBy('id')
            ->cursor();
        foreach ($products as $product) {
            if (! $this->needsContent($product, $mode)) {
                continue;
            }
            EnrichProductContentJob::dispatch($product->id, $mode);
            $queued++;
            if ($limit > 0 && $queued >= $limit) {
                break;
            }
        }

        return $queued;
    }

    private function validateMode(string $mode): void
    {
        if (! in_array($mode, ['both', 'description', 'images'], true)) {
            throw new \InvalidArgumentException('Unsupported product enrichment mode.');
        }
    }

    private function needsContent(Product $product, string $mode): bool
    {
        return ($mode !== 'images' && $product->needsDescription())
            || ($mode !== 'description' && ! $product->hasUsableImage());
    }

    public function enrich(Product $product, string $mode = 'both'): void
    {
        $this->validateMode($mode);
        if (! $this->needsContent($product, $mode)) {
            return;
        }

        $details = $this->supplier->verifiedOnlineProductDetails($product);
        $description = trim((string) ($details['description'] ?? ''));
        $descriptionSource = 'openai-verified-online';
        $warnings = (array) ($details['warnings'] ?? []);
        if ($mode !== 'images' && $product->needsDescription()) {
            $verifiedDescription = $description;
            $generated = $this->ai->generateProductDescription([
                'product_name' => (string) $product->product_name,
                'manufacturer' => (string) $product->manufacturer,
                'part_number' => (string) $product->mfg_part_no,
                'verified_website_description' => $verifiedDescription,
                'website_sources' => (array) ($details['sources'] ?? []),
                'verified_specifications' => (array) (($details['specifications'] ?? []) ?: data_get($product->specifications, 'online_specifications', [])),
            ]);
            $description = $generated ?? $verifiedDescription;
            $descriptionSource = $generated === null ? 'openai-verified-online'
                : ($verifiedDescription !== '' || ! empty($details['specifications'])
                    ? 'ai-generated-from-verified-web-evidence' : 'ai-generated-from-catalog-facts');
            if ($description === '') {
                $warnings[] = 'AI could not generate a factual product description. Check the AI configuration and retry.';
            }
        }

        DB::transaction(function () use ($product, $details, $description, $descriptionSource, $warnings, $mode): void {
            $current = Product::query()->lockForUpdate()->findOrFail($product->id);
            foreach (['product_name', 'manufacturer', 'mfg_part_no'] as $field) {
                if ($current->$field !== $product->$field) {
                    throw new \RuntimeException('Product identity changed during enrichment; retry with the current identity.');
                }
            }
            // Recheck under the lock: an administrator may have supplied content during the lookup.
            $spec = (array) $current->specifications;
            if ($mode !== 'images' && $current->needsDescription() && $description !== '') {
                $current->description = $description;
                $spec['description_source'] = $descriptionSource;
            }
            if ($mode !== 'description' && ! $current->hasUsableImage() && ! empty($details['image_url'])) {
                $current->images = [['imageUrl' => $details['image_url'], 'source' => 'openai-verified-online']];
            }
            if (! empty($details['specifications'])) {
                $spec['online_specifications'] = $details['specifications'];
            }
            if (! empty($details['sources'])) {
                $spec['online_sources'] = $details['sources'];
            }
            if ($mode !== 'description' && ! $current->hasUsableImage()) {
                $warnings[] = 'No confidently matched product image was found. Check the search provider configuration or supply a verified image manually.';
            }
            $spec['content_enrichment_status'] = ! $current->needsDescription() && $current->hasUsableImage()
                ? 'complete' : 'needs_review';
            $spec['content_enrichment_warnings'] = array_values(array_unique($warnings));
            $spec['content_enrichment_mode'] = $mode;
            $spec['content_enrichment_result'] = $this->needsContent($current, $mode) ? 'needs_review' : 'complete';
            $current->specifications = $spec;
            $current->content_enrichment_attempted_at = now();
            if ($mode !== 'images') {
                $current->description_enrichment_attempted_at = now();
            }
            if ($mode !== 'description') {
                $current->images_enrichment_attempted_at = now();
            }
            $current->save();
        });
        Cache::forever('catalog:price_version', (string) now()->getTimestampMs());
    }
}
