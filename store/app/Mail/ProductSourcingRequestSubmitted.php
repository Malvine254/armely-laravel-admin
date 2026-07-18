<?php

namespace App\Mail;

use App\Models\ProductSourcingRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ProductSourcingRequestSubmitted extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public ProductSourcingRequest $sourcingRequest)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New procurement request #'.$this->sourcingRequest->id,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.product-sourcing-request-submitted',
        );
    }
}
