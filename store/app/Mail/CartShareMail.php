<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CartShareMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly string $senderName,
        public readonly string $shareUrl,
        public readonly int $itemCount,
        public readonly string $note = '',
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "{$this->senderName} shared a quote cart with you",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.shares.cart',
            with: [
                'senderName' => $this->senderName,
                'shareUrl'   => $this->shareUrl,
                'itemCount'  => $this->itemCount,
                'note'       => $this->note,
                'appName'    => config('app.name', 'Armely'),
            ],
        );
    }
}
