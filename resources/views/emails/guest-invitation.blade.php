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

    <!-- Include FontAwesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        .website-name {
            font-size: 28px;
            font-weight: bold;
            font-family: 'Poppins', sans-serif;
            color: #1877F2; /* Facebook Blue */
            text-transform: uppercase;
            letter-spacing: 1.5px;
            text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.2);
            margin-bottom: 10px;
            display: inline-block;
            background: linear-gradient(45deg, #1877F2, #1DA1F2, #E4405F);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .footer {
            text-align: center;
            padding: 20px;
            background: #f8f9fa;
            border-top: 2px solid #ddd;
        }

        .contact-info {
            font-size: 16px;
            margin-bottom: 10px;
        }

        .contact-info i {
            margin-right: 8px;
            color: #E4405F;
        }
        .social-icons {
            display: flex;
            gap: 15px;
            justify-content: center;
            align-items: center;
            margin-top: 15px;
        }

        .social-icons a {
            font-size: 36px; /* Bigger icons */
            color: inherit; /* Use default brand colors */
            text-decoration: none;
            transition: transform 0.3s ease, opacity 0.3s ease;
        }

        .social-icons a:hover {
            transform: scale(1.2);
            opacity: 0.8;
        }

        /* Brand Colors */
        .social-icons .facebook { color: #1877F2; }
        .social-icons .x { color: #000000; }
        .social-icons .instagram { color: #E4405F; }
        .social-icons .youtube { color: #FF0000; }
        .social-icons .linkedin { color: #0077B5; }
        .social-icons .whatsapp { color: #25D366; }
        .social-icons .snapchat { color: #FFFC00; }
        .social-icons .tiktok { color: #000000; }
        .social-icons .telegram { color: #0088CC; }
    </style>

    <style>
        body {
            font-family: 'Poppins', Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
            direction: {{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }};
            text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }};
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0px 4px 20px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #d63384;
            text-align: center;
            margin-bottom: 5px;
        }
        .website-name {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            color: #555;
            margin-bottom: 15px;
        }
        p {
            font-size: 16px;
            color: #333;
            line-height: 1.6;
            margin: 10px 0;
        }

        .footer {
            text-align: center;
            font-size: 14px;
            color: #777;
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
        }
        strong {
            color: #d63384;
        }
        .social-icons {
            margin-top: 10px;
        }
        .social-icons a {
            margin: 0 8px;
            display: inline-block;
        }
        .social-icons img {
            width: 28px;
            height: 28px;
            transition: transform 0.3s ease;
        }
        .social-icons img:hover {
            transform: scale(1.1);
        }
    </style>
</head>
<body>
@php
    $siteSettings = App\Models\Setting::getAllSettings();
    $locale = app()->getLocale();
    $contact = \App\Models\Setting::getContactDetails();
    $siteName = $siteSettings["site_name"] ?? ($locale === 'ar' ? 'لا يوجد شعار بعد' : 'No Logo Yet');
@endphp

<div class="container">
    <h1>{{ __("You're Invited!") }}</h1>
    <div class="website-name">{{ $siteName }}</div>

    <p>{{ __("We are excited to invite you to explore our extensive digital library, where knowledge meets convenience. Join us and access a wide range of books anytime, anywhere!") }}</p>

    <p><strong>{{ __("Why Join?") }}</strong></p>
    <ul>
        <li>{{ __("Access thousands of books in various categories.") }}</li>
        <li>{{ __("Enjoy a seamless reading experience with our user-friendly interface.") }}</li>
        <li>{{ __("Stay updated with the latest publications and recommendations.") }}</li>
    </ul>

    <p>{{ __("Click the button below to start your journey with us:") }}</p>

    <div style="text-align: center; margin: 20px 0;">
        <a href="{{ $acceptUrl }}" style="
            display: inline-block;
            background-color: #d63384;
            color: #ffffff;
            padding: 12px 24px;
            font-size: 16px;
            font-weight: bold;
            text-decoration: none;
            border-radius: 6px;
            transition: background 0.3s;
        " onmouseover="this.style.background='#a82664'" onmouseout="this.style.background='#d63384'">
            {{ __("Join Now") }}
        </a>
    </div>

    <p>{{ __("We look forward to having you with us!") }}</p>
</div>

<div class="footer">
    <div class="contact-info">
        <p>
            <i class="fas fa-envelope"></i>
            {{ app()->getLocale() === 'ar' ? 'للتواصل عبر البريد الإلكتروني:' : 'Contact us via Email:' }}
            <a href="mailto:{{ $contact['email'] }}">
                {{ $contact['email'] }}
            </a>
        </p>
        <p>
            <i class="fas fa-phone"></i>
            {{ app()->getLocale() === 'ar' ? 'للتواصل عبر الهاتف:' : 'Contact us via Phone:' }}
            <a href="tel:{{ $contact['phone'] }}">
                {{ $contact['phone'] }}
            </a>
        </p>
    </div>


    <div class="social-icons">
        @foreach (\App\Models\Setting::getSocialMediaLinks() as $platform => $link)
            @if ($link)
                <a href="{{ $link }}" target="_blank" class="{{ strtolower($platform) }}">
                    @if(strtolower($platform) === 'x')
                        <i class="fa-brands fa-x-twitter"></i>
                    @else
                        <i class="fab fa-{{ strtolower($platform) }}"></i>
                    @endif
                </a>
            @endif
        @endforeach
    </div>

</div>

</body>
</html>
