<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class SharePreviewController extends Controller
{
    public function product(string $productId): View
    {
        $product = Product::query()
            ->where('tdsynnex_product_id', $productId)
            ->orWhere('tdsynnex_sku_no', $productId)
            ->orWhere('mfg_part_no', $productId)
            ->first();

        $productName = trim((string) ($product?->product_name ?? 'Shared product from Armely'));
        $vendor = trim((string) ($product?->vendor_id ?? ''));
        $sku = trim((string) ($product?->mfg_part_no ?? $product?->tdsynnex_sku_no ?? ''));
        $price = $product ? (float) ($product->base_price ?? $product->retail_price ?? 0) : 0.0;
        $description = trim((string) ($product?->description ?? 'View this product on Armely.'));

        $descriptionParts = array_values(array_filter([
            $vendor !== '' ? 'Vendor: ' . $vendor : null,
            $sku !== '' ? 'SKU: ' . $sku : null,
            $price > 0 ? 'Price: $' . number_format($price, 2) : null,
            $description !== '' ? Str::limit(preg_replace('/\s+/', ' ', strip_tags($description)), 160) : null,
        ]));

        return view('share-preview', [
            'title' => $productName,
            'description' => !empty($descriptionParts) ? implode(' | ', $descriptionParts) : 'View this product on Armely.',
            'imageUrl' => $this->makeAbsoluteUrl($this->extractImageUrl($product?->images)),
            'previewUrl' => $this->frontendBaseUrl() . '/share/product/' . rawurlencode($productId),
            'redirectUrl' => $this->frontendBaseUrl() . '/products/' . rawurlencode($productId),
            'ctaLabel' => 'Open product',
        ]);
    }

    public function cart(int $messageId): View
    {
        $message = Message::query()->findOrFail($messageId);
        abort_unless((string) data_get($message->metadata, 'share_type') === 'cart', 404);

        $items = array_values((array) data_get($message->metadata, 'cart.items', []));
        $itemCount = count($items);
        $sharedBy = trim((string) data_get($message->metadata, 'shared_by_name', 'A user'));
        $note = trim((string) data_get($message->metadata, 'note', ''));
        $firstItem = $items[0] ?? [];

        $itemNames = collect($items)
            ->map(fn ($item) => trim((string) ($item['productName'] ?? '')))
            ->filter()
            ->take(3)
            ->implode(', ');

        $descriptionParts = array_values(array_filter([
            $itemCount > 0 ? $itemCount . ' item(s)' : 'Shared cart',
            $itemNames !== '' ? $itemNames : null,
            $note !== '' ? 'Note: ' . Str::limit($note, 120) : null,
        ]));

        return view('share-preview', [
            'title' => $sharedBy . ' shared a cart with you',
            'description' => !empty($descriptionParts) ? implode(' | ', $descriptionParts) : 'Open this shared cart on Armely.',
            'imageUrl' => $this->makeAbsoluteUrl($this->extractImageUrl($firstItem['productImages'] ?? [])),
            'previewUrl' => url()->current(),
            'redirectUrl' => $this->frontendBaseUrl() . '/cart?shared_message=' . urlencode((string) $messageId),
            'ctaLabel' => 'Open shared cart',
        ]);
    }

    public function cartPublic(string $token): View
    {
        $payload = Cache::get($this->publicCartShareCacheKey($token));
        abort_unless(is_array($payload), 404);

        $items = array_values((array) ($payload['items'] ?? []));
        $itemCount = count($items);
        $sharedBy = trim((string) ($payload['shared_by_name'] ?? 'A user'));
        $note = trim((string) ($payload['note'] ?? ''));
        $firstItem = $items[0] ?? [];

        $itemNames = collect($items)
            ->map(fn ($item) => trim((string) ($item['productName'] ?? '')))
            ->filter()
            ->take(3)
            ->implode(', ');

        $descriptionParts = array_values(array_filter([
            $itemCount > 0 ? $itemCount . ' item(s)' : 'Shared cart',
            $itemNames !== '' ? $itemNames : null,
            $note !== '' ? 'Note: ' . Str::limit($note, 120) : null,
        ]));

        return view('share-preview', [
            'title' => $sharedBy . ' shared a cart with you',
            'description' => !empty($descriptionParts) ? implode(' | ', $descriptionParts) : 'Open this shared cart on Armely.',
            'imageUrl' => $this->makeAbsoluteUrl($this->extractImageUrl($firstItem['productImages'] ?? [])),
            'previewUrl' => url()->current(),
            'redirectUrl' => $this->frontendBaseUrl() . '/cart?shared_token=' . urlencode($token),
            'ctaLabel' => 'Open shared cart',
        ]);
    }

    private function frontendBaseUrl(): string
    {
        $configured = trim((string) env('FRONTEND_URL', ''));
        if ($configured !== '') {
            return rtrim($configured, '/');
        }

        return rtrim((string) config('app.url'), '/');
    }

    private function extractImageUrl($images): ?string
    {
        if (!is_array($images) || empty($images)) {
            return null;
        }

        foreach ($images as $image) {
            if (is_string($image) && trim($image) !== '') {
                return trim($image);
            }

            if (is_array($image)) {
                $url = trim((string) ($image['imageUrl'] ?? $image['url'] ?? ''));
                if ($url !== '') {
                    return $url;
                }
            }
        }

        return null;
    }

    private function makeAbsoluteUrl(?string $value): ?string
    {
        $url = trim((string) $value);
        if ($url === '') {
            return null;
        }

        if (preg_match('/^https?:\/\//i', $url)) {
            return $url;
        }

        if (str_starts_with($url, '/')) {
            return rtrim($this->frontendBaseUrl(), '/') . $url;
        }

        return rtrim($this->frontendBaseUrl(), '/') . '/' . ltrim($url, '/');
    }

    private function publicCartShareCacheKey(string $token): string
    {
        return 'share:cart:public:' . $token;
    }
}