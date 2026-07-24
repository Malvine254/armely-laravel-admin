<!doctype html>
<html lang="en">
<body style="margin:0;background:#f1f5f9;font-family:Arial,sans-serif;color:#26364a">
<div style="max-width:620px;margin:0 auto;padding:32px 20px">
    <div style="background:#2f5597;color:#fff;padding:26px 30px">
        <div style="font-size:12px;text-transform:uppercase;letter-spacing:1.2px;color:#dbeafe;font-weight:700">Armely Events</div>
        <h1 style="font-size:24px;margin:10px 0 0">Thank you for joining us</h1>
    </div>
    <div style="background:#fff;padding:30px;line-height:1.65">
        <p>Hi {{ $name }},</p>
        <p>Thank you for your interest and participation in <strong>{{ $eventTitle }}</strong>.</p>
        <p>We appreciate the time you spent with the Armely team and hope the session provided practical insights you can use in your organization.</p>
        <p>If you have follow-up questions, simply reply to this email and our team will be happy to help.</p>
        <p style="margin-bottom:0">Best regards,<br>The Armely Events Team</p>
    </div>
    <p style="margin:14px 0;text-align:center;color:#64748b;font-size:12px">You are receiving this because you registered for an Armely event. <a href="{{ $unsubscribeUrl }}" style="color:#475569">Unsubscribe from event emails</a>.</p>
</div>
</body>
</html>
