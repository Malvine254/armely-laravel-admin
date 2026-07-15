<?php

namespace App\Http\Controllers;

use App\Jobs\EnrichPriceAvailabilityProductImageJob;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminImportedProductController extends Controller
{
    private function authorizeAdmin(Request $request): void
    {
        abort_unless(in_array((string) $request->user()?->role, ['admin', 'super_admin'], true), 403);
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

        $products = $query->orderByDesc('search_imported_at')->paginate(min(100, max(10, (int) $request->query('per_page', 25))));
        $products->getCollection()->transform(fn (Product $product) => [
            'id' => $product->id,
            'name' => $product->product_name,
            'manufacturer' => $product->manufacturer,
            'mpn' => $product->mfg_part_no,
            'sku' => $product->tdsynnex_sku_no ?: $product->tdsynnex_product_id,
            'query' => $product->search_import_query,
            'status' => $product->search_import_review_status ?: 'pending',
            'has_image' => !empty((array) $product->images),
            'image_attempted_at' => optional($product->image_enrichment_attempted_at)->toIso8601String(),
            'imported_at' => optional($product->search_imported_at)->toIso8601String(),
            'price' => (float) ($product->retail_price ?: $product->base_price ?: 0),
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

    public function enrichImage(Request $request, Product $product): JsonResponse
    {
        $this->authorizeAdmin($request);
        abort_if($product->search_imported_at === null, 404);
        EnrichPriceAvailabilityProductImageJob::dispatch($product->id);
        return response()->json(['success' => true, 'message' => 'Image lookup queued.']);
    }
}
