<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Newsletter Subscriber</title>
</head>
<body style="margin:0; padding:0; background:#f4f7fb; font-family:Arial, Helvetica, sans-serif; color:#17233c;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f4f7fb; padding:28px 14px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:620px; background:#ffffff; border-radius:12px; overflow:hidden; border:1px solid #dbe5f4;">
                    <tr>
                        <td style="background:#2f5597; padding:24px 30px; color:#ffffff;">
                            <div style="font-size:13px; font-weight:700; letter-spacing:.08em; text-transform:uppercase; opacity:.9;">Newsletter Signup</div>
                            <h1 style="margin:8px 0 0; font-size:24px; line-height:1.25;">{{ $statusLabel }}</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:30px;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse;">
                                @if(!empty($name))
                                <tr>
                                    <td style="padding:12px 0; border-bottom:1px solid #e7edf6; color:#667085; width:150px;">Name</td>
                                    <td style="padding:12px 0; border-bottom:1px solid #e7edf6; font-weight:700;">{{ $name }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <td style="padding:12px 0; border-bottom:1px solid #e7edf6; color:#667085; width:150px;">Email</td>
                                    <td style="padding:12px 0; border-bottom:1px solid #e7edf6; font-weight:700;">
                                        <a href="mailto:{{ $email }}" style="color:#2f5597;">{{ $email }}</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:12px 0; border-bottom:1px solid #e7edf6; color:#667085; width:150px;">Submitted</td>
                                    <td style="padding:12px 0; border-bottom:1px solid #e7edf6; font-weight:700;">
                                        {{ $subscribedAt->format('M d, Y g:i A') }}
                                    </td>
                                </tr>
                            </table>
                            <p style="margin:20px 0 0; font-size:13px; line-height:1.6; color:#667085;">
                                This subscriber is now active in the admin Newsletter tab.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
