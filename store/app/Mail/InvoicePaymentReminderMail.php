<?php

namespace App\Mail;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvoicePaymentReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Invoice $invoice)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Payment Reminder: Invoice {$this->invoice->invoice_number}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.invoices.reminder',
            with: [
                'invoice' => $this->invoice,
                'customer' => $this->invoice->user,
                'order' => $this->invoice->order,
                'balanceDue' => max(0, (float) ($this->invoice->total_amount ?? 0) - (float) ($this->invoice->paid_amount ?? 0)),
            ],
        );
    }
}
