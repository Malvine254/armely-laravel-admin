<?php

namespace App\Console\Commands;

use App\Services\AzureGraphMailService;
use Illuminate\Console\Command;

class TestPriceAvailabilityStatusEmailCommand extends Command
{
    protected $signature = 'tdsynnex:test-priceavailability-email
                            {--to=malvine.owuor@armely.com : Recipient used through SYNC_STATUS_EMAIL config fallback}';

    protected $description = 'Send a test PriceAvailability sync status email';

    public function handle(AzureGraphMailService $mailer): int
    {
        $recipient = trim((string) $this->option('to'));
        if ($recipient !== '') {
            config(['mail.sync_status_email' => $recipient]);
            putenv("SYNC_STATUS_EMAIL={$recipient}");
            $_ENV['SYNC_STATUS_EMAIL'] = $recipient;
            $_SERVER['SYNC_STATUS_EMAIL'] = $recipient;
        }

        $sent = $mailer->sendSyncStatusEmail('PriceAvailability 6 PM Kenya Sync', 'completed', [
            'Test Email' => 'Yes',
            'Recipient' => $recipient ?: 'malvine.owuor@armely.com',
            'Scheduled Time' => '18:00 Africa/Nairobi',
            'Purpose' => 'Daily TD SYNNEX price and availability refresh',
            'Sent At' => now()->format('Y-m-d H:i:s T'),
        ]);

        if (!$sent) {
            $this->error('Test email was not sent. Azure Graph mail is not configured or delivery failed.');

            return self::FAILURE;
        }

        $this->info('Test PriceAvailability status email sent to ' . ($recipient ?: 'malvine.owuor@armely.com'));

        return self::SUCCESS;
    }
}
