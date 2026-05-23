<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #2d3748;
            margin: 0;
            padding: 0;
            background-color: #f7fafc;
        }
        .wrapper {
            width: 100%;
            padding: 40px 0;
            background-color: #f7fafc;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }
        .header {
            padding: 40px 20px;
            text-align: center;
            border-bottom: 1px solid #edf2f7;
        }
        .logo {
            width: 80px;
            height: auto;
            margin-bottom: 15px;
        }
        .header h1 {
            margin: 0;
            color: #800000;
            font-size: 24px;
            font-weight: 700;
            letter-spacing: -0.025em;
        }
        .header p {
            margin: 5px 0 0;
            color: #718096;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }
        .content {
            padding: 40px;
            text-align: center;
        }
        .content h2 {
            margin-top: 0;
            color: #1a202c;
            font-size: 20px;
        }
        .content p {
            margin-bottom: 25px;
            color: #4a5568;
        }
        .button {
            display: inline-block;
            padding: 14px 32px;
            background-color: #800000;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            margin: 10px 5px;
        }
        .button-secondary {
            background-color: transparent !important;
            color: #800000 !important;
            border: 2px solid #800000;
        }
        .expiration {
            margin-top: 30px;
            font-size: 13px;
            color: #a0aec0;
        }
        .footer {
            padding: 30px;
            text-align: center;
            font-size: 12px;
            color: #718096;
            background-color: #f8fafc;
        }
        .footer hr {
            border: 0;
            border-top: 1px solid #edf2f7;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container">
            <div class="header">
                <img src="{{ $message->embed(public_path('images/esmis_logo.png')) }}" alt="{{ config('identities.org_acronym') }} Logo" class="logo">
                <h1>{{ config('identities.system_acronym') }}</h1>
                <p>{{ config('identities.system_name') }}</p>
            </div>
            <div class="content">
                <h2>Hello, {{ $name }}!</h2>
                <p>We received a request to reset the password for your {{ config('identities.system_acronym') }} account.</p>
                
                <a href="{{ $url }}" class="button">Reset Password</a>
                <br>
                <a href="{{ $resendUrl }}" class="button button-secondary">Request New Link</a>
                
                <p class="expiration">
                    This link will expire in <strong>10 minutes</strong>.<br>
                    If you did not request this, you can safely ignore this email.
                </p>
            </div>
            <div class="footer">
                <p>&copy; {{ date('Y') }} {{ config('identities.org_name') }}</p>
                <p>Supply Management Office</p>
            </div>
        </div>
    </div>
</body>
</html>
