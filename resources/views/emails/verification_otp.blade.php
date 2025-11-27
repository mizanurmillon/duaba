<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Verification Code</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            padding: 20px;
            background-color: #f4f4f4;
        }

        .container {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            max-width: 600px;
            margin: auto;
            border: 1px solid #ddd;
        }

        h2 {
            color: #333;
        }

        .code {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
            margin: 20px 0;
        }

        .footer {
            font-size: 14px;
            color: #777;
            margin-top: 20px;
        }

        .footer a {
            color: #007bff;
            text-decoration: none;
        }
    </style>
</head>

<body>
    <div class="container">
        <p>Dear {{ $user->name }},</p>

        <p>Thank you for Login with Duaba.</p>
        <p>To complete your Login, please enter the following verification code:</p>

        <p>Code: <span class="code">{{ $otp }}</span></p>

        <p>This code is valid for 3 minute. Please keep it confidential for security purposes. If you did not initiate
            this login, feel free to disregard this email.</p>

        <p>Best regards,</p>
        <p>Duaba</p>
        {{--  <p><a href="https://duaba.com">https://duaba.com</a></p>  --}}
    </div>
</body>

</html>
