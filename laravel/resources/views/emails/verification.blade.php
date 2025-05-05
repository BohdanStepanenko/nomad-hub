<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
<div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 10px;">
    <h2 style="text-align: center; color: #6D5DFC;">Verify Your Email Address</h2>
    <p>Hello, {{ $name }}</p>
    <p>Thank you for signing up! Please verify your email address by clicking the button below:</p>
    <div style="text-align: center; margin: 20px 0;">
        <a href="{{ $url }}"
           style="display: inline-block; padding: 10px 20px; background-color: #6D5DFC; color: white; text-decoration: none; border-radius: 5px;">
            Verify Email Address
        </a>
    </div>
    <p>If the button above does not work, copy and paste the following link into your browser:</p>
    <p style="word-break: break-all;"><a href="{{ $url }}" style="color: #6D5DFC;">{{ $url }}</a></p>
    <p>Thank you,</p>
    <p>The {{ config('app.name') }} Team</p>
</div>
</body>
</html>
