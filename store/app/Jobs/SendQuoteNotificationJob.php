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

class SendQuoteNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $quoteId,
        public ?string $revisedFromQuoteId = null
    ) {
    }

    public function handle(NotificationService $notificationService): void
    {
        $quote = Quote::find($this->quoteId);

        if (!$quote) {
            Log::warning("Quote notification job skipped: quote {$this->quoteId} not found.");
            return;
        }

        if ($this->revisedFromQuoteId) {
            $notificationService->sendQuoteRevisionNotification($quote, $this->revisedFromQuoteId);
            return;
        }

        $notificationService->sendQuoteCreatedNotification($quote);
    }
}
