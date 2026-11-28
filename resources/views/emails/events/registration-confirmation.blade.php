@extends('emails.layouts.modern', ['emailTitle' => 'Request received', 'emailBadge' => 'Armely Events', 'emailAccent' => '#0d233a'])
@section('content')
<p style="margin:0 0 14px;font-size:16px">Hi {{ $full_name }},</p>
<p style="margin:0 0 14px">Thank you for requesting an invitation to <strong>{{ $event_name ?? 'Sovereign Data Clouds with Snowflake' }}</strong>.</p>
<p style="margin:0 0 14px">To ensure a curated room of peers and protect the interactive quality of the briefing, our team reviews all requests within 24–48 hours.</p>
<p style="margin:0 0 14px">If approved, you will receive a direct calendar invitation containing your private Microsoft Teams access link alongside an executive pre-read.</p>
<p style="margin:0">Best regards,<br>The Armely Events Team</p>
@endsection
@section('footer')You are receiving this because you requested an event invitation. <a href="{{ $unsubscribe_url }}" style="color:#2F5597">Unsubscribe from event emails</a>.@endsection
