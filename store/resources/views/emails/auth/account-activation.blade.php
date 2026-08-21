@extends('emails.layouts.modern', ['emailTitle' => 'Activate Your Account', 'emailBadge' => 'Account Security', 'emailAccent' => '#2563eb'])
@section('content')
<p style="margin:0 0 14px;font-size:16px;">Hello <strong>{{ $user->name }}</strong>,</p>
<p style="margin:0 0 14px;color:#475569;">Welcome to Armely Store. Confirm your email address to activate your account and begin requesting quotes.</p>
<div style="padding:14px 16px;background:#eff6ff;border-left:4px solid #2563eb;border-radius:8px;color:#1e3a8a;">This secure activation link expires in <strong>{{ $expiresIn }} hours</strong>.</div>
@include('emails.partials.button', ['url' => $activationUrl, 'label' => 'Activate Account'])
@endsection
@section('footer-note')If you did not create this account, no action is required.@endsection
