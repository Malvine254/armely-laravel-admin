@php
    $breakdown = is_array($invoice->raw_data['invoice_charge_breakdown'] ?? null) ? $invoice->raw_data['invoice_charge_breakdown'] : [];
    $shippingAmount = (float) ($breakdown['shipping_amount'] ?? 0);
@endphp
@extends('emails.layouts.modern', ['emailTitle' => 'Invoice Balance Reminder', 'emailBadge' => 'Billing Reminder', 'emailAccent' => '#f59e0b'])
@section('content')
<p style="margin:0 0 14px;font-size:16px;">Hello <strong>{{ $customer->name ?? 'Customer' }}</strong>,</p>
<p style="margin:0 0 16px;color:#475569;">Invoice <strong>{{ $invoice->invoice_number }}</strong> has an outstanding recorded balance.</p>
@include('emails.partials.details', ['rows' => ['Invoice' => e($invoice->invoice_number), 'Order' => e($invoice->order_number ?? 'N/A'), 'Invoice total' => '$'.number_format($invoice->total_amount ?? 0, 2), 'Paid' => '$'.number_format($invoice->paid_amount ?? 0, 2), 'Balance due' => '<span style="color:#b45309;font-size:18px">$'.number_format($balanceDue ?? 0, 2).'</span>', 'Due date' => optional($invoice->due_at)->format('M d, Y') ?? 'Pending delivery']])
@include('emails.partials.button', ['url' => rtrim(\App\Support\FrontendUrl::base(), '/') . '/invoices', 'label' => 'Review Invoice'])
@endsection
@section('footer-note')If payment was already provided, your account administrator will update the record. No repeated action is required.@endsection
