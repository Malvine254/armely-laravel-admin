<?php

namespace App\Services\Mela\Escalation;

interface EscalationNotifier
{
    /**
     * Notifies the Armely team. Returns the number of team recipients the message was delivered to.
     */
    public function notifyTeam(string $subject, string $html): int;

    public function notifyVisitor(string $email, string $subject, string $html): bool;
}
