<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Invoice Payment Reminder</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; color: #111827; background: #f8fafc;">
    <div style="max-width: 640px; margin: 0 auto; padding: 24px;">
        <div style="background: #ffffff; border: 1px solid #d9e6f7; border-radius: 12px; padding: 24px;">
            <h2 style="margin: 0 0 12px; color: #2F5597;">Invoice Payment Reminder</h2>
            <p style="margin: 0 0 16px;">Hello {{ $customer->name ?? 'Customer' }},</p>
            <p style="margin: 0 0 16px;">
                This is a reminder that invoice <strong>{{ $invoice->invoice_number }}</strong> has an outstanding balance.
            </p>

            <div style="border: 1px solid #d9e6f7; background: #edf3fb; border-radius: 10px; padding: 14px; margin-bottom: 16px;">
                <p style="margin: 0 0 8px;"><strong>Invoice:</strong> {{ $invoice->invoice_number }}</p>
                <p style="margin: 0 0 8px;"><strong>Order:</strong> {{ $invoice->order_number ?? 'N/A' }}</p>
                <p style="margin: 0 0 8px;"><strong>Total:</strong> ${{ number_format($invoice->total_amount ?? 0, 2) }}</p>
                <p style="margin: 0 0 8px;"><strong>Paid:</strong> ${{ number_format($invoice->paid_amount ?? 0, 2) }}</p>
                <p style="margin: 0;"><strong>Balance Due:</strong> ${{ number_format($balanceDue ?? 0, 2) }}</p>
            </div>

            <p style="margin: 0 0 16px;">
                <strong>Due Date:</strong> {{ $invoice->due_at?->format('M d, Y') ?? 'On Demand' }}
            </p>

            <a
                href="{{ config('app.url') }}/invoices"
                style="display: inline-block; padding: 10px 18px; background: #2F5597; color: #ffffff; text-decoration: none; border-radius: 8px;"
            >
                View & Pay Invoice
            </a>

            <p style="margin: 20px 0 0; color: #64748b; font-size: 12px;">
                If payment has already been made, please ignore this reminder.
            </p>
            <p style="margin: 8px 0 0; color: #64748b; font-size: 12px;">
                For questions about this invoice, contact us at {{ \App\Models\AppSetting::getValue('system.support_email', env('SUPPORT_EMAIL', 'info@armely.com')) }}.
            </p>
        </div>
    </div>
</body>
</html>
