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
                ->orWhere($attemptColumn, '<=', now()->subDay())
                ->when($mode !== 'images', fn (Builder $q) => $q->orWhere('specifications->description_source', 'ai-generated-from-catalog-facts')))
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

    public function processNext(string $mode, int $afterId): array
    {
        $this->validateMode($mode);
        $query = Product::query()->where('id', '>', $afterId)->orderBy('id');
        if ($mode === 'description') {
            $query->missingDescription();
        }
        // Bound each HTTP scan even when most catalog products already have images.
        $batch = $query->limit(100)->get();
        foreach ($batch as $product) {
            $afterId = (int) $product->id;
            if (! $this->needsContent($product, $mode)) {
                continue;
            }
            $lock = Cache::lock('store:content-product:'.$product->id, 180);
            if (! $lock->get()) {
                return ['done' => false, 'after_id' => $afterId, 'processed' => false,
                    'message' => 'Product '.$product->id.' is already being processed by another request.'];
            }
            try {
                $this->enrich($product, $mode);
                $product->refresh();
                $ready = ! $this->needsContent($product, $mode);

                return [
                    'done' => false, 'after_id' => $afterId, 'processed' => true,
                    'product_id' => $product->id, 'product_name' => $product->product_name,
                    'result' => $ready ? 'complete' : 'needs_review',
                    'warnings' => (array) data_get($product->specifications, 'content_enrichment_warnings', []),
                    'message' => $ready ? 'Product content saved.' : 'No verified content found; product needs research.',
                ];
            } finally {
                $lock->release();
            }
        }

        return ['done' => $batch->count() < 100, 'after_id' => $afterId, 'processed' => false,
            'message' => $batch->count() < 100 ? 'Catalog scan finished.' : 'Scanning for products with missing content.'];
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
        $verifiedSpecs = (array) (($details['specifications'] ?? []) ?: data_get($product->specifications, 'online_specifications', []));
        $hasEvidence = $description !== '' || $verifiedSpecs !== [];
        if ($mode !== 'images' && $product->needsDescription()) {
            $verifiedDescription = $description;
            $generated = $this->ai->generateProductDescription([
                'product_name' => (string) $product->product_name,
                'manufacturer' => (string) $product->manufacturer,
                'part_number' => (string) $product->mfg_part_no,
                'verified_website_description' => $verifiedDescription,
                'website_sources' => (array) ($details['sources'] ?? []),
                'verified_specifications' => $verifiedSpecs,
                'allow_title_fallback' => true,
            ]);
            $description = $generated ?? $verifiedDescription;
            $descriptionSource = $generated === null ? 'openai-verified-online'
                : ($hasEvidence ? 'ai-generated-from-verified-web-evidence' : 'ai-generated-from-title');
            if ($generated !== null && ! $hasEvidence) {
                $warnings[] = 'Title-based AI description using general category knowledge; not verified against a website. Review before relying on product-specific details.';
            }
            if ($description === '') {
                $warnings[] = 'AI could not approve a description from website evidence or the product title. Review the title and AI configuration, then retry.';
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
            if ($mode !== 'images' && ($spec['description_source'] ?? '') === 'ai-generated-from-catalog-facts'
                && $description === '') {
                $spec['description_repair_backup'] ??= (string) $current->description;
                $current->description = null;
                $spec['description_source'] = 'needs-research';
            }
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
            $current->specifications = $spec;
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
