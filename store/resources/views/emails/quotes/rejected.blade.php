@component('mail::message')
# Quote Rejected

Hello {{ $customer->name }},

Unfortunately, your quote request has been rejected.

**Quote Details:**
- Quote ID: {{ $quote->quote_id }}
- Rejection Reason: {{ $reason ?? 'Not specified' }}

Please contact our sales team for more information or to discuss alternative options.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
