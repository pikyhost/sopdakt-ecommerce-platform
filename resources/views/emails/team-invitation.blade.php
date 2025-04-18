<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invitation to Join {{ config('app.name') }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #fdf2f8;
            margin: 0;
            padding: 0;
            color: #4b5563;
        }
        .container {
            width: 100%;
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #fbcfe8;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        }
        .header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 1px solid #fbcfe8;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #ec4899;
        }
        .content {
            padding: 20px 0;
            text-align: center;
        }
        .content p {
            font-size: 16px;
            line-height: 1.6;
            margin: 10px 0;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background-color: #ec4899;
            color: #ffffff;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }
        .btn:hover {
            background-color: #c02675;
        }
        .footer {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #fbcfe8;
            margin-top: 20px;
            color: #6b7280;
            font-size: 12px;
        }
        .footer a {
            color: #9333ea;
            text-decoration: none;
        }
        .footer a:hover {
            text-decoration: underline;
        }

        /* Dark Mode Support */
        @media (prefers-color-scheme: dark) {
            body {
                background-color: #0f172a;
                color: #e2e8f0;
            }
            .container {
                background-color: #1e293b;
                border-color: #334155;
                box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
            }
            .header h1 {
                color: #f472b6;
            }
            .btn {
                background-color: #f472b6;
                color: #1e293b;
            }
            .btn:hover {
                background-color: #be185d;
            }
            .footer {
                color: #94a3b8;
                border-top-color: #334155;
            }
            .footer a {
                color: #c084fc;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>You're Invited to Join {{ config('app.name') }}</h1>
    </div>
    <div class="content">
        <p>Hello,</p>
        <p>You have been invited to join <strong>{{ config('app.name') }}</strong> as a
            <strong>{{ \App\Enums\UserRole::getLabelFor($invitation->role->name) }}</strong>.</p>
        <p>Click the button below to accept the invitation:</p>
        <a href="{{ $acceptUrl }}" class="btn">Accept Invitation</a>
    </div>
    <div class="footer">
        <p>If you weren’t expecting this invitation, feel free to ignore this email.</p>
        <p>© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        <p><a href="{{ config('app.url') }}">Visit Website</a></p>
    </div>
</div>
</body>
</html>
