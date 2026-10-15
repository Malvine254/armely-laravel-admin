<?php

namespace App\Services\Mela\Escalation;

/**
 * Captures notifications instead of emailing them; used by tests and `mela:eval`.
 */
class RecordingEscalationNotifier implements EscalationNotifier
{
    public array $team = [];
    public array $visitor = [];
    public bool $failTeam = false;
    public int $recipientCount = 1;

    public function notifyTeam(string $subject, string $html): int
    {
        if ($this->failTeam) {
            return 0;
        }
        $this->team[] = ['subject' => $subject, 'html' => $html];

        return 1;
    }

    public function teamRecipientCount(): int
    {
        return $this->recipientCount;
    }

    public function notifyVisitor(string $email, string $subject, string $html): bool
    {
        $this->visitor[] = ['email' => $email, 'subject' => $subject];

        return true;
    }
}
