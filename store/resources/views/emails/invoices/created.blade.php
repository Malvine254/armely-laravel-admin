@php
    $breakdown = is_array($invoice->raw_data['invoice_charge_breakdown'] ?? null) ? $invoice->raw_data['invoice_charge_breakdown'] : [];
    $shippingAmount = (float) ($breakdown['shipping_amount'] ?? 0);
    $taxAmount = (float) ($invoice->tax_amount ?? 0);
    $subtotalAmount = max(0, (float) ($invoice->total_amount ?? 0) - $taxAmount - $shippingAmount);
@endphp
@extends('emails.layouts.modern', ['emailTitle' => 'Invoice Issued', 'emailBadge' => 'Billing Record', 'emailAccent' => '#2563eb'])
@section('content')
<p style="margin:0 0 14px;font-size:16px;">Hello <strong>{{ $customer->name ?? 'Customer' }}</strong>,</p>
<p style="margin:0 0 16px;color:#475569;">Your invoice has been issued with the approved amount for this order.</p>
@include('emails.partials.details', ['rows' => ['Invoice' => e($invoice->invoice_number), 'Order' => e($invoice->order_number), 'Subtotal' => '$'.number_format($subtotalAmount, 2), 'Shipping' => '$'.number_format($shippingAmount, 2), 'Tax' => '$'.number_format($taxAmount, 2), 'Invoice total' => '<span style="color:#1d4ed8;font-size:18px">$'.number_format($invoice->total_amount ?? 0, 2).'</span>', 'Due date' => optional($invoice->due_at)->format('M d, Y') ?? 'Begins after delivery']])
@include('emails.partials.button', ['url' => rtrim(\App\Support\FrontendUrl::base(), '/') . '/invoices', 'label' => 'View Invoice'])
@endsection
@section('footer-note')Keep this invoice for your records. Contact accounting if any order or billing details need correction.@endsection
