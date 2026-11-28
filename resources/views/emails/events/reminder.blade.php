@extends('emails.layouts.modern', ['emailTitle' => 'Your event is coming up', 'emailBadge' => 'Armely Events', 'emailAccent' => '#0d233a'])
@section('content')
<p style="margin:0 0 14px;font-size:16px">Hi {{ $name }},</p>
<p style="margin:0 0 14px">This is a friendly reminder about <strong>{{ $eventTitle }}</strong>.</p>
@if($eventDate)
    <p style="margin:0 0 14px"><strong>Date and time:</strong> {{ $eventDate }}</p>
@endif
@if($eventUrl)
    @include('emails.partials.button', ['buttonUrl' => $eventUrl, 'buttonLabel' => 'Open Event Link'])
@endif
<p style="margin:0 0 14px">We look forward to seeing you.</p>
<p style="margin:0">Best regards,<br>The Armely Events Team</p>
@endsection
@section('footer')You are receiving this because you registered for an Armely event. <a href="{{ $unsubscribeUrl }}" style="color:#2F5597">Unsubscribe from event emails</a>.@endsection
