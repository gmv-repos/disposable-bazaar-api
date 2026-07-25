<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset OTP</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            color: #333;
            margin: 0;
            padding: 0;
            line-height: 1.6;
        }
        .container {
            max-width: 600px;
            margin: 50px auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #007bff;
        }
        p {
            margin: 10px 0;
        }
        .otp {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
        }
        footer {
            margin-top: 20px;
            font-size: 0.9em;
            color: #666;
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>Password Reset OTP</h1>
        <p>Hello,</p>
        <p>You requested to reset your password. Please use the following One Time Password (OTP) to proceed:</p>
        <p class="otp">{{ $code }}</p>
        <p>This OTP is valid for the next 10 minutes. If you did not request a password reset, please ignore this email.</p>
        <p>Thank you!</p>
        <footer>
            <p>Best regards,</p>
            <p>Disposible Bazaar</p>
        </footer>
    </div>

</body>
</html>
