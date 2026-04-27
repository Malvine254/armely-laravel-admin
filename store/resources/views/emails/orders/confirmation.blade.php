@component('mail::message')
# Order Confirmed

Hello {{ $customer->name }},

Thank you for your order! Your order has been confirmed and submitted to our fulfillment team.

**Order Details:**
- Order Number: {{ $order->order_number }}
- Total Amount: ${{ number_format($order->total_amount, 2) }}
- Items: {{ count($order->items ?? []) }}
- Order Date: {{ $order->ordered_at->format('M d, Y H:i A') }}

@component('mail::button', ['url' => config('app.url') . '/orders/' . $order->id])
Track Order
@endcomponent

You will receive tracking information once your order has shipped.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
