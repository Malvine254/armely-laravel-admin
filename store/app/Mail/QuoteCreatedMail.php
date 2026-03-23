<?php

namespace App\Mail;

use App\Models\Quote;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class QuoteCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Quote $quote)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "New Quote Request: {$this->quote->quote_id}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.quotes.created',
            with: [
                'quote' => $this->quote,
                'customer' => $this->quote->user,
                'company' => $this->quote->user->company,
            ],
        );
    }
}
