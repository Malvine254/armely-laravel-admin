<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductReview;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ReviewController extends Controller
{
    /**
     * List reviews for a product.
     */
    public function index(string $productId, Request $request): JsonResponse
    {
        $reviews = ProductReview::where('product_id', $productId)
            ->with('user:id,name,email,profile_picture')
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
                ->whereIn('tdsynnex_product_id', $productIds)
                ->orWhereIn('tdsynnex_sku_no', $productIds)
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

        // Build summary stats
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
                'last_page'    => $reviews->lastPage(),
                'total'        => $reviews->total(),
            ],
            'stats' => [
                'total'   => (int) $stats->total,
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
    }

    /**
     * Store a new review (authenticated users only).
     */
    public function store(string $productId, Request $request): JsonResponse
    {
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

        $review->load('user:id,name,email,profile_picture');

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
