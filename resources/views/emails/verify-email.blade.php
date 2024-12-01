<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            background-color: #f7f7f7;
            padding: 20px;
        }
        .email-container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            max-width: 600px;
            margin: 0 auto;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .email-header {
            text-align: center;
            margin-bottom: 20px;
        }
        .email-header h1 {
            color: #4CAF50;
        }
        .email-body {
            font-size: 16px;
            line-height: 1.5;
        }
        .email-body p {
            margin-bottom: 20px;
        }
        .verify-button {
            display: inline-block;
            background-color: #4CAF50;
            color: #fff;
            padding: 12px 20px;
            text-decoration: none;
            border-radius: 4px;
            font-size: 16px;
        }
        .verify-button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
<div class="email-container">
    <div class="email-header">
        <h1>Email Verification</h1>
    </div>
    <div class="email-body">
        <p>Hi {{ $user->first_name }},</p>
        <p>Thank you for registering with us! Please click the button below to verify your email address and activate your account.</p>
        <a href="{{ $verificationUrl }}" class="verify-button">Verify Your Email</a>
        <p>If you did not register, please ignore this email.</p>
        <p>Thanks, <br> Your Company Name</p>
    </div>
</div>
</body>
</html>
