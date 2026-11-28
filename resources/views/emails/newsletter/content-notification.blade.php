@php($recipientReason = trim((string) ($recipientReason ?? '')))
@php($recipientKind = trim((string) ($recipientKind ?? 'subscriber')))
@extends('emails.layouts.modern', ['emailTitle' => $title, 'emailBadge' => 'Armely ' . ($type === 'event' ? 'Events' : 'Insights'), 'emailAccent' => '#2f5597'])
@section('content')
<p style="margin:0 0 18px;color:#334155;font-size:15px;line-height:1.75">{{ $summary ?: 'A new Armely update is available.' }}</p>
@include('emails.partials.button', ['buttonUrl' => $url, 'buttonLabel' => $type === 'event' ? 'View Event' : 'Read Article'])
@endsection
@section('footer')
@if($recipientKind === 'admin')
{{ $recipientReason !== '' ? $recipientReason : 'You are receiving this because you are on the Armely admin team.' }}
@else
{{ $recipientReason !== '' ? $recipientReason : 'You are receiving this because you subscribed to Armely newsletter updates for blogs, events, and related resources.' }}
@endif
@if(!empty($unsubscribeUrl))
<br>No longer want Armely updates? <a href="{{ $unsubscribeUrl }}" style="color:#2F5597;font-weight:700">Unsubscribe</a>.
<br><span style="color:#64748b;font-size:12px">Direct link: <span style="word-break:break-all">{{ $unsubscribeUrl }}</span></span>
@endif
@endsection
