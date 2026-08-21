@extends('emails.layouts.modern', ['emailTitle' => 'Your Account Is Approved', 'emailBadge' => 'Access Granted', 'emailAccent' => '#16a34a'])
@section('content')
<p style="margin:0 0 14px;font-size:16px;">Hello <strong>{{ $user->name }}</strong>,</p>
<p style="margin:0 0 16px;color:#475569;">Great news—your account has been reviewed and approved. You now have full access to quotes, orders, invoices, and Mela AI assistance.</p>
@include('emails.partials.details', ['rows' => array_filter(['Account' => e($user->email), 'Company' => $user->company ? e($user->company->name) : null])])
@include('emails.partials.button', ['url' => rtrim(\App\Support\FrontendUrl::base(), '/') . '/login', 'label' => 'Sign In'])
@endsection
@section('footer-note')Your procurement workspace is ready whenever you are.@endsection
