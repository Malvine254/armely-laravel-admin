<?php

namespace App\Services;

use App\Models\MarketingCustomerProfile;
use App\Models\Order;
use App\Models\SearchProfile;
use App\Models\User;
use App\Models\UserCartSnapshot;
use App\Models\UserProductView;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class CustomerIntelligenceService
{
    public function refresh(User $user): ?MarketingCustomerProfile
    {
        if (! config('ai_marketing.enabled')
            || ! config('ai_marketing.customer_intelligence_enabled')
            || ! $user->email_verified_at
            || in_array((string) $user->role, ['admin', 'super_admin'], true)
            || strtolower((string) $user->status) !== 'active'
            || ! $user->emailPreference?->marketing_enabled
            || ! $user->emailPreference?->ai_marketing_enabled
            || ! $user->emailPreference?->ai_marketing_consent_at) {
            return null;
        }

        $since = now()->subDays(30);
        $categoryActivity = UserProductView::query()
            ->join('products', 'products.id', '=', 'user_product_views.product_id')
            ->leftJoin('categories', 'categories.id', '=', 'products.category_id')
            ->where('user_product_views.user_id', (int) $user->id)
            ->where('user_product_views.viewed_at', '>=', $since)
            ->whereNotNull('products.category_id')
            ->select([
                'products.category_id',
                'categories.name as category_name',
                DB::raw('COUNT(*) as view_count'),
            ])
            ->groupBy('products.category_id', 'categories.name')
            ->orderByDesc('view_count')
            ->limit(5)
            ->get();

        $viewedProducts = UserProductView::query()
            ->where('user_id', (int) $user->id)
            ->where('viewed_at', '>=', $since)
            ->orderByDesc('viewed_at')
            ->limit(20)
            ->pluck('product_id')
            ->map(static fn ($id): int => (int) $id)
            ->unique()
            ->values()
            ->all();

        $cart = UserCartSnapshot::query()
            ->where('user_id', (int) $user->id)
            ->first();
        $cartProductIds = collect((array) ($cart?->items ?? []))
            ->map(static fn (array $item): int => (int) ($item['productId'] ?? 0))
            ->filter(static fn (int $id): bool => $id > 0)
            ->unique()
            ->values()
            ->all();

        $searchProfile = SearchProfile::query()
            ->where('user_id', (int) $user->id)
            ->orderByDesc('updated_at')
            ->first();
        $recentSearches = collect((array) ($searchProfile?->terms ?? []))
            ->filter(static fn (array $term): bool => ! empty($term['lastSearched'])
                && Carbon::parse($term['lastSearched'])->greaterThanOrEqualTo($since))
            ->map(static fn (array $term): array => [
                'count' => max(1, (int) ($term['count'] ?? 1)),
                'last_searched_at' => Carbon::parse((string) $term['lastSearched'])->toISOString(),
            ])
            ->take(5)
            ->values();

        $orders = Order::query()
            ->where('user_id', (int) $user->id)
            ->whereNotIn('status', ['cancelled', 'failed'])
            ->whereNotNull('ordered_at')
            ->where('ordered_at', '>=', $since)
            ->get(['total_amount', 'ordered_at']);

        $features = [
            'window_days' => 30,
            'viewed_categories' => $categoryActivity->map(static fn ($row): array => [
                'id' => $row->category_id ? (int) $row->category_id : null,
                'name' => (string) ($row->category_name ?? ''),
                'view_count' => (int) $row->view_count,
            ])->all(),
            'recent_product_ids' => $viewedProducts,
            'active_cart_product_ids' => $cartProductIds,
            'recent_search_term_count' => $recentSearches->count(),
            'recent_search_count' => (int) $recentSearches->sum('count'),
            'order_count' => $orders->count(),
            'average_order_value' => $orders->contains(static fn ($order): bool => is_numeric($order->total_amount))
                ? round((float) $orders->filter(static fn ($order): bool => is_numeric($order->total_amount))
                    ->avg(static fn ($order): float => (float) $order->total_amount), 2)
                : null,
        ];

        $lastActivity = UserProductView::query()
            ->where('user_id', (int) $user->id)
            ->max('viewed_at');
        foreach ([
            $cart?->last_synced_at,
            $orders->max('ordered_at'),
            $recentSearches->max('last_searched_at'),
        ] as $activityAt) {
            if ($activityAt && (! $lastActivity || Carbon::parse($activityAt)->greaterThan(Carbon::parse($lastActivity)))) {
                $lastActivity = $activityAt;
            }
        }
        $viewCount = (int) $categoryActivity->sum('view_count');
        $summary = $categoryActivity->isEmpty()
            ? 'No product category interest was observed in the last 30 days.'
            : 'Observed product views in the last 30 days: '.$categoryActivity
                ->map(static fn ($row): string => (string) $row->category_name.' ('.(int) $row->view_count.')')
                ->implode(', ').'.';
        if ($recentSearches->isNotEmpty()) {
            $summary .= ' Recent search activity was observed for '.$recentSearches->count().' distinct terms '
                .'('.(int) $recentSearches->sum('count').' searches).';
        }

        return MarketingCustomerProfile::query()->updateOrCreate(
            ['user_id' => (int) $user->id],
            [
                'features' => $features,
                'summary' => $summary,
                'confidence' => $viewCount > 0 ? min(1, $viewCount / 10) : null,
                'last_activity_at' => $lastActivity ? Carbon::parse($lastActivity) : null,
                'data_freshness_at' => now(),
            ]
        );
    }
}
