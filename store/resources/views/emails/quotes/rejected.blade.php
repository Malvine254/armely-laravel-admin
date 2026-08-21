@extends('emails.layouts.modern', ['emailTitle' => 'Quote Requires Revision', 'emailBadge' => 'Quote Update', 'emailAccent' => '#dc2626'])
@section('content')
<p style="margin:0 0 14px;font-size:16px;">Hello <strong>{{ $customer->name }}</strong>,</p>
<p style="margin:0 0 16px;color:#475569;">We could not approve this quote in its current form. Our team can help revise the products, quantities, or sourcing options.</p>
@include('emails.partials.details', ['rows' => ['Quote ID' => e($quote->quote_id), 'Reason' => e($reason ?? 'Please contact our sales team for details')]])
@include('emails.partials.button', ['url' => rtrim(\App\Support\FrontendUrl::base(), '/') . '/quotes', 'label' => 'View Quote'])
@endsection
@section('footer-note')Reply to this email or contact sales to discuss alternatives.@endsection
