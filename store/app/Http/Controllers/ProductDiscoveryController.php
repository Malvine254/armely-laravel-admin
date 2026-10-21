<?php

namespace App\Http\Controllers;

use App\Exceptions\TDSynnexApiException;
use App\Models\User;
use App\Models\Product;
use App\Services\TDSynnexService;
use Illuminate\Database\QueryException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProductDiscoveryController extends Controller
{
    public function __construct(
        private readonly TDSynnexService $supplier,
        private readonly ProductController $catalog
    ) {}

    public function search(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'search' => ['required', 'string', 'min:2', 'max:200'],
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'min_price' => ['sometimes', 'numeric', 'min:0'],
            'max_price' => ['sometimes', 'nullable', 'numeric', 'min:0'],
        ]);

        try {
            $page = (int) ($validated['page'] ?? 1);
            $perPage = (int) ($validated['per_page'] ?? 10);
            $result = $this->searchPage(trim($validated['search']), $perPage, $page, $request->hasAny(['page', 'per_page']),
                isset($validated['min_price']) ? (float) $validated['min_price'] : null,
                !empty($validated['max_price']) ? (float) $validated['max_price'] : null);
            $products = $result['products'];
            $data = array_map(fn (array $product) => $this->identity($product, $request->user('sanctum'), $validated['search']), $products);
        } catch (TDSynnexApiException|ConnectionException|RequestException|QueryException|\InvalidArgumentException $error) {
            return $this->unavailable($error);
        }

        return response()->json([
            'success' => true,
            'data' => $data,
            'pagination' => [
                'page' => $page,
                'per_page' => $perPage,
                'total' => $result['total'],
                'has_more' => $result['has_more'],
                'total_is_estimate' => $result['total_is_estimate'],
            ],
            'message' => $products === []
                ? 'No in-stock supplier match found. Try the manufacturer and exact model or part number.'
                : 'Closest supplier matches. Titles may omit configuration details; verify the exact model before purchasing.',
        ])->header('Cache-Control', 'private, no-store');
    }

    public function details(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'search' => ['required', 'string', 'min:2', 'max:200'],
            'product_id' => ['required', 'string', 'max:150'],
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ]);

        try {
            $result = $this->searchPage(trim($validated['search']), (int) ($validated['per_page'] ?? 10), (int) ($validated['page'] ?? 1), $request->hasAny(['page', 'per_page']));
            $products = $result['products'];
            $product = collect($products)->first(fn (array $row) => (string) ($row['productId'] ?? '') === $validated['product_id']);
            abort_if($product === null, 404, 'Product not found in these supplier search results. Search again.');

            $saved = $this->supplier->saveDiscoveredProduct($product, $validated['search']);
            $details = $this->supplier->enrichDiscoveredProduct($saved);
            $identity = $this->identity($product, $request->user('sanctum'), $validated['search']);
        } catch (TDSynnexApiException|ConnectionException|RequestException|QueryException|\InvalidArgumentException $error) {
            return $this->unavailable($error);
        }

        return response()->json([
            'success' => true,
            'data' => [...$identity, ...$details],
        ])->header('Cache-Control', 'private, no-store');
    }

    public function content(Request $request, string $productId): JsonResponse
    {
        $product = Product::query()->where('tdsynnex_product_id', $productId)->firstOrFail();
        try {
            $details = $this->supplier->enrichDiscoveredProduct($product);
            $payload = $this->catalog->cartProductPayload($product->fresh(), $request->user('sanctum'));
        } catch (TDSynnexApiException|ConnectionException|RequestException|QueryException $error) {
            return $this->unavailable($error);
        }

        return response()->json([
            'success' => true,
            'data' => $payload,
            'warnings' => $details['warnings'],
        ])->header('Cache-Control', 'private, no-store');
    }

    private function searchPage(string $search, int $perPage, int $page, bool $paginate, ?float $minPrice = null, ?float $maxPrice = null): array
    {
        if ($paginate) {
            return $this->supplier->discoverInStockPage($search, $perPage, $page, $minPrice, $maxPrice);
        }
        $products = $this->supplier->filterStorefrontSupplierProducts(
            $this->supplier->searchPriceAvailabilityCatalog($search, $perPage, false, true, true),
            $minPrice, $maxPrice
        );

        return ['products' => $products, 'total' => count($products), 'has_more' => false, 'total_is_estimate' => false];
    }

    private function identity(array $product, ?User $user, string $search): array
    {
        $saved = $this->supplier->saveDiscoveredProduct($product, $search);
        $payload = $this->catalog->cartProductPayload($saved, $user);

        return [
            'product_id' => (string) ($product['productId'] ?? ''),
            'name' => (string) ($product['productName'] ?? ''),
            'manufacturer' => (string) ($product['manufacturer'] ?? $product['vendorName'] ?? ''),
            'part_number' => (string) ($product['mfgPartNo'] ?? ''),
            'supplier_description' => trim(strip_tags((string) ($product['description'] ?? ''))),
            'productPrice' => $payload['productPrice'],
            'pricing_available' => (float) $payload['productPrice'][0]['rsPrice'] > 0,
            'availableQuantity' => isset($product['availableQuantity']) ? max(0, (int) $product['availableQuantity']) : null,
            'discontinueProduct' => (bool) ($product['discontinueProduct'] ?? false),
            'catalog_product' => $payload,
        ];
    }

    private function unavailable(\Exception $error): JsonResponse
    {
        Log::warning('Product discovery provider unavailable.', ['exception' => $error::class]);

        return response()->json([
            'success' => false,
            'message' => 'Product lookup is temporarily unavailable. Please try again shortly.',
        ], 503);
    }
}
