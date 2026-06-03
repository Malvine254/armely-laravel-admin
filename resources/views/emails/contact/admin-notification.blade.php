@php
    $detailRows = [
        'Name' => trim((string) ($name ?? '')),
        'Email' => trim((string) ($email ?? '')),
        'Organization' => trim((string) ($organization ?? '')),
        'Phone' => trim((string) ($phone ?? '')),
        'Subject' => trim((string) ($subject ?? '')),
    ];

    $detailRows = array_filter($detailRows, fn ($value) => $value !== '');
    $messageText = trim((string) ($message ?? ''));
@endphp
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Contact Submission</title>
</head>
<body style="margin:0;padding:0;background:#eef2fa;font-family:'Segoe UI',Arial,sans-serif;color:#172033;">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#eef2fa;padding:30px 14px;">
    <tr>
        <td align="center">
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:720px;background:#ffffff;border:1px solid #d6e1f7;border-radius:18px;overflow:hidden;box-shadow:0 18px 44px rgba(28,54,93,0.12);">
                <tr>
                    <td style="background:#2f5597;padding:30px;">
                        <p style="margin:0 0 10px;color:#dbeafe;font-size:12px;letter-spacing:1.4px;text-transform:uppercase;font-weight:800;">Armely Website Inquiry</p>
                        <h1 style="margin:0;color:#ffffff;font-size:25px;line-height:1.25;font-weight:800;">New contact form submission</h1>
                        <p style="margin:12px 0 0;color:#edf4ff;font-size:14px;line-height:1.6;">A visitor submitted the contact form. The details below include only fields they completed.</p>
                    </td>
                </tr>
                <tr>
                    <td style="padding:28px 30px 10px;">
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:separate;border-spacing:0 10px;">
                            @foreach($detailRows as $label => $value)
                                <tr>
                                    <td style="width:180px;padding:12px 14px;background:#f6f9ff;border:1px solid #dde8ff;color:#2f5597;font-size:13px;font-weight:800;vertical-align:top;">{{ e($label) }}</td>
                                    <td style="padding:12px 14px;background:#ffffff;border:1px solid #e4ebfb;color:#1f3458;font-size:14px;line-height:1.55;vertical-align:top;">{{ e($value) }}</td>
                                </tr>
                            @endforeach
                        </table>
                    </td>
                </tr>
                @if($messageText !== '')
                    <tr>
                        <td style="padding:6px 30px 30px;">
                            <div style="border:1px solid #dbe6f3;background:#f8fbff;padding:18px;">
                                <p style="margin:0 0 10px;color:#2f5597;font-size:13px;font-weight:800;text-transform:uppercase;letter-spacing:.6px;">Message</p>
                                <div style="margin:0;color:#172033;font-size:15px;line-height:1.75;">{!! nl2br(e($messageText)) !!}</div>
                            </div>
                        </td>
                    </tr>
                @endif
                <tr>
                    <td style="padding:18px 30px;background:#f7faff;border-top:1px solid #e5edff;">
                        <p style="margin:0;color:#60779c;font-size:13px;line-height:1.6;">This automated notification was sent from the Armely website contact form.</p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
