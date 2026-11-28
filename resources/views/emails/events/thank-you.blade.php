@extends('emails.layouts.modern', ['emailTitle' => 'Thank you for joining us', 'emailBadge' => 'Armely Events', 'emailAccent' => '#2f5597'])
@section('content')
<p style="margin:0 0 14px;font-size:16px">Hi {{ $name }},</p>
<p style="margin:0 0 14px">Thank you for your interest and participation in <strong>{{ $eventTitle }}</strong>.</p>
<p style="margin:0 0 14px">We appreciate the time you spent with the Armely team and hope the session provided practical insights you can use in your organization.</p>
<p style="margin:0 0 14px">If you have follow-up questions, simply reply to this email and our team will be happy to help.</p>
<p style="margin:0">Best regards,<br>The Armely Events Team</p>
@endsection
@section('footer')You are receiving this because you registered for an Armely event. <a href="{{ $unsubscribeUrl }}" style="color:#2F5597">Unsubscribe from event emails</a>.@endsection
