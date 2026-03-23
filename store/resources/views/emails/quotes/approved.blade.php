<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Quote Approved</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; color: #111827; background: #f8fafc;">
    <div style="max-width: 640px; margin: 0 auto; padding: 24px;">
        <div style="background: #ffffff; border: 1px solid #d9e6f7; border-radius: 12px; padding: 24px;">
            <h2 style="margin: 0 0 12px; color: #2F5597;">Quote Approved</h2>
            <p style="margin: 0 0 16px;">Hello {{ $customer->name ?? 'Customer' }},</p>
            <p style="margin: 0 0 16px;">
                Good news! Your quote has been approved and is ready to be converted into an order.
            </p>

            <div style="border: 1px solid #d9e6f7; background: #edf3fb; border-radius: 10px; padding: 14px; margin-bottom: 16px;">
                <p style="margin: 0 0 8px;"><strong>Quote ID:</strong> {{ $quote->quote_id }}</p>
                <p style="margin: 0 0 8px;"><strong>Total Amount:</strong> ${{ number_format($quote->total_amount, 2) }}</p>
                <p style="margin: 0 0 8px;"><strong>Items:</strong> {{ count($quote->items ?? []) }}</p>
                <p style="margin: 0;"><strong>Valid Until:</strong> {{ $quote->expires_at?->format('M d, Y') ?? 'N/A' }}</p>
            </div>

            <p style="margin: 0 0 16px;">
                Ready to place an order? Simply log in to your account and convert this quote to an order.
            </p>

            <a
                href="{{ config('app.url') }}/quotes/{{ $quote->id }}"
                style="display: inline-block; padding: 10px 18px; background: #2F5597; color: #ffffff; text-decoration: none; border-radius: 8px;"
            >
                View Quote
            </a>

            <p style="margin: 20px 0 0; color: #64748b; font-size: 12px;">
                Thanks,<br>{{ config('app.name') }}
            </p>
        </div>
    </div>
</body>
</html>
