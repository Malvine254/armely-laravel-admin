<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Armely Website AI Enquiry</title>
</head>
<body style="margin:0;padding:0;background:#eef2fa;font-family:'Segoe UI',Arial,sans-serif;color:#1e2f4d;">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#eef2fa;padding:28px 14px;">
    <tr>
        <td align="center">
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:680px;background:#ffffff;border-radius:18px;overflow:hidden;border:1px solid #d6e1f7;">
                <tr>
                    <td style="background:linear-gradient(135deg,#153462 0%,#2f5597 100%);padding:24px 30px;">
                        <p style="margin:0;color:#9cc8ff;font-size:12px;letter-spacing:1.2px;text-transform:uppercase;font-weight:700;">Source: Armely Website AI Assistant (Mela AI)</p>
                        <h1 style="margin:10px 0 0;color:#ffffff;font-size:22px;line-height:1.25;">A visitor requested follow-up</h1>
                    </td>
                </tr>
                <tr>
                    <td style="padding:24px 30px;">
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="font-size:14px;color:#233f71;">
                            <tr><td style="padding:6px 0;width:170px;color:#6b7fa2;">Name</td><td style="padding:6px 0;font-weight:600;">{{ $name ?: 'Not provided' }}</td></tr>
                            <tr><td style="padding:6px 0;color:#6b7fa2;">Email</td><td style="padding:6px 0;font-weight:600;">{{ $email ?: 'Not provided' }}</td></tr>
                            <tr><td style="padding:6px 0;color:#6b7fa2;">Company</td><td style="padding:6px 0;">{{ $company ?: 'Not provided' }}</td></tr>
                            <tr><td style="padding:6px 0;color:#6b7fa2;">Phone</td><td style="padding:6px 0;">{{ $phone ?: 'Not provided' }}</td></tr>
                            <tr><td style="padding:6px 0;color:#6b7fa2;">Preferred contact</td><td style="padding:6px 0;">{{ $preferredContact ?: 'Not specified' }}</td></tr>
                            <tr><td style="padding:6px 0;color:#6b7fa2;">Request type</td><td style="padding:6px 0;">{{ $requestType }}</td></tr>
                            <tr><td style="padding:6px 0;color:#6b7fa2;">Area of interest</td><td style="padding:6px 0;font-weight:600;">{{ $topic }}</td></tr>
                            <tr><td style="padding:6px 0;color:#6b7fa2;">Reference</td><td style="padding:6px 0;">{{ $reference }}</td></tr>
                            <tr><td style="padding:6px 0;color:#6b7fa2;">Conversation ID</td><td style="padding:6px 0;font-family:Consolas,monospace;font-size:12px;">{{ $conversationId }}</td></tr>
                            <tr><td style="padding:6px 0;color:#6b7fa2;">Timestamp</td><td style="padding:6px 0;">{{ $timestamp }}</td></tr>
                            @if($landingPage)
                            <tr><td style="padding:6px 0;color:#6b7fa2;">Started on page</td><td style="padding:6px 0;">{{ $landingPage }}</td></tr>
                            @endif
                        </table>

                        <p style="margin:20px 0 0;font-size:13px;color:#6b7fa2;text-transform:uppercase;letter-spacing:.5px;font-weight:700;">Business need</p>
                        <p style="margin:6px 0 0;font-size:14px;line-height:1.6;color:#324a73;white-space:pre-line;">{{ $businessNeed }}</p>

                        <p style="margin:18px 0 0;font-size:13px;color:#6b7fa2;text-transform:uppercase;letter-spacing:.5px;font-weight:700;">Requested next action</p>
                        <p style="margin:6px 0 0;font-size:14px;line-height:1.6;color:#324a73;white-space:pre-line;">{{ $requestedAction }}</p>

                        @if($summary)
                        <p style="margin:18px 0 0;font-size:13px;color:#6b7fa2;text-transform:uppercase;letter-spacing:.5px;font-weight:700;">Conversation summary</p>
                        <p style="margin:6px 0 0;font-size:14px;line-height:1.6;color:#324a73;white-space:pre-line;">{{ $summary }}</p>
                        @endif

                        @if(!empty($technologies) || !empty($services))
                        <p style="margin:18px 0 0;font-size:13px;color:#6b7fa2;text-transform:uppercase;letter-spacing:.5px;font-weight:700;">Context</p>
                        <p style="margin:6px 0 0;font-size:14px;line-height:1.6;color:#324a73;">
                            @if(!empty($technologies))Technologies: {{ implode(', ', $technologies) }}<br>@endif
                            @if(!empty($services))Armely services discussed: {{ implode(', ', $services) }}@endif
                        </p>
                        @endif

                        @if(!empty($transcript))
                        <p style="margin:22px 0 8px;font-size:13px;color:#6b7fa2;text-transform:uppercase;letter-spacing:.5px;font-weight:700;">Recent conversation</p>
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="font-size:13px;line-height:1.55;">
                            @foreach($transcript as $line)
                            <tr>
                                <td style="padding:8px 10px;border-radius:8px;background:{{ $line['role'] === 'user' ? '#eef4ff' : '#f7f9fc' }};color:#26364a;white-space:pre-line;">
                                    <strong style="color:{{ $line['role'] === 'user' ? '#1f4d99' : '#667085' }};">{{ $line['role'] === 'user' ? 'Visitor' : 'Mela AI' }}:</strong> {{ $line['content'] }}
                                </td>
                            </tr>
                            <tr><td style="height:6px;"></td></tr>
                            @endforeach
                        </table>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td style="padding:16px 30px 22px;background:#f7faff;border-top:1px solid #e5edff;">
                        <p style="margin:0;color:#7b8fad;font-size:12px;line-height:1.5;">This enquiry was also saved to the consultation leads list. Reply directly to the visitor's email address to follow up.</p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
