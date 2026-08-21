@extends('emails.layouts.modern', ['emailTitle' => 'Order Confirmed', 'emailBadge' => 'Order Update', 'emailAccent' => '#16a34a'])
@section('content')
<p style="margin:0 0 14px;font-size:16px;">Hello <strong>{{ $customer->name }}</strong>,</p>
<p style="margin:0 0 16px;color:#475569;">Your order is confirmed and has moved to our fulfillment team.</p>
@include('emails.partials.details', ['rows' => ['Order number' => e($order->order_number), 'Total' => '$'.number_format($order->total_amount, 2), 'Items' => count($order->items ?? []), 'Order date' => optional($order->ordered_at)->format('M d, Y g:i A') ?? 'Pending']])
@include('emails.partials.button', ['url' => rtrim(\App\Support\FrontendUrl::base(), '/') . '/orders', 'label' => 'View Order'])
@endsection
@section('footer-note')We will send another update when tracking information becomes available.@endsection
