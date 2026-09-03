<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PriceSyncStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param string              $jobName   e.g. "Live Price Refresh" or "Nightly Price Sync"
     * @param string              $phase     "started" | "completed" | "failed"
     * @param array<string,mixed> $stats     Metrics to display in the email body
     */
    public function __construct(
        public readonly string $jobName,
        public readonly string $phase,
        public readonly array  $stats = [],
    ) {}

    public function envelope(): Envelope
    {
        $phaseLabel = \Illuminate\Support\Str::headline($this->phase);
        $emoji = match ($this->phase) {
            'started'   => '🔄',
            'completed' => '✅',
            'completed_with_errors' => '⚠️',
            'failed'    => '❌',
            default     => '📋',
        };

        return new Envelope(
            subject: "[Armely Store] {$emoji} {$this->jobName}: {$phaseLabel}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.sync.price-sync-status',
            with: [
                'jobName' => $this->jobName,
                'phase'   => $this->phase,
                'stats'   => $this->stats,
                'time'    => now()->format('Y-m-d H:i:s T'),
            ],
        );
    }
}
