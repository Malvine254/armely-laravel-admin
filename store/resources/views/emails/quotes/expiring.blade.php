@extends('emails.layouts.modern', ['emailTitle' => 'Quote Expiring Soon', 'emailBadge' => 'Action Recommended', 'emailAccent' => '#f59e0b'])
@section('content')
<p style="margin:0 0 14px;font-size:16px;">Hello <strong>{{ $customer->name }}</strong>,</p>
<p style="margin:0 0 16px;color:#475569;">Your approved pricing expires on <strong>{{ $quote->expires_at->format('M d, Y') }}</strong>. Review it before supplier pricing is refreshed.</p>
@include('emails.partials.details', ['rows' => ['Quote ID' => e($quote->quote_id), 'Amount' => '$'.number_format($quote->total_amount, 2), 'Items' => count($quote->items ?? [])]])
@include('emails.partials.button', ['url' => rtrim(\App\Support\FrontendUrl::base(), '/') . '/quotes', 'label' => 'Review Quote'])
@endsection
@section('footer-note')Contact the sales team if you need revised quantities, products, or validity dates.@endsection
