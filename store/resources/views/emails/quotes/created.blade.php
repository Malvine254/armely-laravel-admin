@component('mail::message')
# New Quote Request

Hello Admin,

A new quote request has been submitted and requires your review.

**Quote Details:**
- Quote ID: {{ $quote->quote_id }}
- Customer: {{ $customer->name }}
- Company: {{ $company->name }}
- Total Amount: ${{ number_format($quote->total_amount, 2) }}
- Items: {{ count($quote->items ?? []) }}
- Created: {{ $quote->created_at->format('M d, Y H:i A') }}

@component('mail::button', ['url' => config('app.url') . '/admin/quotes/' . $quote->id])
Review Quote
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
