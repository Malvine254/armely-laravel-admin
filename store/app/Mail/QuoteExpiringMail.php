<?php

namespace App\Mail;

use App\Models\Quote;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class QuoteExpiringMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Quote $quote)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Your Quote Is Expiring Soon: {$this->quote->quote_id}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.quotes.expiring',
            with: [
                'quote' => $this->quote,
                'customer' => $this->quote->user,
            ],
        );
    }
}
