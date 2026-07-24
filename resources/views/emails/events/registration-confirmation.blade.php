<!doctype html>
<html lang="en">
<body style="margin:0;background:#f1f5f9;font-family:Arial,sans-serif;color:#26364a">
<div style="max-width:620px;margin:0 auto;padding:32px 20px">
    <div style="background:#0d233a;color:#fff;padding:26px 30px">
        <div style="font-size:12px;text-transform:uppercase;letter-spacing:1.2px;color:#7dd3fc;font-weight:700">Armely Events</div>
        <h1 style="font-size:24px;margin:10px 0 0">Request received</h1>
    </div>
    <div style="background:#fff;padding:30px;line-height:1.65">
        <p>Hi {{ $full_name }},</p>
        <p>Thank you for requesting an invitation to <strong>{{ $event_name ?? 'Sovereign Data Clouds with Snowflake' }}</strong>.</p>
        <p>To ensure a curated room of peers and protect the interactive quality of the briefing, our team reviews all requests within 24–48 hours.</p>
        <p>If approved, you will receive a direct calendar invitation containing your private Microsoft Teams access link alongside an executive pre-read.</p>
        <p style="margin-bottom:0">Best regards,<br>The Armely Events Team</p>
    </div>
    <p style="margin:14px 0;text-align:center;color:#64748b;font-size:12px">You are receiving this because you requested an event invitation. <a href="{{ $unsubscribe_url }}" style="color:#475569">Unsubscribe from event emails</a>.</p>
</div>
</body>
</html>
