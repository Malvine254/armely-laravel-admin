<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
</head>
<body style="margin:0;padding:0;background:#eef2fa;font-family:'Segoe UI',Arial,sans-serif;color:#172033;">
@php($recipientReason = trim((string) ($recipientReason ?? '')))
@php($recipientKind = trim((string) ($recipientKind ?? 'subscriber')))
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#eef2fa;padding:30px 14px;">
    <tr>
        <td align="center">
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:680px;background:#ffffff;border:1px solid #d6e1f7;border-radius:18px;overflow:hidden;">
                <tr>
                    <td style="background:#2f5597;padding:30px;">
                        <p style="margin:0 0 10px;color:#dbeafe;font-size:12px;letter-spacing:1.4px;text-transform:uppercase;font-weight:800;">Armely {{ $type === 'event' ? 'Events' : 'Insights' }}</p>
                        <h1 style="margin:0;color:#ffffff;font-size:25px;line-height:1.25;font-weight:800;">{{ $title }}</h1>
                    </td>
                </tr>
                <tr>
                    <td style="padding:28px 30px 8px;">
                        <p style="margin:0 0 16px;color:#334155;font-size:15px;line-height:1.75;">{{ $summary ?: 'A new Armely update is available.' }}</p>
                        <a href="{{ $url }}" style="display:inline-block;background:#2f5597;color:#ffffff;text-decoration:none;padding:12px 20px;font-size:14px;font-weight:800;border-radius:0;">{{ $type === 'event' ? 'View Event' : 'Read Article' }}</a>
                    </td>
                </tr>
                <tr>
                    <td style="padding:20px 30px 28px;">
                        @if($recipientKind === 'admin')
                        <p style="margin:0;color:#66758c;font-size:13px;line-height:1.6;">{{ $recipientReason !== '' ? $recipientReason : 'You are receiving this because you are on the Armely admin team.' }}</p>
                        @else
                        <p style="margin:0;color:#66758c;font-size:13px;line-height:1.6;">{{ $recipientReason !== '' ? $recipientReason : 'You are receiving this because you subscribed to Armely newsletter updates for blogs, events, and related resources.' }}</p>
                        @endif
                        @if(!empty($unsubscribeUrl))
                        <div style="margin:16px 0 0;padding:16px 18px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;">
                            <p style="margin:0 0 12px;color:#66758c;font-size:13px;line-height:1.6;">No longer want Armely updates?</p>
                            <a href="{{ $unsubscribeUrl }}" style="display:inline-block;background:#ffffff;color:#2f5597;border:1px solid #2f5597;text-decoration:none;padding:10px 16px;font-size:13px;font-weight:800;border-radius:0;">Unsubscribe</a>
                            <p style="margin:12px 0 0;color:#66758c;font-size:12px;line-height:1.5;">Direct link: <a href="{{ $unsubscribeUrl }}" style="color:#2f5597;text-decoration:underline;">{{ $unsubscribeUrl }}</a></p>
                        </div>
                        @endif
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
