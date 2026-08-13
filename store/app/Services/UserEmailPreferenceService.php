<?php

namespace App\Services;

use App\Models\EmailPreference;
use App\Models\SuppressionEvent;
use App\Models\UnsubscribeToken;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class UserEmailPreferenceService
{
    public function ensurePreference(User $user): EmailPreference
    {
        return EmailPreference::query()->firstOrCreate(
            ['user_id' => (int) $user->id],
            [
                'transactional_enabled' => true,
                'marketing_enabled' => true,
                'price_alerts_enabled' => true,
                'cart_reminders_enabled' => true,
                'browse_reminders_enabled' => true,
                'timezone' => null,
                'quiet_hours_start' => null,
                'quiet_hours_end' => null,
            ]
        );
    }

    public function shouldSendPriceAlert(User $user, ?Carbon $when = null): bool
    {
        $pref = $this->ensurePreference($user);
        if (!$pref->marketing_enabled || !$pref->price_alerts_enabled) {
            return false;
        }

        return !$this->isQuietHours($pref, $when ?: now());
    }

    public function shouldSendReminder(User $user, string $triggerType, ?Carbon $when = null): bool
    {
        $pref = $this->ensurePreference($user);

        if (!$pref->marketing_enabled) {
            return false;
        }

        if ($triggerType === 'abandoned_cart' && !$pref->cart_reminders_enabled) {
            return false;
        }

        if (in_array($triggerType, ['viewed_product', 'favorite_product'], true) && !$pref->browse_reminders_enabled) {
            return false;
        }

        return !$this->isQuietHours($pref, $when ?: now());
    }

    public function underDailySendCap(User $user, string $campaign, int $dailyCap, ?Carbon $when = null): bool
    {
        $now = $when ?: now();
        $start = $now->copy()->startOfDay();

        $sentCount = SuppressionEvent::query()
            ->where('user_id', (int) $user->id)
            ->where('event_type', 'marketing_sent')
            ->where('reason', $this->campaignReason($campaign))
            ->where('occurred_at', '>=', $start)
            ->count();

        return $sentCount < max(1, $dailyCap);
    }

    public function markMarketingSent(User $user, string $campaign, array $metadata = []): void
    {
        SuppressionEvent::query()->create([
            'user_id' => (int) $user->id,
            'email' => (string) ($user->email ?? ''),
            'event_type' => 'marketing_sent',
            'channel' => 'email',
            'reason' => $this->campaignReason($campaign),
            'source' => 'lifecycle_job',
            'metadata' => $metadata,
            'occurred_at' => now(),
        ]);
    }

    public function wasIdempotencyKeySent(string $key): bool
    {
        return SuppressionEvent::query()
            ->where('event_type', 'marketing_idempotency')
            ->where('reason', $this->idempotencyReason($key))
            ->exists();
    }

    public function markIdempotencyKeySent(User $user, string $key, array $metadata = []): void
    {
        SuppressionEvent::query()->create([
            'user_id' => (int) $user->id,
            'email' => (string) ($user->email ?? ''),
            'event_type' => 'marketing_idempotency',
            'channel' => 'email',
            'reason' => $this->idempotencyReason($key),
            'source' => 'lifecycle_job',
            'metadata' => $metadata,
            'occurred_at' => now(),
        ]);
    }

    public function unsubscribeUrl(User $user, string $scope): string
    {
        $token = $this->issueToken($user, $scope);

        return rtrim(config('app.url', ''), '/') . '/api/v1/behavior/unsubscribe/' . urlencode($token);
    }

    public function applyUnsubscribeToken(string $token, ?string $ipAddress = null): ?array
    {
        $plain = trim($token);
        if ($plain === '') {
            return null;
        }

        $record = UnsubscribeToken::query()
            ->where('token_hash', hash('sha256', $plain))
            ->with('user')
            ->first();

        if (!$record || !$record->user) {
            return null;
        }

        if ($record->expires_at && $record->expires_at->isPast()) {
            return null;
        }

        $scope = (string) ($record->scope ?? 'marketing');
        $user = $record->user;
        $preference = $this->ensurePreference($user);

        $updates = ['marketing_enabled' => false];

        if ($scope === 'price_alerts') {
            $updates['price_alerts_enabled'] = false;
        }

        if ($scope === 'cart_reminders') {
            $updates['cart_reminders_enabled'] = false;
        }

        if ($scope === 'browse_reminders') {
            $updates['browse_reminders_enabled'] = false;
        }

        $preference->fill($updates);
        $preference->save();

        SuppressionEvent::query()->create([
            'user_id' => (int) $user->id,
            'email' => (string) ($user->email ?? ''),
            'event_type' => 'unsubscribe',
            'channel' => 'email',
            'reason' => 'one_click_unsubscribe',
            'source' => 'lifecycle_email',
            'metadata' => [
                'scope' => $scope,
                'token_id' => (int) $record->id,
            ],
            'occurred_at' => now(),
        ]);

        $record->update([
            'used_at' => now(),
            'last_used_ip' => $ipAddress,
        ]);

        return [
            'scope' => $scope,
            'user_id' => (int) $user->id,
            'email' => (string) ($user->email ?? ''),
            'preferences' => [
                'marketing_enabled' => (bool) $preference->marketing_enabled,
                'price_alerts_enabled' => (bool) $preference->price_alerts_enabled,
                'cart_reminders_enabled' => (bool) $preference->cart_reminders_enabled,
                'browse_reminders_enabled' => (bool) $preference->browse_reminders_enabled,
            ],
        ];
    }

    private function issueToken(User $user, string $scope): string
    {
        $plain = Str::random(48);

        UnsubscribeToken::query()->create([
            'user_id' => (int) $user->id,
            'email' => (string) ($user->email ?? ''),
            'token_hash' => hash('sha256', $plain),
            'scope' => $this->normalizeScope($scope),
            'expires_at' => now()->addDays(180),
            'metadata' => [
                'issued_at' => now()->toISOString(),
            ],
        ]);

        return $plain;
    }

    private function normalizeScope(string $scope): string
    {
        $value = trim(strtolower($scope));

        return in_array($value, ['marketing', 'price_alerts', 'cart_reminders', 'browse_reminders'], true)
            ? $value
            : 'marketing';
    }

    private function campaignReason(string $campaign): string
    {
        return 'campaign:' . substr(preg_replace('/[^a-z0-9_\-]/i', '_', strtolower($campaign)) ?: 'unknown', 0, 52);
    }

    private function idempotencyReason(string $key): string
    {
        return 'idem:' . hash('sha256', $key);
    }

    private function isQuietHours(EmailPreference $preference, Carbon $when): bool
    {
        if ($preference->quiet_hours_start === null || $preference->quiet_hours_end === null) {
            return false;
        }

        $tz = (string) ($preference->timezone ?: config('app.timezone', 'UTC'));
        if (!in_array($tz, timezone_identifiers_list(), true)) {
            $tz = config('app.timezone', 'UTC');
        }

        $local = $when->copy()->setTimezone($tz);
        $currentMinute = ((int) $local->format('G')) * 60 + (int) $local->format('i');

        $start = max(0, min(1439, (int) $preference->quiet_hours_start));
        $end = max(0, min(1439, (int) $preference->quiet_hours_end));

        if ($start === $end) {
            return false;
        }

        if ($start < $end) {
            return $currentMinute >= $start && $currentMinute < $end;
        }

        return $currentMinute >= $start || $currentMinute < $end;
    }
}
