<?php

namespace App\Http\Controllers;

use App\Exceptions\TDSynnexApiException;
use App\Services\TDSynnexService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProductDiscoveryController extends Controller
{
    public function __construct(private readonly TDSynnexService $supplier) {}

    public function search(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'search' => ['required', 'string', 'min:2', 'max:200'],
        ]);

        try {
            $products = $this->supplier->searchPriceAvailabilityCatalog(trim($validated['search']), 10, false, true, true);
        } catch (TDSynnexApiException|ConnectionException|RequestException $error) {
            return $this->unavailable($error);
        }

        return response()->json([
            'success' => true,
            'data' => array_map(fn (array $product) => $this->identity($product), $products),
            'message' => $products === []
                ? 'No supplier match found. Try the manufacturer and exact model or part number.'
                : 'Closest supplier matches. Titles may omit configuration details; verify the exact model before purchasing.',
        ]);
    }

    public function details(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'search' => ['required', 'string', 'min:2', 'max:200'],
            'product_id' => ['required', 'string', 'max:150'],
        ]);

        try {
            $products = $this->supplier->searchPriceAvailabilityCatalog(trim($validated['search']), 10, false, true, true);
            $product = collect($products)->first(fn (array $row) => (string) ($row['productId'] ?? '') === $validated['product_id']);
            abort_if($product === null, 404, 'Product not found in these supplier search results. Search again.');

            $details = $this->supplier->discoverOnlineProductDetails($product);
        } catch (TDSynnexApiException|ConnectionException|RequestException $error) {
            return $this->unavailable($error);
        }

        return response()->json([
            'success' => true,
            'data' => [...$this->identity($product), ...$details],
        ]);
    }

    private function identity(array $product): array
    {
        return [
            'product_id' => (string) ($product['productId'] ?? ''),
            'name' => (string) ($product['productName'] ?? ''),
            'manufacturer' => (string) ($product['manufacturer'] ?? $product['vendorName'] ?? ''),
            'part_number' => (string) ($product['mfgPartNo'] ?? ''),
            'supplier_description' => trim(strip_tags((string) ($product['description'] ?? ''))),
        ];
    }

    private function unavailable(\Exception $error): JsonResponse
    {
        Log::warning('Product discovery provider unavailable.', ['error' => $error->getMessage()]);

        return response()->json([
            'success' => false,
            'message' => 'Product lookup is temporarily unavailable. Please try again shortly.',
        ], 503);
    }
}
