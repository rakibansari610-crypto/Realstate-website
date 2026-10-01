<!DOCTYPE html>
<html>
<head>
    <title>OTP for Password Reset</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f8;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        h2 {
            color: #333333;
        }
        p {
            color: #555555;
            line-height: 1.5;
        }
        .otp {
            display: inline-block;
            font-size: 28px;
            font-weight: bold;
            background-color: #f0f0f0;
            padding: 12px 20px;
            margin: 15px 0;
            border-radius: 6px;
            letter-spacing: 3px;
        }
        .footer {
            margin-top: 30px;
            font-size: 14px;
            color: #888888;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Password Reset Request</h2>
        <p>Hello  {{ $userName ?? 'User' }},</p>
        <p>We received a request to reset your password. Your One-Time Password (OTP) is:</p>
        <div class="otp">{{ $otp }}</div>
        <p>This OTP will expire in <strong>10 minutes</strong>. Please do not share it with anyone.</p>
        <p class="footer">Regards,<br>The MaidPlace Team</p>
    </div>
</body>
</html>
