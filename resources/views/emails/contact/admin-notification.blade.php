@php($rows = array_filter(['Name' => e($name ?? ''), 'Email' => '<a href="mailto:'.e($email ?? '').'" style="color:#2563eb">'.e($email ?? '').'</a>', 'Organization' => e($organization ?? ''), 'Phone' => e($phone ?? ''), 'Subject' => e($subject ?? '')]))
@extends('emails.layouts.modern', ['emailTitle' => 'New Contact Inquiry', 'emailBadge' => 'Lead Alert', 'emailAccent' => '#7c3aed'])
@section('content')
<p style="margin:0 0 14px;color:#475569;">A visitor submitted the Armely contact form.</p>
@include('emails.partials.details', ['rows' => $rows])
@if(trim((string)($message ?? '')) !== '')<div style="padding:14px 16px;background:#fff;border:1px solid #dbe5f1;border-radius:10px"><p style="margin:0 0 5px;color:#64748b;font-size:11px;font-weight:700;text-transform:uppercase">Message</p><div>{!! nl2br(e($message)) !!}</div></div>@endif
@endsection
@section('footer')Internal lead notification from the Armely website contact form.@endsection
