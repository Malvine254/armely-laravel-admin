<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Armely Newsletter</title>
</head>
<body style="margin:0; padding:0; background:#f4f7fb; font-family:Arial, Helvetica, sans-serif; color:#17233c;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f4f7fb; padding:28px 14px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:620px; background:#ffffff; border-radius:12px; overflow:hidden; border:1px solid #dbe5f4;">
                    <tr>
                        <td style="background:#2f5597; padding:26px 30px; color:#ffffff;">
                            <div style="font-size:13px; font-weight:700; letter-spacing:.08em; text-transform:uppercase; opacity:.9;">Armely Updates</div>
                            <h1 style="margin:8px 0 0; font-size:26px; line-height:1.25;">You are subscribed</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:30px;">
                            <p style="margin:0 0 16px; font-size:16px; line-height:1.65;">
                                Hi {{ $name ?: 'there' }},
                            </p>
                            <p style="margin:0 0 16px; font-size:16px; line-height:1.65;">
                                Thanks for subscribing to Armely updates. We will send new blog articles, events, and practical Microsoft platform insights as they are published.
                            </p>
                            <div style="margin:24px 0; padding:18px 20px; background:#eef4ff; border-left:4px solid #2f5597; border-radius:8px;">
                                <p style="margin:0; font-size:15px; line-height:1.55; color:#233a66;">
                                    Subscribed email: <strong>{{ $email }}</strong>
                                </p>
                            </div>
                            <div style="margin:24px 0 10px; padding:18px 20px; background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px;">
                                <p style="margin:0 0 12px; font-size:14px; line-height:1.6; color:#475569;">
                                    Changed your mind? You can stop newsletter emails at any time.
                                </p>
                                <a href="{{ $unsubscribeUrl }}" style="display:inline-block; background:#ffffff; color:#2f5597; border:1px solid #2f5597; text-decoration:none; padding:10px 16px; font-size:13px; font-weight:700; border-radius:0;">
                                    Unsubscribe
                                </a>
                            </div>
                            <p style="margin:0; font-size:13px; line-height:1.6; color:#667085;">
                                If the button does not work, use this link:
                                <a href="{{ $unsubscribeUrl }}" style="color:#2f5597; font-weight:700;">{{ $unsubscribeUrl }}</a>
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
