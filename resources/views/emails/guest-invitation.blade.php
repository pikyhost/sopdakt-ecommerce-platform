<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>دعوة للانضمام إلى {{ config('app.name') }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #fdf2f8; /* Light Pink Background */
            margin: 0;
            padding: 0;
            color: #4b5563; /* Dark Gray Text */
        }
        .container {
            width: 100%;
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff; /* White Background */
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #fbcfe8; /* Soft Pink Border */
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
            color: #ec4899; /* Pink Header */
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
            background-color: #ec4899; /* Button Color */
            color: #ffffff; /* White Text */
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }
        .btn:hover {
            background-color: #c02675; /* Darker Pink Hover */
        }
        .footer {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #fbcfe8;
            margin-top: 20px;
            color: #6b7280; /* Muted Gray Text */
            font-size: 12px;
        }
        .footer a {
            color: #9333ea; /* Purple Accent */
            text-decoration: none;
        }
        .footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
@php
    // Retrieve site settings
    $siteSettings = App\Models\Setting::getAllSettings();
    $locale = app()->getLocale();

    // Get site name
    $siteName = $siteSettings["site_name"] ?? ($locale === 'ar' ? 'لا يوجد شعار بعد' : 'No Logo Yet');
@endphp

<div class="container">
    <div class="header">
        <h1>انضم إلى {{ $siteName }}</h1>
    </div>
    <div class="content">
        <p>مرحباً،</p>
        <p>لقد تمت دعوتك للانضمام إلى {{ config('app.name') }}.</p>
        <p>اضغط على الزر أدناه لإنشاء حسابك:</p>
        <a href="{{ $acceptUrl }}" class="btn">إنشاء حساب</a>
    </div>
    <div class="footer">
        <p>إذا لم تكن تتوقع هذه الدعوة، يمكنك تجاهل هذا البريد الإلكتروني بأمان.</p>
        <p>© {{ date('Y') }} {{ config('app.name') }}. جميع الحقوق محفوظة.</p>
        <p><a href="{{ config('app.url') }}">زيارة الموقع</a></p>
    </div>
</div>
</body>
</html>
