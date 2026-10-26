<?php

namespace App\Http\Controllers;

use App\Jobs\EnrichPriceAvailabilityProductImageJob;
use App\Jobs\EnrichProductContentJob;
use App\Jobs\QueueMissingProductContentJob;
use App\Models\Product;
use App\Services\AzureOpenAiChatService;
use App\Services\ProductContentEnrichmentService;
use App\Services\TDSynnexService;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class AdminImportedProductController extends Controller
{
    private ?array $cachedLocalImageProductIds = null;

    public function __construct(private readonly TDSynnexService $tdsynnexService)
    {
    }

    private function applyImageFilter($query, string $imageFilter): void
    {
        $localImageProductIds = $this->localImageProductIds();

        if ($imageFilter === 'with_image') {
            $query->where(function ($q) use ($localImageProductIds) {
                $q->where('images', 'like', '%http%');
                if ($localImageProductIds !== []) {
                    $q->orWhereIn('tdsynnex_product_id', $localImageProductIds);
                }
            });
        } elseif ($imageFilter === 'no_image') {
            $query->where(fn ($q) => $q->whereNull('images')->orWhere('images', 'not like', '%http%'));
            if ($localImageProductIds !== []) {
                $query->where(fn ($q) => $q->whereNull('tdsynnex_product_id')
                    ->orWhereNotIn('tdsynnex_product_id', $localImageProductIds));
            }
        }
    }

    private function localImageProductIds(): array
    {
        if ($this->cachedLocalImageProductIds !== null) {
            return $this->cachedLocalImageProductIds;
        }

        $paths = glob(public_path('images/products/*.{jpg,jpeg,png,webp,gif,avif}'), GLOB_BRACE) ?: [];

        return $this->cachedLocalImageProductIds = array_values(array_unique(array_filter(array_map(
            static function (string $path): ?int {
                $stem = pathinfo($path, PATHINFO_FILENAME);
                return ctype_digit($stem) ? (int) $stem : null;
            },
            $paths
        ))));
    }

    private function authorizeAdmin(Request $request): void
    {
        abort_unless(in_array((string) $request->user()?->role, ['admin', 'super_admin'], true), 403);
    }

    public function all(Request $request): JsonResponse
    {
        $this->authorizeAdmin($request);

        $query = Product::query();
        $search = trim((string) $request->query('search', ''));
        if ($search !== '') {
            $like = '%' . $search . '%';
            $query->where(fn ($q) => $q->where('product_name', 'like', $like)
                ->orWhere('manufacturer', 'like', $like)
                ->orWhere('mfg_part_no', 'like', $like)
                ->orWhere('tdsynnex_sku_no', 'like', $like)
                ->orWhere('tdsynnex_product_id', 'like', $like));
        }

        $vendor = trim((string) $request->query('vendor', ''));
        if ($vendor !== '') {
            $query->where('vendor_id', $vendor);
        }

        $imageFilter = trim((string) $request->query('image_filter', 'all'));
        $this->applyImageFilter($query, $imageFilter);
        if ($request->boolean('missing_description')) {
            $query->missingDescription();
        }

        $products = $query->orderByDesc('updated_at')
            ->paginate(min(100, max(10, (int) $request->query('per_page', 10))));
        $products->getCollection()->transform(fn (Product $product) => [
            'id' => $product->id,
            'name' => $product->product_name,
            'manufacturer' => $product->manufacturer,
            'mpn' => $product->mfg_part_no,
            'sku' => $product->tdsynnex_sku_no ?: $product->tdsynnex_product_id,
            'vendor' => $product->vendor_id,
            'price' => \App\Support\OfferPricing::sellPrice($product),
            'quantity' => (int) ($product->quantity ?: data_get($product->specifications, 'availableQuantity', 0)),
            'available' => (bool) $product->is_available,
            'has_image' => $product->hasUsableImage(),
            'has_description' => !$product->needsDescription(),
            'description' => $product->description,
            'description_source' => data_get($product->specifications, 'description_source'),
            'description_repair_status' => data_get($product->specifications, 'description_repair_status'),
            'content_status' => data_get($product->specifications, 'content_enrichment_status'),
            'content_warnings' => (array) data_get($product->specifications, 'content_enrichment_warnings', []),
            'updated_at' => optional($product->updated_at)->toIso8601String(),
        ]);

        return response()->json([
            'success' => true,
            'data' => $products,
            'stats' => [
                'total' => Product::count(),
                'available' => Product::where('is_available', true)->count(),
                'with_images' => tap(Product::query(), fn ($query) => $this->applyImageFilter($query, 'with_image'))->count(),
                'without_images' => tap(Product::query(), fn ($query) => $this->applyImageFilter($query, 'no_image'))->count(),
                'without_descriptions' => Product::query()->missingDescription()->count(),
            ],
        ]);
    }

    public function enrichContent(Request $request, AzureOpenAiChatService $ai, ?Product $product = null): JsonResponse
    {
        $this->authorizeAdmin($request);
        $validated = $request->validate(['mode' => ['sometimes', 'required', 'in:both,description,images']]);
        $mode = $validated['mode'] ?? 'both';
        if (!$ai->isConfigured()) {
            return response()->json([
                'success' => false,
                'message' => 'Configure the store AZURE_OPENAI_* settings before enriching product content.',
            ], 422);
        }
        if ($product) {
            EnrichProductContentJob::dispatch($product->id, $mode);
        } else {
            QueueMissingProductContentJob::dispatch(0, $mode);
        }

        return response()->json([
            'success' => true,
            'mode' => $mode,
            'message' => 'Background enrichment requested. The products-sync worker fills missing content only; refresh to see progress.',
        ], 202);
    }

    public function processContent(Request $request, AzureOpenAiChatService $ai, ProductContentEnrichmentService $service): JsonResponse
    {
        $this->authorizeAdmin($request);
        $validated = $request->validate([
            'mode' => ['required', 'in:description,images,repair_descriptions'],
            'after_id' => ['required', 'integer', 'min:0'],
        ]);
        if (! $ai->isConfigured()) {
            return response()->json(['success' => false, 'message' => 'Configure the store AZURE_OPENAI_* settings before processing content.'], 422);
        }
        foreach (['content_enrichment_attempted_at', 'description_enrichment_attempted_at', 'images_enrichment_attempted_at'] as $column) {
            if (! Schema::hasColumn('products', $column)) {
                return response()->json(['success' => false,
                    'message' => 'Store content migrations are missing. Deploy php artisan migrate --force from the store directory before running.'], 503);
            }
        }
        // One product per request; do not depend on exec(), PHP CLI or a queue worker.
        if (function_exists('set_time_limit')) {
            set_time_limit(150);
        }
        try {
            $result = $service->processNext($validated['mode'], (int) $validated['after_id']);
        } catch (\Illuminate\Http\Client\ConnectionException|\Illuminate\Http\Client\RequestException|\RuntimeException|QueryException $error) {
            Log::error('Admin product content processing failed.', ['mode' => $validated['mode'], 'exception' => $error]);
            return response()->json(['success' => false,
                'message' => 'Product processing failed. Previous results are saved. Check the store logs and retry; no completion is confirmed.'], 503);
        }

        return response()->json(['success' => true, 'data' => $result]);
    }

    public function index(Request $request): JsonResponse
    {
        $this->authorizeAdmin($request);
        $query = Product::query()->whereNotNull('search_imported_at');
        $search = trim((string) $request->query('search', ''));
        if ($search !== '') {
            $like = '%' . $search . '%';
            $query->where(fn ($q) => $q->where('product_name', 'like', $like)
                ->orWhere('mfg_part_no', 'like', $like)
                ->orWhere('tdsynnex_sku_no', 'like', $like)
                ->orWhere('search_import_query', 'like', $like));
        }
        $status = trim((string) $request->query('status', ''));
        if ($status !== '') {
            $query->where('search_import_review_status', $status);
        }
        if (filter_var($request->query('missing_image', false), FILTER_VALIDATE_BOOLEAN)) {
            $query->where(fn ($q) => $q->whereNull('images')->orWhere('images', '')->orWhere('images', '[]')->orWhere('images', 'null'));
        }

        $products = $query->orderByDesc('search_imported_at')->paginate(min(100, max(10, (int) $request->query('per_page', 10))));
        $products->getCollection()->transform(fn (Product $product) => [
            'id' => $product->id,
            'name' => $product->product_name,
            'manufacturer' => $product->manufacturer,
            'mpn' => $product->mfg_part_no,
            'sku' => $product->tdsynnex_sku_no ?: $product->tdsynnex_product_id,
            'query' => $product->search_import_query,
            'status' => $product->search_import_review_status ?: 'pending',
            'storefront_pinned' => (bool) $product->is_storefront_pinned,
            'has_image' => !empty((array) $product->images),
            'image_attempted_at' => optional($product->image_enrichment_attempted_at)->toIso8601String(),
            'imported_at' => optional($product->search_imported_at)->toIso8601String(),
            'price' => \App\Support\OfferPricing::sellPrice($product),
            'quantity' => (int) ($product->quantity ?: data_get($product->specifications, 'availableQuantity', 0)),
        ]);

        $base = Product::query()->whereNotNull('search_imported_at');
        return response()->json(['success' => true, 'data' => $products, 'stats' => [
            'total' => (clone $base)->count(),
            'pending' => (clone $base)->where('search_import_review_status', 'pending')->count(),
            'missing_images' => (clone $base)->where(fn ($q) => $q->whereNull('images')->orWhere('images', '')->orWhere('images', '[]')->orWhere('images', 'null'))->count(),
        ]]);
    }

    public function update(Request $request, Product $product): JsonResponse
    {
        $this->authorizeAdmin($request);
        abort_if($product->search_imported_at === null, 404);
        $validated = $request->validate(['status' => ['required', 'in:pending,approved,rejected']]);
        $product->update([
            'search_import_review_status' => $validated['status'],
            'search_import_reviewed_at' => now(),
            'search_import_reviewed_by' => $request->user()->id,
        ]);
        return response()->json(['success' => true, 'message' => 'Product review status updated.']);
    }

    public function supplierSearch(Request $request): JsonResponse
    {
        $this->authorizeAdmin($request);
        $validated = $request->validate([
            'q' => ['required', 'string', 'min:2', 'max:500'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        $query = trim($validated['q']);
        $results = $this->tdsynnexService->searchPriceAvailabilityCatalog(
            $query,
            (int) ($validated['limit'] ?? 25),
            false
        );
        $mappedRows = collect($results)->mapWithKeys(function (array $product) {
            $row = $this->tdsynnexService->mapPriceAvailabilityCatalogProductToDatabaseRow($product);
            $identifier = (string) ($product['sku'] ?? $product['productId'] ?? '');
            return $row === null ? [] : [$identifier => $row];
        });
        $identifiers = $mappedRows->pluck('tdsynnex_product_id')->unique()->values();
        $localProducts = Product::query()
            ->whereIn('tdsynnex_product_id', $identifiers)
            ->get()->keyBy('tdsynnex_product_id');

        $data = collect($results)->take((int) ($validated['limit'] ?? 25))->map(function (array $product) use ($localProducts, $mappedRows) {
            $identifier = (string) ($product['sku'] ?? $product['productId'] ?? '');
            $local = $localProducts->get($mappedRows->get($identifier)['tdsynnex_product_id'] ?? null);
            $status = trim((string) ($product['status'] ?? ''));
            $quantity = (int) ($product['availableQuantity'] ?? $product['totalQuantity'] ?? 0);
            $discontinued = (bool) ($product['discontinueProduct'] ?? false);
            $orderable = !$discontinued && (
                $quantity > 0 || in_array(strtolower($status), ['active', 'available', 'in stock'], true)
            );

            return [
                'identifier' => $identifier,
                'product_id' => (string) ($product['productId'] ?? $identifier),
                'name' => (string) ($product['productName'] ?? $identifier),
                'manufacturer' => (string) ($product['manufacturer'] ?? $product['vendorName'] ?? ''),
                'mpn' => (string) ($product['mfgPartNo'] ?? ''),
                'price' => (float) data_get($product, 'productPrice.0.rsPrice', $product['price'] ?? 0),
                'quantity' => $quantity,
                'status' => $status,
                'discontinued' => $discontinued,
                'orderable' => $orderable,
                'already_imported' => $local !== null,
                'storefront_pinned' => (bool) ($local?->is_storefront_pinned),
            ];
        })->values();

        return response()->json(['success' => true, 'data' => $data]);
    }

    public function importSupplierProduct(Request $request): JsonResponse
    {
        $this->authorizeAdmin($request);
        $validated = $request->validate([
            'identifier' => ['required', 'string', 'max:150'],
            'search_query' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $product = $this->tdsynnexService->importSelectedPriceAvailabilityProduct(
                $validated['identifier'],
                (string) ($validated['search_query'] ?? ''),
                (int) $request->user()->id
            );
        } catch (QueryException $exception) {
            Log::error('Supplier product import database failure', [
                'identifier' => $validated['identifier'],
                'error_code' => $exception->getCode(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'The product could not be saved because the catalog database is not ready.',
            ], 500);
        } catch (\InvalidArgumentException|\RuntimeException $exception) {
            return response()->json(['success' => false, 'message' => $exception->getMessage()], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Product imported and pinned to the storefront assortment.',
            'data' => ['id' => $product->id, 'storefront_pinned' => true],
        ], 201);
    }

    public function updateStorefrontPin(Request $request, Product $product): JsonResponse
    {
        $this->authorizeAdmin($request);
        $validated = $request->validate(['pinned' => ['required', 'boolean']]);
        $product->update([
            'is_storefront_pinned' => $validated['pinned'],
            'storefront_pinned_at' => $validated['pinned'] ? now() : null,
        ]);
        \Illuminate\Support\Facades\Cache::forget('storefront_capped_product_ids_v5');
        \Illuminate\Support\Facades\Cache::forget('menu_categories:v15:pinned-storefront-cap');

        return response()->json(['success' => true, 'message' => $validated['pinned'] ? 'Product pinned to storefront.' : 'Storefront pin removed.']);
    }

    public function enrichImage(Request $request, Product $product): JsonResponse
    {
        $this->authorizeAdmin($request);
        abort_if($product->search_imported_at === null, 404);
        EnrichPriceAvailabilityProductImageJob::dispatch($product->id);
        return response()->json(['success' => true, 'message' => 'Image lookup queued.']);
    }
}
