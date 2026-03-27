<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Resource Request</title>
</head>
<body style="margin:0;padding:0;background:#eef2fa;font-family:'Segoe UI',Arial,sans-serif;color:#1e2f4d;">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#eef2fa;padding:28px 14px;">
    <tr>
        <td align="center">
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:700px;background:#ffffff;border-radius:18px;overflow:hidden;border:1px solid #d6e1f7;">
                <tr>
                    <td style="background:linear-gradient(135deg,#153462 0%,#2f5597 100%);padding:28px 30px;">
                        <p style="margin:0;color:#9cc8ff;font-size:12px;letter-spacing:1.2px;text-transform:uppercase;font-weight:700;">Armely Lead Alerts</p>
                        <h1 style="margin:10px 0 0;color:#ffffff;font-size:24px;line-height:1.2;">New Resource Request Received</h1>
                    </td>
                </tr>
                <tr>
                    <td style="padding:26px 30px 8px;">
                        <p style="margin:0 0 12px;font-size:16px;line-height:1.6;">Hello Armely Team,</p>
                        <p style="margin:0 0 16px;font-size:14px;line-height:1.7;color:#334b74;">A new lead has requested gated resources from the Case Studies page. Please review the details below and follow up promptly.</p>
                    </td>
                </tr>
                <tr>
                    <td style="padding:0 30px 16px;">
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:separate;border-spacing:0 8px;">
                            <tr>
                                <td style="width:220px;padding:10px 12px;background:#f6f9ff;border:1px solid #dde8ff;border-radius:10px;color:#33507f;font-size:13px;font-weight:700;">Name</td>
                                <td style="padding:10px 12px;background:#ffffff;border:1px solid #e4ebfb;border-radius:10px;color:#1f3458;font-size:14px;">{{ e($name) }}</td>
                            </tr>
                            <tr>
                                <td style="padding:10px 12px;background:#f6f9ff;border:1px solid #dde8ff;border-radius:10px;color:#33507f;font-size:13px;font-weight:700;">Email</td>
                                <td style="padding:10px 12px;background:#ffffff;border:1px solid #e4ebfb;border-radius:10px;color:#1f3458;font-size:14px;">{{ e($email) }}</td>
                            </tr>
                            <tr>
                                <td style="padding:10px 12px;background:#f6f9ff;border:1px solid #dde8ff;border-radius:10px;color:#33507f;font-size:13px;font-weight:700;">Phone</td>
                                <td style="padding:10px 12px;background:#ffffff;border:1px solid #e4ebfb;border-radius:10px;color:#1f3458;font-size:14px;">{{ e($phone) }}</td>
                            </tr>
                            <tr>
                                <td style="padding:10px 12px;background:#f6f9ff;border:1px solid #dde8ff;border-radius:10px;color:#33507f;font-size:13px;font-weight:700;">Organization</td>
                                <td style="padding:10px 12px;background:#ffffff;border:1px solid #e4ebfb;border-radius:10px;color:#1f3458;font-size:14px;">{{ e($organization) }}</td>
                            </tr>
                            <tr>
                                <td style="padding:10px 12px;background:#f6f9ff;border:1px solid #dde8ff;border-radius:10px;color:#33507f;font-size:13px;font-weight:700;">Job Title</td>
                                <td style="padding:10px 12px;background:#ffffff;border:1px solid #e4ebfb;border-radius:10px;color:#1f3458;font-size:14px;">{{ e($jobTitle ?: 'N/A') }}</td>
                            </tr>
                            <tr>
                                <td style="padding:10px 12px;background:#f6f9ff;border:1px solid #dde8ff;border-radius:10px;color:#33507f;font-size:13px;font-weight:700;">Country/Region</td>
                                <td style="padding:10px 12px;background:#ffffff;border:1px solid #e4ebfb;border-radius:10px;color:#1f3458;font-size:14px;">{{ e($country ?: 'N/A') }}</td>
                            </tr>
                            <tr>
                                <td style="padding:10px 12px;background:#f6f9ff;border:1px solid #dde8ff;border-radius:10px;color:#33507f;font-size:13px;font-weight:700;">Interest</td>
                                <td style="padding:10px 12px;background:#ffffff;border:1px solid #e4ebfb;border-radius:10px;color:#1f3458;font-size:14px;">{{ e($interest) }}</td>
                            </tr>
                            <tr>
                                <td style="padding:10px 12px;background:#f6f9ff;border:1px solid #dde8ff;border-radius:10px;color:#33507f;font-size:13px;font-weight:700;">Requested Resource</td>
                                <td style="padding:10px 12px;background:#ffffff;border:1px solid #e4ebfb;border-radius:10px;color:#1f3458;font-size:14px;">{{ e($requestedResource ?: 'N/A') }}</td>
                            </tr>
                            <tr>
                                <td style="padding:10px 12px;background:#f6f9ff;border:1px solid #dde8ff;border-radius:10px;color:#33507f;font-size:13px;font-weight:700;">Requested Case Study ID</td>
                                <td style="padding:10px 12px;background:#ffffff;border:1px solid #e4ebfb;border-radius:10px;color:#1f3458;font-size:14px;">{{ e($caseStudyId ?: 'N/A') }}</td>
                            </tr>
                            <tr>
                                <td style="padding:10px 12px;background:#f6f9ff;border:1px solid #dde8ff;border-radius:10px;color:#33507f;font-size:13px;font-weight:700;">Requested White Paper ID</td>
                                <td style="padding:10px 12px;background:#ffffff;border:1px solid #e4ebfb;border-radius:10px;color:#1f3458;font-size:14px;">{{ e($whitePaperId ?: 'N/A') }}</td>
                            </tr>
                            <tr>
                                <td style="padding:10px 12px;background:#f6f9ff;border:1px solid #dde8ff;border-radius:10px;color:#33507f;font-size:13px;font-weight:700;">Secure Link Expires</td>
                                <td style="padding:10px 12px;background:#ffffff;border:1px solid #e4ebfb;border-radius:10px;color:#1f3458;font-size:14px;">{{ e($expiresAt ?: 'N/A') }}</td>
                            </tr>
                            <tr>
                                <td style="padding:10px 12px;background:#f6f9ff;border:1px solid #dde8ff;border-radius:10px;color:#33507f;font-size:13px;font-weight:700;">Lead Source</td>
                                <td style="padding:10px 12px;background:#ffffff;border:1px solid #e4ebfb;border-radius:10px;color:#1f3458;font-size:14px;">Case Studies Modal</td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td style="padding:4px 30px 24px;">
                        <div style="padding:12px 14px;background:#f8fbff;border:1px solid #e2edff;border-radius:10px;">
                            <p style="margin:0 0 8px;color:#34517d;font-size:13px;font-weight:700;">Additional Notes</p>
                            <p style="margin:0;color:#1f3458;font-size:14px;line-height:1.7;">{!! nl2br(e($message ?: 'N/A')) !!}</p>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td style="padding:16px 30px 24px;background:#f7faff;border-top:1px solid #e5edff;">
                        <p style="margin:0;font-size:13px;color:#60779c;line-height:1.6;">This is an automated notification from the Armely website.</p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
