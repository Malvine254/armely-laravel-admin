<!doctype html>
<html lang="en">
<body style="margin:0;background:#f1f5f9;font-family:Arial,sans-serif;color:#26364a">
<div style="max-width:620px;margin:0 auto;padding:32px 20px">
    <div style="background:#0d233a;color:#fff;padding:26px 30px">
        <div style="font-size:12px;text-transform:uppercase;letter-spacing:1.2px;color:#7dd3fc;font-weight:700">Armely Events</div>
        <h1 style="font-size:24px;margin:10px 0 0">Your event is coming up</h1>
    </div>
    <div style="background:#fff;padding:30px;line-height:1.65">
        <p>Hi {{ $name }},</p>
        <p>This is a friendly reminder about <strong>{{ $eventTitle }}</strong>.</p>
        @if($eventDate)
            <p><strong>Date and time:</strong> {{ $eventDate }}</p>
        @endif
        @if($eventUrl)
            <p style="margin:26px 0"><a href="{{ $eventUrl }}" style="display:inline-block;padding:13px 22px;background:#0d233a;color:#fff;text-decoration:none;border-radius:5px;font-weight:700">Open Event Link</a></p>
        @endif
        <p>We look forward to seeing you.</p>
        <p style="margin-bottom:0">Best regards,<br>The Armely Events Team</p>
    </div>
    <p style="margin:14px 0;text-align:center;color:#64748b;font-size:12px">You are receiving this because you registered for an Armely event. <a href="{{ $unsubscribeUrl }}" style="color:#475569">Unsubscribe from event emails</a>.</p>
</div>
</body>
</html>
