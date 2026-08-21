@extends('emails.layouts.modern', ['emailTitle' => 'A Quote Cart Was Shared With You', 'emailBadge' => 'Shared Cart', 'emailAccent' => '#0ea5e9'])
@section('content')
<p style="margin:0 0 14px;font-size:16px;">Hello,</p>
<p style="margin:0 0 16px;color:#475569;"><strong>{{ $senderName }}</strong> shared a {{ $itemCount }}-item quote cart with you on {{ $appName }}.</p>
@if($note)<div style="margin:16px 0;padding:14px 16px;background:#f4f8ff;border-left:4px solid #2F5597;border-radius:8px;"><p style="margin:0 0 4px;color:#64748b;font-size:11px;text-transform:uppercase;font-weight:700;">Note from {{ $senderName }}</p><p style="margin:0;">{{ $note }}</p></div>@endif
@include('emails.partials.button', ['url' => $shareUrl, 'label' => 'Open Shared Cart'])
<p style="margin:16px 0 0;color:#64748b;font-size:12px;word-break:break-all;">Direct link: <a href="{{ $shareUrl }}" style="color:#2563eb;">{{ $shareUrl }}</a></p>
@endsection
@section('footer-note')Opening the link lets you review and import the products into your own quote cart.@endsection
