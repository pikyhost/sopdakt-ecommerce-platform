<!DOCTYPE html>
<html lang="en">
<head>
    @if ($settings?->meta_thanks_pixel_code)
        {!! $settings?->meta_thanks_pixel_code !!}
    @else
        {!! $settings?->facebook_pixel_code !!}
    @endif

    {!! $settings?->tiktok_pixel_code !!}
    {!! $settings?->google_pixel_code !!}
    {!! $settings?->snapchat_pixel_code !!}
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You | Page</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background-color: #f4f7fa;
        }

        .header {
            background-color: var(--nav-bar-background-color);
            color: var(--nav-bar-items-text-color);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 {
            font-size: 2rem;
        }

        .header nav a {
            color: var(--nav-bar-items-text-color);
            text-decoration: none;
            margin: 0 0.5rem;
        }

        .main-content {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 4rem 2rem;
            text-align: center;
        }

        .thank-you-container {
            background: #fff;
            border-radius: 10px;
            padding: 3rem 2rem;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 650px;
        }

        .checkmark-circle {
            width: 100px;
            height: 100px;
            margin: 0 auto 2rem;
            background-color: #4CAF50;
            border-radius: 50%;
            position: relative;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        }

        .checkmark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(45deg);
            width: 25px;
            height: 50px;
            border: solid #fff;
            border-width: 0 8px 8px 0;
            background: transparent;
        }

        .thank-you-container h2 {
            margin: 1rem 0;
            font-size: 2rem;
            font-weight: bold;
            color: #333;
        }

        .thank-you-container p {
            color: #555;
            font-size: 1.1rem;
            margin-bottom: 1.5rem;
        }

        .cta-button {
            padding: 12px 30px;
            background-color: #4CAF50;
            color: #fff;
            border-radius: 30px;
            text-decoration: none;
            font-size: 1.1rem;
            display: inline-block;
            transition: background-color 0.3s ease;
        }

        .cta-button:hover {
            background-color: #45a049;
        }

        .footer {
            background-color: var(--footer-section-background-color);
            color: var(--footer-section-subtitle-color);
            text-align: center;
            padding: 1rem;
            font-size: 0.9rem;
        }

        .footer a {
            color: var(--footer-section-subtitle-color);
            text-decoration: none;
            margin: 0 0.5rem;
        }

        .footer a:hover {
            text-decoration: underline;
        }

    </style>
</head>
<body>
    <header class="header">
        <h1>{{$settings?->website_name}}</h1>
        <nav></nav>
    </header>

    <main class="main-content">
        <div class="thank-you-container">
            <div class="checkmark-circle">
                <div class="checkmark"></div>
            </div>
            <h2>{{__('Thank You for Your Order!')}}</h2>
            <p dir="rtl">{{__('Your request has been sent, thank you for choosing')}}{{$settings?->website_name}}. </p>
            <a href="{{route('landing-page.show-by-slug',$landingPage->slug)}}" class="cta-button">{{__('Continue Shopping')}}</a>
        </div>
    </main>

    <footer class="footer">
        <p>&copy; {{date('Y')}} {{$settings?->website_name}}. {{__('All rights reserved')}}.</p>
        <p></p>
    </footer>
</body>
</html>
