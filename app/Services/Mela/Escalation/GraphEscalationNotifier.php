<?php

namespace App\Services\Mela\Escalation;

use App\Services\AzureMailService;
use App\Services\NewsletterNotificationService;

/**
 * Sends through the existing Armely Graph mail workflow to the standard admin recipient list.
 */
class GraphEscalationNotifier implements EscalationNotifier
{
    public function __construct(
        private readonly AzureMailService $mail,
        private readonly NewsletterNotificationService $recipients,
    ) {
    }

    public function notifyTeam(string $subject, string $html): int
    {
        $from = AzureMailService::outboundFromEmail();
        $delivered = 0;

        foreach ($this->recipients->adminRecipientEmails() as $recipient) {
            if ($this->sendWithRetry($from, $recipient, $subject, $html)) {
                $delivered++;
            }
        }

        return $delivered;
    }

    public function notifyVisitor(string $email, string $subject, string $html): bool
    {
        return $this->sendWithRetry(AzureMailService::outboundFromEmail(), $email, $subject, $html);
    }

    private function sendWithRetry(string $from, string $to, string $subject, string $html): bool
    {
        for ($attempt = 1; $attempt <= 2; $attempt++) {
            if ($this->mail->sendEmail($from, $to, $subject, $html)) {
                return true;
            }
            if ($attempt === 1) {
                usleep(700_000);
            }
        }

        return false;
    }
}
