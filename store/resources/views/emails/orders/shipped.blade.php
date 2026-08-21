@extends('emails.layouts.modern', ['emailTitle' => 'Your Order Has Shipped', 'emailBadge' => 'Shipping Update', 'emailAccent' => '#0ea5e9'])
@section('content')
<p style="margin:0 0 14px;font-size:16px;">Hello <strong>{{ $customer->name }}</strong>,</p>
<p style="margin:0 0 16px;color:#475569;">Great news—your order is on its way.</p>
@include('emails.partials.details', ['rows' => ['Order number' => e($order->order_number), 'Shipped' => optional($order->shipped_at)->format('M d, Y g:i A') ?? 'Recently', 'Tracking number' => e(data_get($order->tracking_info, 'tracking_number', 'Available in your account'))]])
@include('emails.partials.button', ['url' => rtrim(\App\Support\FrontendUrl::base(), '/') . '/orders', 'label' => 'Track Shipment'])
@endsection
@section('footer-note')Carrier scans may take a few hours to appear after shipment.@endsection
