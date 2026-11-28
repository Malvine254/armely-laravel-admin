@extends('emails.layouts.modern', ['emailTitle' => 'Your registration is verified', 'emailBadge' => 'Armely Events', 'emailAccent' => '#0d233a'])
@section('content')
<p style="margin:0 0 14px;font-size:16px">Hi {{ $name }},</p>
<p style="margin:0 0 14px">Your registration for <strong>{{ $eventTitle }}</strong> has been verified.</p>
@if($eventDate)<p style="margin:0 0 14px"><strong>Event date:</strong> {{ $eventDate }}</p>@endif
@include('emails.partials.button', ['buttonUrl' => $eventUrl, 'buttonLabel' => 'Open Event Link'])
<p style="margin:0 0 14px">This access link is intended for you. Please keep it private.</p>
<p style="margin:0">Best regards,<br>The Armely Events Team</p>
@endsection
@section('footer')You are receiving this because you registered for an Armely event. <a href="{{ $unsubscribeUrl }}" style="color:#2F5597">Unsubscribe from event emails</a>.@endsection
