@extends('emails.layouts.modern', ['emailTitle' => 'New Consultation Request', 'emailBadge' => 'Consultation Lead', 'emailAccent' => '#7c3aed'])
@section('content')
<p style="margin:0 0 14px;color:#475569">A visitor requested a consultation with Armely.</p>
@include('emails.partials.details', ['rows' => array_filter(['Name' => e($name), 'Email' => '<a href="mailto:'.e($email).'" style="color:#2563eb">'.e($email).'</a>', 'Organization' => e($organization ?: ''), 'Phone' => e($phone ?: ''), 'Service' => e($serviceType ?: 'Not specified')])])
<div style="padding:14px 16px;background:#fff;border:1px solid #dbe5f1;border-radius:10px"><p style="margin:0 0 5px;color:#64748b;font-size:11px;font-weight:700;text-transform:uppercase">Consultation goals</p><div>{!! nl2br(e($message)) !!}</div></div>
@endsection
@section('footer')Internal consultation lead notification from armely.com.@endsection
