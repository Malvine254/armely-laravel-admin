<?php

namespace App\Jobs;

use App\Models\Order;
use App\Models\Quote;
use App\Services\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CheckExpiringQuotesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(NotificationService $notificationService): void
    {
        try {
            $now = now();

            // Find quotes expiring within 3 days
            $expiringQuotes = Quote::whereNotIn('status', ['expired', 'converted', 'cancelled', 'rejected'])
                ->whereBetween('expires_at', [$now, $now->copy()->addDays(3)])
                ->get();

            foreach ($expiringQuotes as $quote) {
                if (!$quote instanceof Quote) {
                    continue;
                }
                $notificationService->sendQuoteExpiringNotification($quote);
                Log::info("Expiring quote notification sent for quote {$quote->quote_id}");
            }

            // Mark quotes as expired if past expiry date
            $expiredQuotesCount = Quote::whereNotIn('status', ['converted', 'cancelled', 'rejected', 'expired'])
                ->where('expires_at', '<', $now)
                ->update(['status' => 'expired']);

            // Auto-cancel local pending orders when payment was not completed in time.
            // We scope to locally generated order numbers (ORD-*) to avoid touching external distributor orders.
            $expiryHours = max(1, (int) config('app.order_payment_expiry_hours', 72));
            $cutoff = $now->copy()->subHours($expiryHours);

            $ordersToExpire = Order::query()
                ->where('status', 'pending')
                ->where('order_number', 'like', 'ORD-%')
                ->whereNull('cancelled_at')
                ->where(function ($query) use ($cutoff) {
                    $query->where('ordered_at', '<=', $cutoff)
                        ->orWhere(function ($inner) use ($cutoff) {
                            $inner->whereNull('ordered_at')
                                ->where('created_at', '<=', $cutoff);
                        });
                })
                ->where(function ($query) {
                    $query->whereNull('payment_status')
                        ->orWhereNotIn('payment_status', ['paid', 'completed']);
                })
                ->where(function ($query) {
                    $query->whereDoesntHave('invoice')
                        ->orWhereHas('invoice', function ($invoiceQuery) {
                            $invoiceQuery->where('status', '!=', 'paid');
                        });
                })
                ->get();

            $expiredOrdersCount = 0;

            foreach ($ordersToExpire as $order) {
                $raw = is_array($order->raw_data) ? $order->raw_data : [];
                $raw['auto_expired'] = [
                    'reason' => 'payment_not_completed',
                    'expired_at' => $now->toIso8601String(),
                    'expiry_hours' => $expiryHours,
                ];

                $order->update([
                    'status' => 'cancelled',
                    'cancellation_reason' => "Auto-cancelled: payment not completed within {$expiryHours} hours",
                    'cancelled_at' => $now,
                    'raw_data' => $raw,
                ]);

                $expiredOrdersCount++;
            }

            Log::info("Checked {$expiringQuotes->count()} expiring quotes; marked {$expiredQuotesCount} quotes expired; auto-cancelled {$expiredOrdersCount} unpaid pending local orders.");
        } catch (\Exception $e) {
            Log::error("Failed to check expiring quotes: " . $e->getMessage());
            throw $e;
        }
    }
}
