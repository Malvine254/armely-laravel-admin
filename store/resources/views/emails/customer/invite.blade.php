@extends('emails.layouts.modern', ['emailTitle' => 'Your Store Account Is Ready', 'emailBadge' => 'Customer Invitation', 'emailAccent' => '#2563eb'])
@section('content')
<p style="margin:0 0 14px;font-size:16px;">Hello <strong>{{ $customer->name }}</strong>,</p>
<p style="margin:0 0 16px;color:#475569;">An Armely Store account has been prepared for you. Use these temporary credentials to sign in.</p>
@include('emails.partials.details', ['rows' => array_filter(['Email' => e($customer->email), 'Temporary password' => '<span style="font-family:Consolas,monospace">'.e($plainPassword).'</span>', 'Company' => $customer->company ? e($customer->company->name) : null])])
<div style="padding:13px 15px;background:#fff7ed;border:1px solid #fed7aa;border-radius:9px;color:#9a3412;font-size:13px;">The temporary password expires in <strong>48 hours</strong>. You will create a private password after signing in.</div>
@include('emails.partials.button', ['url' => rtrim(\App\Support\FrontendUrl::base(), '/') . '/login', 'label' => 'Sign In'])
@endsection
@section('footer-note')If you did not expect this invitation, contact Armely support.@endsection
