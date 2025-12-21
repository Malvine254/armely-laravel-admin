<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; background-color: #f4f7fa; margin: 0; padding: 20px; }
        .email-container { max-width: 600px; margin: 0 auto; background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1); }
        .email-header { background: linear-gradient(135deg, #2f5597 0%, #1e3a6d 100%); padding: 40px 30px; text-align: center; }
        .email-header .logo { width: 70px; height: 70px; background: rgba(255, 255, 255, 0.2); border-radius: 12px; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 15px; }
        .email-header .logo-text { font-size: 32px; color: white; font-weight: 700; letter-spacing: -1px; }
        .email-header h1 { color: white; margin: 0; font-size: 24px; font-weight: 600; }
        .email-body { padding: 40px 30px; }
        .email-body p { margin: 0 0 18px 0; font-size: 16px; }
        .button { display: inline-block; padding: 16px 36px; background: linear-gradient(135deg, #2f5597 0%, #1e3a6d 100%); color: white; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 16px; margin: 18px 0; box-shadow: 0 4px 15px rgba(47, 85, 151, 0.3); transition: all 0.3s ease; }
        .button:hover { box-shadow: 0 6px 20px rgba(47, 85, 151, 0.4); }
        .button-container { text-align: center; margin: 24px 0; }
        .email-footer { background: #f8fafc; padding: 30px; text-align: center; border-top: 1px solid #e8eef5; }
        .email-footer p { color: #666; font-size: 14px; margin: 5px 0; }
        .alternative-link { margin-top: 20px; padding-top: 20px; border-top: 1px solid #e8eef5; font-size: 13px; color: #666; }
        .alternative-link a { color: #2f5597; word-break: break-all; }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <div class="logo">
                <span class="logo-text">armely</span>
            </div>
            <h1>Welcome to Armely Admin</h1>
        </div>
        <div class="email-body">
            <p>Hi {{ $name ?? 'Admin' }},</p>
            <p>Your administrator account has been created. To activate your access, please set your password using the link below:</p>
            <div class="button-container">
                <a href="{{ $activationLink }}" class="button">Activate Your Account</a>
            </div>
            <p>After setting your password, you can sign in anytime here:</p>
            <div class="button-container">
                <a href="{{ $loginLink }}" class="button" style="background: #1e3a6d;">Go to Admin Login</a>
            </div>
            <div class="alternative-link">
                <p><strong>Button not working?</strong> Copy this activation link into your browser:</p>
                <p><a href="{{ $activationLink }}">{{ $activationLink }}</a></p>
            </div>
            <p style="margin-top: 18px; font-size: 13px; color: #856404; background: #fff3cd; padding: 12px 16px; border-left: 4px solid #ffc107; border-radius: 6px;">This activation link expires in 60 minutes for your security.</p>
        </div>
        <div class="email-footer">
            <p><strong>Armely Admin Panel</strong></p>
            <p>This is an automated email. Please do not reply.</p>
            <p style="margin-top: 15px; font-size: 12px; color: #999;">© 2025 Armely. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
