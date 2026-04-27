@component('mail::message')
# Quote Expiring Soon

Hello {{ $customer->name }},

Your quote will expire on **{{ $quote->expires_at->format('M d, Y') }}**. Don't miss out on this offer!

**Quote Details:**
- Quote ID: {{ $quote->quote_id }}
- Total Amount: ${{ number_format($quote->total_amount, 2) }}
- Items: {{ count($quote->items ?? []) }}

@component('mail::button', ['url' => config('app.url') . '/quotes/' . $quote->id])
View & Convert Quote
@endcomponent

If you have any questions, please contact our sales team.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
