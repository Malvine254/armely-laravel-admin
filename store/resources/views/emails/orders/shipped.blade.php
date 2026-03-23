@component('mail::message')
# Order Shipped

Hello {{ $customer->name }},

Great news! Your order has shipped and is on its way to you.

**Order Details:**
- Order Number: {{ $order->order_number }}
- Shipped Date: {{ $order->shipped_at->format('M d, Y H:i A') }}

@component('mail::button', ['url' => config('app.url') . '/orders/' . $order->id])
Track Shipment
@endcomponent

Use the tracking information in your account to monitor your package's progress.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
