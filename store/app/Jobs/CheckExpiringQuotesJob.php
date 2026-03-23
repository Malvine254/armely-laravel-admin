<?php

namespace App\Jobs;

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
            // Find quotes expiring within 3 days
            $expiringQuotes = Quote::where('status', '!=', 'expired')
                ->where('status', '!=', 'converted')
                ->whereBetween('expires_at', [now(), now()->addDays(3)])
                ->get();

            foreach ($expiringQuotes as $quote) {
                $notificationService->sendQuoteExpiringNotification($quote);
                Log::info("Expiring quote notification sent for quote {$quote->quote_id}");
            }

            // Mark quotes as expired if past expiry date
            Quote::where('status', '!=', 'converted')
                ->where('expires_at', '<', now())
                ->update(['status' => 'expired']);

            Log::info("Checked " . $expiringQuotes->count() . " expiring quotes");
        } catch (\Exception $e) {
            Log::error("Failed to check expiring quotes: " . $e->getMessage());
            throw $e;
        }
    }
}
