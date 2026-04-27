<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activate Account</title>
</head>
<body style="margin:0;padding:24px;background:#f8fafc;font-family:Segoe UI,Arial,sans-serif;color:#1f2937;">
    <div style="max-width:640px;margin:0 auto;background:#ffffff;border:1px solid #e2e8f0;border-radius:12px;padding:24px;">
        <h2 style="margin-top:0;color:#2F5597;">Activate your account</h2>
        <p>Hello {{ $user->name }},</p>
        <p>Thank you for registering with Armely Store. Please activate your account to continue.</p>
        <p style="margin:24px 0;">
            <a href="{{ $activationUrl }}" style="background:#2F5597;color:#ffffff;text-decoration:none;padding:10px 16px;border-radius:6px;display:inline-block;">Activate account</a>
        </p>
        <p>This activation link expires in {{ $expiresIn }} hours.</p>
        <p>If you did not request this, you can safely ignore this email.</p>
    </div>
</body>
</html>
