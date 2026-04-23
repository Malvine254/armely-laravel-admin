<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductReview;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ReviewController extends Controller
{
    /**
     * List reviews for a product.
     */
    public function index(string $productId, Request $request): JsonResponse
    {
        if (!Schema::hasTable('product_reviews')) {
            return response()->json([
                'data' => [],
                'meta' => [
                    'current_page' => 1,
                    'last_page' => 1,
                    'total' => 0,
                ],
                'stats' => [
                    'total' => 0,
                    'average' => 0,
                    'breakdown' => [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0],
                ],
            ]);
        }

        try {
            $reviews = ProductReview::where('product_id', $productId)
                ->with('user:' . implode(',', $this->reviewUserColumns()))
                ->orderByDesc('created_at')
                ->paginate($request->integer('per_page', 10));

            $reviewItems = $reviews->items();
            $productIds = collect($reviewItems)
                ->pluck('product_id')
                ->filter()
                ->map(fn ($id) => (string) $id)
                ->unique()
                ->values();

            $productNameLookup = [];
            if ($productIds->isNotEmpty()) {
                $products = Product::query()
                    ->where(function ($q) use ($productIds) {
                        $q->whereIn('tdsynnex_product_id', $productIds)
                            ->orWhereIn('tdsynnex_sku_no', $productIds);
                    })
                    ->get(['tdsynnex_product_id', 'tdsynnex_sku_no', 'product_name']);

                foreach ($products as $product) {
                    $name = (string) ($product->product_name ?? '');
                    if ($name === '') {
                        continue;
                    }

                    if ($product->tdsynnex_product_id !== null) {
                        $productNameLookup[(string) $product->tdsynnex_product_id] = $name;
                    }

                    if (!empty($product->tdsynnex_sku_no)) {
                        $productNameLookup[(string) $product->tdsynnex_sku_no] = $name;
                    }
                }
            }

            $reviewItems = array_map(function ($review) use ($productNameLookup) {
                $reviewData = $review->toArray();
                $id = (string) ($reviewData['product_id'] ?? '');
                $reviewData['product_name'] = $productNameLookup[$id] ?? ($id !== '' ? ('Product ' . $id) : 'Unknown Product');
                return $reviewData;
            }, $reviewItems);

            $stats = ProductReview::where('product_id', $productId)
                ->selectRaw('COUNT(*) as total, AVG(rating) as average, ' .
                    'SUM(CASE WHEN rating = 5 THEN 1 ELSE 0 END) as five, ' .
                    'SUM(CASE WHEN rating = 4 THEN 1 ELSE 0 END) as four, ' .
                    'SUM(CASE WHEN rating = 3 THEN 1 ELSE 0 END) as three, ' .
                    'SUM(CASE WHEN rating = 2 THEN 1 ELSE 0 END) as two, ' .
                    'SUM(CASE WHEN rating = 1 THEN 1 ELSE 0 END) as one')
                ->first();

            return response()->json([
                'data' => $reviewItems,
                'meta' => [
                    'current_page' => $reviews->currentPage(),
                    'last_page' => $reviews->lastPage(),
                    'total' => $reviews->total(),
                ],
                'stats' => [
                    'total' => (int) $stats->total,
                    'average' => $stats->total > 0 ? round((float) $stats->average, 1) : 0,
                    'breakdown' => [
                        5 => (int) $stats->five,
                        4 => (int) $stats->four,
                        3 => (int) $stats->three,
                        2 => (int) $stats->two,
                        1 => (int) $stats->one,
                    ],
                ],
            ]);
        } catch (\Throwable $e) {
            Log::error('ReviewController.index failed', [
                'product_id' => $productId,
                'message' => $e->getMessage(),
                'exception' => get_class($e),
            ]);

            return response()->json([
                'data' => [],
                'meta' => [
                    'current_page' => 1,
                    'last_page' => 1,
                    'total' => 0,
                ],
                'stats' => [
                    'total' => 0,
                    'average' => 0,
                    'breakdown' => [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0],
                ],
            ]);
        }
    }

    private function reviewUserColumns(): array
    {
        $columns = ['id', 'name', 'email'];
        if (Schema::hasColumn('users', 'profile_picture')) {
            $columns[] = 'profile_picture';
        }

        return $columns;
    }

    /**
     * Store a new review (authenticated users only).
     */
    public function store(string $productId, Request $request): JsonResponse
    {
        if (!Schema::hasTable('product_reviews')) {
            return response()->json([
                'message' => 'Reviews are temporarily unavailable.',
            ], 503);
        }

        try {
            $validated = $request->validate([
                'rating' => ['required', 'integer', 'min:1', 'max:5'],
                'title'  => ['nullable', 'string', 'max:150'],
                'body'   => ['required', 'string', 'max:5000'],
                'images' => ['nullable', 'array', 'max:5'],
                'images.*' => ['image', 'mimes:jpeg,png,jpg,gif,webp', 'max:3072'],
            ]);

            // Prevent duplicate reviews from same user on same product
            $existing = ProductReview::where('user_id', $request->user()->id)
                ->where('product_id', $productId)
                ->exists();

            if ($existing) {
                return response()->json([
                    'message' => 'You have already reviewed this product.',
                ], 422);
            }

            $imagePaths = [];
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $filename = Str::uuid() . '.' . $image->getClientOriginalExtension();
                    $path = $image->storeAs('review-images', $filename, 'public');
                    $imagePaths[] = '/storage/' . $path;
                }
            }

            $review = ProductReview::create([
                'user_id'    => $request->user()->id,
                'product_id' => $productId,
                'rating'     => $validated['rating'],
                'title'      => $validated['title'] ?? null,
                'body'       => $validated['body'],
                'images'     => !empty($imagePaths) ? $imagePaths : null,
            ]);

            $review->load('user:' . implode(',', $this->reviewUserColumns()));

            $productName = Product::query()
                ->where('tdsynnex_product_id', $productId)
                ->orWhere('tdsynnex_sku_no', $productId)
                ->value('product_name');

            $reviewData = $review->toArray();
            $reviewData['product_name'] = $productName ?: ('Product ' . $productId);

            return response()->json([
                'message' => 'Review submitted successfully.',
                'data'    => $reviewData,
            ], 201);
        } catch (\Throwable $e) {
            Log::error('ReviewController.store failed', [
                'product_id' => $productId,
                'user_id' => optional($request->user())->id,
                'message' => $e->getMessage(),
                'exception' => get_class($e),
            ]);

            return response()->json([
                'message' => 'Unable to submit review right now. Please try again shortly.',
            ], 500);
        }
    }

    /**
     * Delete own review (authenticated users only).
     */
    public function destroy(string $productId, int $reviewId, Request $request): JsonResponse
    {
        $review = ProductReview::where('id', $reviewId)
            ->where('product_id', $productId)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$review) {
            return response()->json(['message' => 'Review not found.'], 404);
        }

        // Clean up uploaded images
        if (!empty($review->images)) {
            foreach ($review->images as $imagePath) {
                $storagePath = str_replace('/storage/', '', $imagePath);
                Storage::disk('public')->delete($storagePath);
            }
        }

        $review->delete();

        return response()->json(['message' => 'Review deleted.']);
    }
}
