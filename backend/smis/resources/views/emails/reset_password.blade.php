<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            margin: 20px auto;
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
        }
        .header {
            background-color: #800000; /* PUP Maroon */
            color: white;
            padding: 20px;
            text-align: center;
        }
        .content {
            padding: 30px;
        }
        .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #800000;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin-top: 20px;
        }
        .footer {
            background-color: #f4f4f4;
            padding: 10px;
            text-align: center;
            font-size: 0.8em;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>SMIS</h1>
            <p>Supply Management Information System</p>
        </div>
        <div class="content">
            <h2>Hello, {{ $name }}!</h2>
            <p>You are receiving this email because we received a password reset request for your account.</p>
            <p>If you did not request a password reset, no further action is required.</p>
            <p>To proceed with resetting your password, please click the button below:</p>
            <a href="{{ $url }}" class="button">Reset Password</a>
            <p>This password reset link will expire in 60 minutes.</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} SMIS - PUP. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
