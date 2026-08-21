@extends('emails.layouts.modern', ['emailTitle' => 'New Quote Request', 'emailBadge' => 'Admin Review', 'emailAccent' => '#7c3aed'])
@section('content')
<p style="margin:0 0 16px;color:#475569;">A customer submitted a quote request that requires review.</p>
@include('emails.partials.details', ['rows' => ['Quote ID' => e($quote->quote_id), 'Customer' => e($customer->name), 'Company' => e($company->name), 'Requested total' => '$'.number_format($quote->total_amount, 2), 'Items' => count($quote->items ?? []), 'Submitted' => optional($quote->created_at)->format('M d, Y g:i A')]])
@include('emails.partials.button', ['url' => rtrim(config('app.url'), '/') . '/admin/quotes', 'label' => 'Review Quote'])
@endsection
@section('footer-note')Internal notification for the Armely sales and procurement team.@endsection
