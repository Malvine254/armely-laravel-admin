<?php

namespace App\Services;

use App\Models\MarketingCampaign;
use App\Models\MarketingCampaignDecision;
use App\Models\MarketingEmailHistory;
use App\Models\Product;
use App\Models\SuppressionEvent;
use App\Models\User;
use App\Models\UserProductView;
use App\Support\OfferPricing;
use Illuminate\Support\Facades\DB;

class MarketingDecisionEngine
{
    public function __construct(private CustomerIntelligenceService $intelligence) {}

    public function evaluate(User $user): ?MarketingCampaignDecision
    {
        if (! config('ai_marketing.enabled')
            || ! config('ai_marketing.customer_intelligence_enabled')) {
            return null;
        }

        $campaign = MarketingCampaign::query()
            ->where('type', 'personalized_recommendation')
            ->where('status', 'active')
            ->first();
        if (! $campaign) {
            return null;
        }

        $key = 'personalized_recommendation:'.(int) $user->id.':'.now()->toDateString();
        $existing = MarketingCampaignDecision::query()->where('idempotency_key', $key)->first();
        if ($existing) {
            return $existing;
        }

        return DB::transaction(function () use ($campaign, $user, $key): MarketingCampaignDecision {
            $existing = MarketingCampaignDecision::query()
                ->where('idempotency_key', $key)
                ->lockForUpdate()
                ->first();
            if ($existing) {
                return $existing;
            }

            [$status, $reason, $productIds] = $this->decisionFor($user);

            return MarketingCampaignDecision::query()->firstOrCreate(
                ['idempotency_key' => $key],
                [
                    'user_id' => (int) $user->id,
                    'campaign_id' => (int) $campaign->id,
                    'campaign_type' => (string) $campaign->type,
                    'status' => $status,
                    'reason' => $reason,
                    'product_ids' => $productIds,
                    'model_metadata' => ['mode' => 'deterministic'],
                    'decided_at' => now(),
                ]
            );
        });
    }

    private function decisionFor(User $user): array
    {
        if (strtolower((string) $user->status) !== 'active'
            || in_array((string) $user->role, ['admin', 'super_admin'], true)
            || ! $user->email_verified_at
            || ! filter_var((string) $user->email, FILTER_VALIDATE_EMAIL)) {
            return ['suppressed', 'Customer account or email is not eligible.', []];
        }

        $preference = $user->emailPreference;
        if (! $preference || ! $preference->marketing_enabled
            || ! $preference->ai_marketing_enabled
            || ! $preference->ai_marketing_consent_at
            || ! $preference->browse_reminders_enabled) {
            return ['suppressed', 'Marketing permission, AI marketing consent, or browse preference is disabled.', []];
        }

        $deliverySuppressed = SuppressionEvent::query()
            ->where(function ($query) use ($user): void {
                $query->where('user_id', (int) $user->id)
                    ->orWhere('email', strtolower((string) $user->email));
            })
            ->whereIn('event_type', ['hard_bounce', 'complaint'])
            ->exists();
        if ($deliverySuppressed) {
            return ['suppressed', 'Customer has a hard-bounce or complaint suppression.', []];
        }

        $unsubscribedAfterConsent = SuppressionEvent::query()
            ->where('user_id', (int) $user->id)
            ->where('event_type', 'unsubscribe')
            ->where('occurred_at', '>=', $preference->ai_marketing_consent_at)
            ->exists();
        if ($unsubscribedAfterConsent) {
            return ['suppressed', 'Customer unsubscribed after granting AI marketing consent.', []];
        }

        $now = now();
        $history = MarketingEmailHistory::query()
            ->where('user_id', (int) $user->id)
            ->whereIn('status', ['sent', 'delivered'])
            ->where('sent_at', '>=', $now->copy()->subDays(30));
        $activeReservations = MarketingEmailHistory::query()
            ->where('user_id', (int) $user->id)
            ->where('status', 'sending')
            ->where('created_at', '>=', $now->copy()->subHours(2));
        $legacySends = SuppressionEvent::query()
            ->where('user_id', (int) $user->id)
            ->where('event_type', 'marketing_sent');
        $sent7Days = (clone $history)->where('sent_at', '>=', $now->copy()->subDays(7))->count()
            + (clone $activeReservations)->count()
            + (clone $legacySends)->where('occurred_at', '>=', $now->copy()->subDays(7))->count();
        $sent30Days = (clone $history)->count() + (clone $activeReservations)->count()
            + (clone $legacySends)->where('occurred_at', '>=', $now->copy()->subDays(30))->count();
        if ($sent7Days >= max(1, (int) config('ai_marketing.frequency.max_per_7_days', 1))
            || $sent30Days >= max(1, (int) config('ai_marketing.frequency.max_per_30_days', 4))) {
            return ['suppressed', 'Marketing frequency cap reached.', []];
        }

        $category = UserProductView::query()
            ->join('products', 'products.id', '=', 'user_product_views.product_id')
            ->where('user_product_views.user_id', (int) $user->id)
            ->where('user_product_views.viewed_at', '>=', $now->copy()->subDays(7))
            ->whereNotNull('products.category_id')
            ->select('products.category_id', DB::raw('COUNT(*) as view_count'))
            ->groupBy('products.category_id')
            ->orderByDesc('view_count')
            ->first();

        if (! $category || (int) $category->view_count < 3) {
            return ['skipped', 'No category has at least three recent product views.', []];
        }

        $products = Product::query()
            ->where('category_id', (int) $category->category_id)
            ->where('is_available', true)
            ->where('is_discontinued', false)
            ->where('quantity', '>', 0)
            ->whereNotNull('base_price')
            ->where('base_price', '>', 0)
            ->whereNotIn('id', UserProductView::query()
                ->where('user_id', (int) $user->id)
                ->where('viewed_at', '>=', $now->copy()->subDays(30))
                ->select('product_id'))
            ->orderByDesc('is_storefront_curated')
            ->limit(20)
            ->get()
            ->filter(static fn (Product $product): bool => OfferPricing::sellPrice($product) > 0)
            ->take(3)
            ->pluck('id')
            ->map(static fn ($id): int => (int) $id)
            ->values()
            ->all();

        if ($products === []) {
            return ['skipped', 'No in-stock catalog products have a valid current price.', []];
        }

        $this->intelligence->refresh($user);

        return [
            'dry_run',
            config('ai_marketing.auto_send_enabled')
                ? 'Eligible recommendation found, but sending is blocked because the delivery workflow is not implemented.'
                : 'Eligible recommendation identified in dry-run mode; automatic sending is disabled.',
            $products,
        ];
    }
}
