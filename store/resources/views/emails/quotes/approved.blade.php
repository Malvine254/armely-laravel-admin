@extends('emails.layouts.modern', ['emailTitle' => 'Quote Approved', 'emailBadge' => 'Quote Update', 'emailAccent' => '#16a34a'])
@section('content')
<p style="margin:0 0 14px;font-size:16px;">Hello <strong>{{ $customer->name ?? 'Customer' }}</strong>,</p>
<p style="margin:0 0 16px;color:#475569;">Your quote has been reviewed and approved. The confirmed pricing is ready for your records.</p>
@include('emails.partials.details', ['rows' => ['Quote ID' => e($quote->quote_id), 'Approved amount' => '$'.number_format($quote->total_amount, 2), 'Items' => count($quote->items ?? []), 'Valid until' => optional($quote->expires_at)->format('M d, Y') ?? 'N/A']])
@include('emails.partials.button', ['url' => rtrim(\App\Support\FrontendUrl::base(), '/') . '/quotes', 'label' => 'View Quote'])
@endsection
@section('footer-note')Your approved quote, linked order, and invoice will remain available in your account.@endsection
