<?php

namespace App\Jobs;

use App\Models\Order;
use App\Models\SuppressionEvent;
use App\Services\AzureGraphMailService;
use App\Services\UserEmailPreferenceService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class ProcessOrderJourneyRemindersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;
    public int $timeout = 300;

    public function middleware(): array
    {
        return [(new WithoutOverlapping('process-order-journey-reminders'))->expireAfter(600)];
    }

    public function handle(AzureGraphMailService $mailer, UserEmailPreferenceService $preferences): void
    {
        $now = now();
        $stages = [3, 7, 12];

        Order::query()
            ->whereIn('status', ['shipped', 'in_transit'])
            ->whereNotNull('user_id')
            ->with('user:id,name,email,status')
            ->orderBy('id')
            ->chunkById(100, function ($orders) use ($mailer, $preferences, $now, $stages): void {
                foreach ($orders as $order) {
                    $user = $order->user;
                    if (!$user
                        || strtolower((string) ($user->status ?? 'active')) !== 'active'
                        || trim((string) ($user->email ?? '')) === ''
                        || !$preferences->shouldSendOrderJourneyUpdate($user, $now)) {
                        continue;
                    }

                    $anchor = $order->shipped_at ?: $order->ordered_at ?: $order->created_at;
                    if (!$anchor) {
                        continue;
                    }

                    $daysInTransit = max(0, Carbon::parse($anchor)->startOfDay()->diffInDays($now->copy()->startOfDay()));
                    $stage = collect($stages)->filter(fn (int $day): bool => $daysInTransit >= $day)->last();
                    if (!$stage) {
                        continue;
                    }

                    $reason = 'order_journey:' . $order->id . ':day_' . $stage;
                    if (SuppressionEvent::query()->where('event_type', 'transactional_sent')->where('reason', $reason)->exists()) {
                        continue;
                    }

                    // Never send more than one reassurance email for this order
                    // in a 72-hour period, even if a delayed scheduler catches up.
                    if (SuppressionEvent::query()
                        ->where('user_id', (int) $user->id)
                        ->where('event_type', 'transactional_sent')
                        ->where('reason', 'like', 'order_journey:' . $order->id . ':%')
                        ->where('occurred_at', '>', $now->copy()->subHours(72))
                        ->exists()) {
                        continue;
                    }

                    if (!$mailer->sendOrderJourneyReminderEmail($order, $daysInTransit)) {
                        continue;
                    }

                    SuppressionEvent::query()->create([
                        'user_id' => (int) $user->id,
                        'email' => (string) $user->email,
                        'event_type' => 'transactional_sent',
                        'channel' => 'email',
                        'reason' => $reason,
                        'source' => 'order_journey_job',
                        'metadata' => ['order_id' => (int) $order->id, 'days_in_transit' => $daysInTransit],
                        'occurred_at' => $now,
                    ]);
                }
            });
    }
}
