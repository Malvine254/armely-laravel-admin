<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Armely Resource</title>
</head>
<body style="margin:0; padding:0; background:#f4f8ff; font-family:Arial, Helvetica, sans-serif; color:#153869;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f4f8ff; padding:24px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:640px; background:#ffffff; border-radius:16px; overflow:hidden; border:1px solid #d8e4fb;">
                    <tr>
                        <td style="background:linear-gradient(135deg, #0f274f 0%, #2f5597 100%); padding:28px 32px; color:#ffffff;">
                            <div style="font-size:12px; letter-spacing:0.12em; text-transform:uppercase; font-weight:700; opacity:0.9; margin-bottom:10px;">Armely Resources</div>
                            <div style="font-size:28px; line-height:1.2; font-weight:800;">Your requested resource is ready</div>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:28px 32px;">
                            <p style="margin:0 0 16px; font-size:16px; line-height:1.7;">Hi {{ $name }},</p>
                            <p style="margin:0 0 16px; font-size:16px; line-height:1.7;">Thanks for requesting <strong>{{ $resource->title }}</strong>. Use the secure download button below to get the actual file directly.</p>
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin:0 0 20px; border:1px solid #dce7fb; border-radius:12px; background:#f8fbff;">
                                <tr>
                                    <td style="padding:18px 20px;">
                                        <div style="font-size:12px; text-transform:uppercase; letter-spacing:0.08em; color:#56709c; font-weight:700; margin-bottom:8px;">Resource</div>
                                        <div style="font-size:20px; line-height:1.4; color:#153869; font-weight:800; margin-bottom:8px;">{{ $resource->title }}</div>
                                        <div style="font-size:14px; line-height:1.6; color:#4b6187;">{{ ucfirst($resource->resource_type) }}@if($resource->category) | {{ $resource->category }}@endif</div>
                                    </td>
                                </tr>
                            </table>
                            <table role="presentation" cellspacing="0" cellpadding="0" style="margin:0 0 14px;">
                                <tr>
                                    <td>
                                        <a href="{{ $downloadUrl }}" style="display:inline-block; background:#2f5597; color:#ffffff; text-decoration:none; padding:13px 18px; border-radius:10px; font-size:15px; font-weight:700;">Download File Now</a>
                                    </td>
                                </tr>
                            </table>
                            <table role="presentation" cellspacing="0" cellpadding="0" style="margin:0 0 20px;">
                                <tr>
                                    <td>
                                        <a href="{{ $resourceUrl }}" style="display:inline-block; background:#edf3ff; color:#1f4583; text-decoration:none; padding:13px 18px; border-radius:10px; border:1px solid #cddcf8; font-size:15px; font-weight:700;">View Resource Page</a>
                                    </td>
                                </tr>
                            </table>
                            <p style="margin:0 0 6px; font-size:14px; line-height:1.7; color:#60779d;">This secure download link expires in <strong>1 hour</strong>.</p>
                            <p style="margin:0; font-size:14px; line-height:1.7; color:#60779d;">If you have questions about this resource or want related material, reply to this email and the Armely team can help.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>