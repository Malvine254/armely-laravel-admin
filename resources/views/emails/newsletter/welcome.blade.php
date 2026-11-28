@extends('emails.layouts.modern', ['emailTitle' => 'You are subscribed', 'emailBadge' => 'Armely Updates', 'emailAccent' => '#2f5597'])
@section('content')
<p style="margin:0 0 16px;font-size:16px">Hi {{ $name ?: 'there' }},</p>
<p style="margin:0 0 16px">Thanks for subscribing to Armely updates. We will send new blog articles, events, and practical Microsoft platform insights as they are published.</p>
<div style="margin:22px 0;padding:16px 18px;background:#eef4ff;border-left:4px solid #2F5597;border-radius:8px">
    <p style="margin:0;font-size:15px;line-height:1.55;color:#233a66">Subscribed email: <strong>{{ $email }}</strong></p>
</div>
@include('emails.partials.button', ['buttonUrl' => 'https://armely.com/blog', 'buttonLabel' => 'Read the latest insights'])
@endsection
@section('footer')
Changed your mind? You can stop newsletter emails at any time:
<a href="{{ $unsubscribeUrl }}" style="color:#2F5597;font-weight:700">Unsubscribe</a>.
<br><span style="color:#64748b;font-size:12px">If the link does not work, copy this address: <span style="word-break:break-all">{{ $unsubscribeUrl }}</span></span>
@endsection
