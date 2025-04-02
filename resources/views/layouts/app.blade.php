@php
    // Retrieve site settings
    $siteSettings = App\Models\Setting::getAllSettings();
    $locale = app()->getLocale();

        // Get favicon based on locale or fallback to default
    $faviconPath = $siteSettings["favicon"] ?? null;
    $favicon = $faviconPath ? \Illuminate\Support\Facades\Storage::url($faviconPath) : asset('assets/images/clients/client1.png');


    // Get logo based on locale
    $logoPath = $siteSettings["logo_{$locale}"] ?? $siteSettings["logo_en"] ?? null;
    $logo = $logoPath ? \Illuminate\Support\Facades\Storage::url($logoPath) : null;

    // Get site name
    $siteName = $siteSettings["site_name"] ?? ($locale === 'ar' ? 'لا يوجد شعار بعد' : 'No Logo Yet');

    $socialLinks = \App\Models\Setting::getSocialMediaLinks();

    // Ensure correct icon class mappings
    $iconClasses = [
        'facebook'  => 'fa-brands fa-facebook',  // FontAwesome
        'youtube'   => 'fa-brands fa-youtube',
        'instagram' => 'fa-brands fa-instagram',
        'x'         => 'fa-brands fa-x-twitter',
        'snapchat'  => 'fa-brands fa-snapchat',
        'tiktok'    => 'fa-brands fa-tiktok',
    ];
@endphp
    <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>@yield('title', 'Default Title')</title>

    <meta name="keywords" content="HTML5 Template">
    <meta name="description" content="X - Bootstrap eCommerce Template">
    <meta name="author" content="SW-THEMES">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <script async src="https://www.googletagmanager.com/gtag/js?id=G-F5CFYBRQ0F"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'G-F5CFYBRQ0F');
    </script>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ $favicon }}">


    <!-- Ensure correct asset loading for dynamic routes -->
    <base href="{{ url('/') }}/">

    <script>
        WebFontConfig = {
            google: { families: ['Open+Sans:300,400,600,700,800', 'Poppins:300,400,500,600,700', 'Shadows+Into+Light:400'] }
        };
        (function (d) {
            var wf = d.createElement('script'), s = d.scripts[0];
            wf.src = "{{ asset('assets/js/webfont.js') }}";
            wf.async = true;
            s.parentNode.insertBefore(wf, s);
        })(document);
    </script>

    <!-- Plugins CSS File -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">

    <!-- Main CSS File -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendor/fontawesome-free/css/all.min.css') }}">

    <!-- Ensure Bootstrap JS loads properly -->
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}" defer></script>
    {{-- Page-specific styles --}}
    @stack('styles')
    @livewireStyles
</head>

<body>

<div class="page-wrapper">
    @php
        $topNotices = App\Models\TopNotice::where('is_active', true)->get();
        $locale = app()->getLocale();
    @endphp

    @if($topNotices->count())
        <div class="top-notice-bar" id="top-notice">
            <div class="notice-slider-container">
                <div class="notice-slider-track" id="notice-track"></div>
            </div>
            <div class="notice-progress-container">
                <div class="notice-progress" id="notice-progress"></div>
            </div>
        </div>

        <script>
            document.addEventListener("DOMContentLoaded", function () {
                const notices = @json($topNotices);
                if (notices.length === 0) return;

                const noticeTrack = document.getElementById("notice-track");
                const progressBar = document.getElementById("notice-progress");
                const noticeContainer = document.getElementById("top-notice");
                const locale = "{{ $locale }}";

                // Configuration
                const DISPLAY_DURATION = 3000;
                const ANIMATION_DURATION = 500;
                let currentIndex = 0;
                let rotationInterval;
                let progressStartTime;
                let progressRemaining = DISPLAY_DURATION;

                // Create notice elements
                function createNoticeElement(notice) {
                    const noticeEl = document.createElement('div');
                    noticeEl.className = 'notice-slide';

                    noticeEl.innerHTML = `
                    <div class="container">
                        <div class="notice-content-wrapper">
                            <div class="notice-content">${locale === "ar" ? notice.content_ar : notice.content_en}</div>
                            <div class="notice-actions" id="cta-wrapper">
                                ${notice.cta_text_en && notice.cta_url ?
                        `<a href="${notice.cta_url}" class="notice-btn btn-primary-reverse">
                                        ${locale === "ar" ? notice.cta_text_ar : notice.cta_text_en}
                                    </a>` : ''}
                                ${notice.cta_text_2_en && notice.cta_url_2 ?
                        `<a href="${notice.cta_url_2}" class="notice-btn btn-outline">
                                        ${locale === "ar" ? notice.cta_text_2_ar : notice.cta_text_2_en}
                                    </a>` : ''}
                                ${(notice.limited_time_text_en || notice.limited_time_text_ar) ?
                        `<div class="limited-time-badge">
                                        ${locale === "ar" ?
                            (notice.limited_time_text_ar || notice.limited_time_text_en) :
                            (notice.limited_time_text_en || notice.limited_time_text_ar)}
                                    </div>` : ''}
                            </div>
                        </div>
                    </div>
                `;

                    return noticeEl;
                }

                // Initialize slider
                function initSlider() {
                    noticeTrack.innerHTML = '';

                    notices.forEach(notice => {
                        noticeTrack.appendChild(createNoticeElement(notice));
                    });

                    noticeTrack.children[0].classList.add('active');
                    startRotation();
                }

                function goToNextSlide() {
                    const slides = noticeTrack.children;
                    const currentSlide = slides[currentIndex];
                    const nextIndex = (currentIndex + 1) % slides.length;
                    const nextSlide = slides[nextIndex];

                    currentSlide.classList.add('exiting');
                    nextSlide.classList.add('entering');

                    setTimeout(() => {
                        currentSlide.classList.remove('active', 'exiting');
                        nextSlide.classList.add('active');
                        nextSlide.classList.remove('entering');

                        currentIndex = nextIndex;
                        resetProgress();
                    }, ANIMATION_DURATION);
                }

                function startRotation() {
                    stopRotation();
                    resetProgress();

                    rotationInterval = setInterval(() => {
                        goToNextSlide();
                    }, DISPLAY_DURATION + ANIMATION_DURATION);
                }

                function stopRotation() {
                    clearInterval(rotationInterval);
                    progressBar.style.transition = 'none';

                    if (progressStartTime) {
                        const elapsed = Date.now() - progressStartTime;
                        progressRemaining = DISPLAY_DURATION - elapsed;
                        progressBar.style.width = `${100 - (elapsed / DISPLAY_DURATION) * 100}%`;
                    }
                }

                function resetProgress() {
                    progressBar.style.transition = 'none';
                    progressBar.style.width = '0%';

                    void progressBar.offsetWidth;

                    progressBar.style.transition = `width ${DISPLAY_DURATION}ms linear`;
                    progressBar.style.width = '100%';
                    progressStartTime = Date.now();
                }

                initSlider();

                noticeContainer.addEventListener('mouseenter', stopRotation);

                noticeContainer.addEventListener('mouseleave', () => {
                    progressBar.style.transition = `width ${progressRemaining}ms linear`;
                    progressBar.style.width = '100%';
                    progressStartTime = Date.now();

                    rotationInterval = setInterval(() => {
                        goToNextSlide();
                    }, progressRemaining + ANIMATION_DURATION);
                });

                noticeContainer.addEventListener('touchstart', stopRotation);
                noticeContainer.addEventListener('touchend', () => {
                    progressBar.style.transition = `width ${progressRemaining}ms linear`;
                    progressBar.style.width = '100%';
                    progressStartTime = Date.now();

                    rotationInterval = setInterval(() => {
                        goToNextSlide();
                    }, progressRemaining + ANIMATION_DURATION);
                });
            });
        </script>

        <style>
            .top-notice-bar {
                position: relative;
                background: linear-gradient(135deg, #2b5876 0%, #4e4376 100%);
                color: white;
                padding: 12px 0;
                overflow: hidden;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            }

            .container {
                max-width: 1200px;
                margin: 0 auto;
                padding: 0 15px;
            }

            .notice-slider-container {
                position: relative;
                width: 100%;
                overflow: hidden;
                height: 44px; /* Fixed height to match original */
            }

            .notice-slider-track {
                position: relative;
                width: 100%;
            }

            .notice-slide {
                position: absolute;
                width: 100%;
                opacity: 0;
                transform: translateY(10px);
                transition: all 0.5s cubic-bezier(0.25, 0.1, 0.25, 1);
                pointer-events: none;
            }

            .notice-slide.active {
                position: relative;
                opacity: 1;
                transform: translateY(0);
                pointer-events: auto;
            }

            .notice-slide.exiting {
                opacity: 0;
                transform: translateY(-10px);
            }

            .notice-slide.entering {
                opacity: 0;
                transform: translateY(10px);
            }

            .notice-content-wrapper {
                display: flex;
                align-items: center;
                flex-wrap: wrap;
                gap: 15px;
            }

            .notice-content {
                font-size: 16px;
                font-weight: 500;
                margin-right: auto;
            }

            .notice-actions {
                display: flex;
                gap: 10px;
                align-items: center;
            }

            .notice-btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                padding: 6px 12px;
                border-radius: 20px;
                font-size: 13px;
                font-weight: 600;
                text-decoration: none;
                transition: all 0.3s ease;
                white-space: nowrap;
                border: 1px solid;
            }

            .btn-primary-reverse {
                background-color: white;
                color: #2b5876;
                border-color: white;
            }

            .btn-primary-reverse:hover {
                background-color: transparent;
                color: white;
            }

            .btn-outline {
                background-color: transparent;
                color: white;
                border-color: rgba(255, 255, 255, 0.5);
            }

            .btn-outline:hover {
                background-color: rgba(255, 255, 255, 0.1);
                border-color: white;
            }

            .limited-time-badge {
                background-color: rgba(255, 255, 255, 0.15);
                padding: 4px 10px;
                border-radius: 12px;
                font-size: 12px;
                font-weight: 600;
                animation: pulse 2s infinite;
            }

            @keyframes pulse {
                0% { transform: scale(1); opacity: 0.8; }
                50% { transform: scale(1.05); opacity: 1; }
                100% { transform: scale(1); opacity: 0.8; }
            }

            .notice-progress-container {
                position: absolute;
                bottom: 0;
                left: 0;
                width: 100%;
                height: 3px;
                background-color: rgba(255, 255, 255, 0.1);
            }

            .notice-progress {
                height: 100%;
                width: 0%;
                background-color: rgba(255, 255, 255, 0.7);
            }

            @media (max-width: 768px) {
                .notice-content-wrapper {
                    flex-direction: column;
                    align-items: flex-start;
                    gap: 8px;
                }

                .notice-content {
                    margin-right: 0;
                    margin-bottom: 8px;
                    font-size: 14px;
                }

                .notice-actions {
                    width: 100%;
                    justify-content: flex-start;
                }

                .notice-btn {
                    padding: 5px 10px;
                    font-size: 12px;
                }
            }
        </style>
    @endif

    <header  class="header">
        <style>
            /* Base Styles */
            .header {
                font-family: 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
                position: relative;
                z-index: 999;
            }

            /* Header Top Section - Top Right Elements */
            .header-top {
                font-size: 0.85rem;
                border-bottom: 1px solid rgba(255, 255, 255, 0.1);
                background-color: #222;
                color: white;
            }
            .header-top-container {
                display: flex;
                justify-content: flex-end;
                align-items: center;
                padding: 8px 0;
            }
            .header-top-right {
                display: flex;
                align-items: center;
                gap: 20px;
            }
            .header-top-item {
                display: flex;
                align-items: center;
                gap: 8px;
            }
            .header-top-separator {
                width: 1px;
                height: 20px;
                background-color: rgba(255,255,255,0.3);
            }

            /* Language Selector */
            .language-selector {
                position: relative;
            }
            .language-selector-toggle {
                display: flex;
                align-items: center;
                gap: 6px;
                cursor: pointer;
            }
            .language-dropdown {
                position: absolute;
                top: 100%;
                right: 0;
                min-width: 160px;
                background: white;
                border-radius: 4px;
                box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
                display: none;
                z-index: 1000;
            }
            .language-selector:hover .language-dropdown {
                display: block;
                animation: fadeIn 0.2s ease;
            }
            .language-option {
                padding: 8px 16px;
                display: flex;
                align-items: center;
                gap: 8px;
                color: #333;
                text-decoration: none;
            }
            .language-option:hover {
                background-color: #f8f9fa;
            }
            .language-option.active {
                color: #007bff;
            }

            /* Social Icons */
            .social-icons {
                display: flex;
                align-items: center;
                gap: 12px;
            }
            .social-icon {
                color: white;
                font-size: 16px;
                transition: all 0.2s ease;
            }
            .social-icon:hover {
                transform: translateY(-2px);
                opacity: 0.8;
            }

            /* Header Middle Section - Search and Icons */
            .header-middle {
                background: white;
                padding: 15px 0;
                border-bottom: 1px solid #eee;
            }
            .header-middle-container {
                display: flex;
                justify-content: space-between;
                align-items: center;
            }
            .logo img {
                max-height: 50px;
            }
            .header-middle-right {
                display: flex;
                align-items: center;
                gap: 25px;
            }
            .header-search {
                width: 400px;
            }
            .header-icons {
                display: flex;
                align-items: center;
                gap: 20px;
            }
            .header-icon {
                font-size: 22px;
                color: #333;
                position: relative;
                transition: all 0.2s ease;
            }
            .header-icon:hover {
                color: #007bff;
            }
            .icon-badge {
                position: absolute;
                top: -5px;
                right: -8px;
                background: #007bff;
                color: white;
                border-radius: 50%;
                width: 18px;
                height: 18px;
                font-size: 10px;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .header-contact {
                display: flex;
                align-items: center;
                gap: 10px;
            }
            .contact-icon {
                background: #007bff;
                color: white;
                width: 36px;
                height: 36px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .contact-text {
                display: flex;
                flex-direction: column;
            }
            .contact-label {
                font-size: 12px;
                color: #777;
            }
            .contact-number {
                font-weight: 600;
                color: #333;
            }

            /* Header Navigation */
            .header-nav {
                background: #007bff;
                padding: 0;
            }
            .nav-menu {
                display: flex;
                justify-content: flex-end;
                list-style: none;
                margin: 0;
                padding: 0;
            }
            .nav-item {
                position: relative;
            }
            .nav-link {
                color: white;
                padding: 15px 20px;
                display: block;
                font-weight: 600;
                text-decoration: none;
                position: relative;
            }
            .nav-link:after {
                content: '';
                position: absolute;
                bottom: 0;
                left: 0;
                width: 0;
                height: 3px;
                background-color: white;
                transition: all 0.3s ease;
            }
            .nav-item:hover .nav-link:after {
                width: 100%;
            }

            /* Dropdown Menus */
            .dropdown-menu {
                position: absolute;
                top: 100%;
                right: 0;
                background: white;
                min-width: 220px;
                box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
                display: none;
                z-index: 1000;
            }
            .nav-item:hover .dropdown-menu {
                display: block;
                animation: fadeIn 0.2s ease;
            }
            .dropdown-link {
                padding: 10px 20px;
                display: block;
                color: #333;
                text-decoration: none;
                transition: all 0.2s ease;
            }
            .dropdown-link:hover {
                background: #f8f9fa;
                color: #007bff;
            }

            /* Megamenu */
            .megamenu {
                position: absolute;
                top: 100%;
                right: 0;
                width: 900px;
                background: white;
                box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
                display: none;
                z-index: 1000;
                padding: 20px;
            }
            .nav-item:hover .megamenu {
                display: flex;
                animation: fadeIn 0.2s ease;
            }
            .megamenu-column {
                flex: 1;
                padding: 0 15px;
            }
            .megamenu-title {
                font-size: 16px;
                font-weight: 700;
                margin-bottom: 15px;
                padding-bottom: 10px;
                border-bottom: 1px solid #eee;
            }
            .megamenu-banner {
                position: relative;
                height: 100%;
                min-height: 250px;
                background-size: cover;
                background-position: center;
                border-radius: 4px;
                overflow: hidden;
            }
            .banner-content {
                position: absolute;
                bottom: 0;
                left: 0;
                right: 0;
                padding: 20px;
                background: linear-gradient(to top, rgba(0,0,0,0.7), transparent);
                color: white;
            }
            .banner-tag {
                position: absolute;
                top: 15px;
                left: 15px;
                background: #dc3545;
                color: white;
                padding: 3px 10px;
                border-radius: 4px;
                font-size: 12px;
                font-weight: 600;
            }

            /* Animations */
            @keyframes fadeIn {
                from { opacity: 0; }
                to { opacity: 1; }
            }

            /* Responsive Adjustments */
            @media (max-width: 1200px) {
                .header-search {
                    width: 300px;
                }
                .megamenu {
                    width: 800px;
                }
            }
            @media (max-width: 992px) {
                .header-middle-container {
                    flex-wrap: wrap;
                }
                .header-search {
                    order: 3;
                    width: 100%;
                    margin-top: 15px;
                }
                .megamenu {
                    width: 600px;
                }
            }
            @media (max-width: 768px) {
                .header-top-item {
                    display: none;
                }
                .header-top-item.social-icons {
                    display: flex;
                }
                .header-contact {
                    display: none;
                }
                .megamenu {
                    width: 100%;
                    right: 0;
                    left: 0;
                }
            }

            .header-top-container {
                background: #111; /* Deep black background */
                border-bottom: 1px solid #222; /* Soft separator */
            }

            .top-message {
                color: #f1f1f1;
                font-size: 14px;
            }

            .header-dropdown {
                position: relative;
                cursor: pointer;
            }

            .header-dropdown .header-menu {
                display: none;
                position: absolute;
                top: 100%;
                left: 0;
                min-width: 160px;
                background: rgba(30, 30, 30, 0.95); /* Dark semi-transparent dropdown */
                border-radius: 6px;
                padding: 8px 0;
                box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
                z-index: 999;
            }

            .header-dropdown:hover .header-menu {
                display: block;
            }

            .header-menu ul {
                margin: 0;
                padding: 0;
            }

            .header-menu li {
                list-style: none;
                padding: 10px 15px;
            }

            .header-menu a {
                text-decoration: none;
                color: #ddd;
                font-size: 14px;
                display: flex;
                align-items: center;
            }

            .header-menu a:hover {
                background: #222;
                color: dodgerblue;
            }

            .social-icons {
                display: flex;
            }

            .social-icon {
                width: 36px;
                height: 36px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                border-radius: 50%;
                background: #222; /* Dark background for icons */
                color: #bbb;
                transition: all 0.3s ease;
                font-size: 16px;
            }

            .social-icon:hover {
                background: #007bff;
                color: #fff;
            }

            .header-top-container {
                background: #111; /* Deep black background */
                border-bottom: 1px solid #222; /* Soft separator */
            }

            .top-message {
                color: #fff; /* White text for contrast */
                font-size: 14px;
            }

            .header-dropdown {
                position: relative;
                cursor: pointer;
            }

            .header-dropdown .header-menu {
                display: none;
                position: absolute;
                top: 100%;
                left: 0;
                min-width: 160px;
                background: #222; /* Dark semi-transparent dropdown */
                border-radius: 6px;
                padding: 8px 0;
                box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
                z-index: 999;
            }

            .header-dropdown:hover .header-menu {
                display: block;
            }

            .header-menu ul {
                margin: 0;
                padding: 0;
            }

            .header-menu li {
                list-style: none;
                padding: 10px 15px;
            }

            .header-menu a {
                text-decoration: none;
                color: #ddd; /* Light gray for readability */
                font-size: 14px;
                display: flex;
                align-items: center;
            }

            .social-icons {
                display: flex;
            }

            .social-icon {
                width: 36px;
                height: 36px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                border-radius: 50%;
                background: #222; /* Dark background for icons */
                color: #bbb; /* Light gray color */
                transition: all 0.3s ease;
                font-size: 16px;
            }

            .social-icon:hover {
                background: #007bff; /* Blue hover effect */
                color: #fff;
            }

        </style>
        <!-- Header Top Section - Social/Language at Top Right -->
        <div class="header-top">
            <div class="container">
                <div class="header-top-container py-2">
                    <div class="d-flex flex-wrap justify-content-between align-items-center">
                        @php
                            $topNotices = App\Models\TopNotice::where('is_active', true)->get();
                            $locale = app()->getLocale();
                        @endphp

                        @if($topNotices->count())
                            <!-- Main Top Notice Bar -->
                            <div class="top-notice-bar" id="top-notice">
                                <div class="notice-slider-container">
                                    <div class="notice-slider-track" id="notice-track"></div>
                                </div>
                                <div class="notice-progress-container">
                                    <div class="notice-progress" id="notice-progress"></div>
                                </div>
                            </div>

                            <!-- Header with Integrated Announcement -->
                            <header class="header">
                                <div class="header-top">
                                    <div class="container">
                                        <div class="header-top-container py-2">
                                            <div class="d-flex flex-wrap justify-content-between align-items-center">
                                                <!-- Announcement Message - Synced with Top Notice -->
                                                <div class="header-announcement" id="header-announcement">
                                                    @if(!empty($topNotices[0]->header_message_en) || !empty($topNotices[0]->header_message_ar))
                                                        <div class="announcement-content">
                                                            <svg class="announcement-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                                                <path d="M5 4v1h14V4H5zm0 3v8h2v-1h10v1h2V7H5zm2 9v1h6v-1H7z"/>
                                                            </svg>
                                                            <span class="announcement-text" id="announcement-text">
                                        {{ $locale === 'ar' ? $topNotices[0]->header_message_ar : $topNotices[0]->header_message_en }}
                                    </span>
                                                        </div>
                                                    @endif
                                                </div>

                                                <!-- Your existing header top right content here -->
                                                <div class="header-top-right">
                                                    <!-- ... your social/language links ... -->
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- ... rest of your header content ... -->
                            </header>

                            <script>
                                document.addEventListener("DOMContentLoaded", function () {
                                    const notices = @json($topNotices);
                                    if (notices.length === 0) return;

                                    // Top notice elements
                                    const noticeTrack = document.getElementById("notice-track");
                                    const progressBar = document.getElementById("notice-progress");
                                    const noticeContainer = document.getElementById("top-notice");

                                    // Announcement elements
                                    const announcementContainer = document.getElementById("header-announcement");
                                    const announcementText = document.getElementById("announcement-text");

                                    const locale = "{{ $locale }}";

                                    // Configuration
                                    const DISPLAY_DURATION = 3000;
                                    const ANIMATION_DURATION = 500;
                                    let currentIndex = 0;
                                    let rotationInterval;
                                    let progressStartTime;
                                    let progressRemaining = DISPLAY_DURATION;

                                    // Create notice elements
                                    function createNoticeElement(notice) {
                                        const noticeEl = document.createElement('div');
                                        noticeEl.className = 'notice-slide';

                                        noticeEl.innerHTML = `
                    <div class="container">
                        <div class="notice-content-wrapper">
                            <div class="notice-content">${locale === "ar" ? notice.content_ar : notice.content_en}</div>
                            <div class="notice-actions" id="cta-wrapper">
                                ${notice.cta_text_en && notice.cta_url ?
                                            `<a href="${notice.cta_url}" class="notice-btn btn-primary-reverse">
                                        ${locale === "ar" ? notice.cta_text_ar : notice.cta_text_en}
                                    </a>` : ''}
                                ${notice.cta_text_2_en && notice.cta_url_2 ?
                                            `<a href="${notice.cta_url_2}" class="notice-btn btn-outline">
                                        ${locale === "ar" ? notice.cta_text_2_ar : notice.cta_text_2_en}
                                    </a>` : ''}
                                ${(notice.limited_time_text_en || notice.limited_time_text_ar) ?
                                            `<div class="limited-time-badge">
                                        ${locale === "ar" ?
                                                (notice.limited_time_text_ar || notice.limited_time_text_en) :
                                                (notice.limited_time_text_en || notice.limited_time_text_ar)}
                                    </div>` : ''}
                            </div>
                        </div>
                    </div>
                `;

                                        return noticeEl;
                                    }

                                    // Update announcement content
                                    function updateAnnouncement(notice) {
                                        if (notice.header_message_en || notice.header_message_ar) {
                                            announcementText.textContent = locale === 'ar' ? notice.header_message_ar : notice.header_message_en;
                                            announcementContainer.style.display = 'block';

                                            // Add animation class
                                            announcementContainer.classList.add('announcement-update');
                                            setTimeout(() => {
                                                announcementContainer.classList.remove('announcement-update');
                                            }, 300);
                                        } else {
                                            announcementContainer.style.display = 'none';
                                        }
                                    }

                                    // Initialize slider
                                    function initSlider() {
                                        noticeTrack.innerHTML = '';

                                        notices.forEach(notice => {
                                            noticeTrack.appendChild(createNoticeElement(notice));
                                        });

                                        noticeTrack.children[0].classList.add('active');
                                        updateAnnouncement(notices[0]);
                                        startRotation();
                                    }

                                    function goToNextSlide() {
                                        const slides = noticeTrack.children;
                                        const currentSlide = slides[currentIndex];
                                        const nextIndex = (currentIndex + 1) % slides.length;
                                        const nextSlide = slides[nextIndex];
                                        const nextNotice = notices[nextIndex];

                                        currentSlide.classList.add('exiting');
                                        nextSlide.classList.add('entering');

                                        setTimeout(() => {
                                            currentSlide.classList.remove('active', 'exiting');
                                            nextSlide.classList.add('active');
                                            nextSlide.classList.remove('entering');

                                            // Update announcement when notice changes
                                            updateAnnouncement(nextNotice);

                                            currentIndex = nextIndex;
                                            resetProgress();
                                        }, ANIMATION_DURATION);
                                    }

                                    function startRotation() {
                                        stopRotation();
                                        resetProgress();

                                        rotationInterval = setInterval(() => {
                                            goToNextSlide();
                                        }, DISPLAY_DURATION + ANIMATION_DURATION);
                                    }

                                    function stopRotation() {
                                        clearInterval(rotationInterval);
                                        progressBar.style.transition = 'none';

                                        if (progressStartTime) {
                                            const elapsed = Date.now() - progressStartTime;
                                            progressRemaining = DISPLAY_DURATION - elapsed;
                                            progressBar.style.width = `${100 - (elapsed / DISPLAY_DURATION) * 100}%`;
                                        }
                                    }

                                    function resetProgress() {
                                        progressBar.style.transition = 'none';
                                        progressBar.style.width = '0%';

                                        void progressBar.offsetWidth;

                                        progressBar.style.transition = `width ${DISPLAY_DURATION}ms linear`;
                                        progressBar.style.width = '100%';
                                        progressStartTime = Date.now();
                                    }

                                    initSlider();

                                    noticeContainer.addEventListener('mouseenter', stopRotation);

                                    noticeContainer.addEventListener('mouseleave', () => {
                                        progressBar.style.transition = `width ${progressRemaining}ms linear`;
                                        progressBar.style.width = '100%';
                                        progressStartTime = Date.now();

                                        rotationInterval = setInterval(() => {
                                            goToNextSlide();
                                        }, progressRemaining + ANIMATION_DURATION);
                                    });

                                    noticeContainer.addEventListener('touchstart', stopRotation);
                                    noticeContainer.addEventListener('touchend', () => {
                                        progressBar.style.transition = `width ${progressRemaining}ms linear`;
                                        progressBar.style.width = '100%';
                                        progressStartTime = Date.now();

                                        rotationInterval = setInterval(() => {
                                            goToNextSlide();
                                        }, progressRemaining + ANIMATION_DURATION);
                                    });
                                });
                            </script>

                            <style>
                                /* Top Notice Bar Styles (unchanged from previous version) */
                                .top-notice-bar {
                                    position: relative;
                                    background: linear-gradient(135deg, #2b5876 0%, #4e4376 100%);
                                    color: white;
                                    padding: 12px 0;
                                    overflow: hidden;
                                    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
                                }

                                .notice-slider-container {
                                    position: relative;
                                    width: 100%;
                                    overflow: hidden;
                                    height: 44px;
                                }

                                /* ... (keep all your existing top notice bar styles exactly as before) ... */

                                /* Header Announcement Styles */
                                .header-announcement {
                                    display: flex;
                                    align-items: center;
                                    transition: all 0.3s ease;
                                }

                                .announcement-content {
                                    display: flex;
                                    align-items: center;
                                    gap: 8px;
                                    background-color: rgba(0, 0, 0, 0.2);
                                    padding: 4px 12px;
                                    border-radius: 4px;
                                }

                                .announcement-icon {
                                    width: 16px;
                                    height: 16px;
                                    color: #ffc107;
                                    flex-shrink: 0;
                                }

                                .announcement-text {
                                    font-size: 13px;
                                    font-weight: 600;
                                    color: #ffffff;
                                    text-transform: uppercase;
                                    transition: all 0.3s ease;
                                }

                                .announcement-update .announcement-text {
                                    animation: fadeIn 0.3s ease;
                                }

                                @keyframes fadeIn {
                                    0% { opacity: 0; transform: translateY(-3px); }
                                    100% { opacity: 1; transform: translateY(0); }
                                }

                                @media (max-width: 767.98px) {
                                    .announcement-content {
                                        padding: 3px 8px;
                                    }

                                    .announcement-text {
                                        font-size: 12px;
                                    }
                                }
                            </style>
                        @endif
                        <div class="d-flex align-items-center gap-3">
                            <!-- Language Selector -->
                            <div class="header-dropdown dropdown">
                                <a href="#" class="language-toggle d-flex align-items-center dropdown-toggle" id="languageDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="flag-{{ app()->getLocale() === 'ar' ? 'eg' : 'us' }} flag mr-2"></i>
                                    <span class="font-weight-bold">{{ app()->getLocale() === 'ar' ? 'العربية' : 'English' }}</span>
                                </a>

                                <div class="dropdown-menu" aria-labelledby="languageDropdown">
                                    <a href="{{ LaravelLocalization::getLocalizedURL('en', null, [], true) }}" class="dropdown-item d-flex align-items-center">
                                        <i class="flag-us flag mr-2"></i> English
                                        @if (app()->getLocale() === 'en')
                                            <i class="fas fa-check ml-auto text-success"></i>
                                        @endif
                                    </a>
                                    <a href="{{ LaravelLocalization::getLocalizedURL('ar', null, [], true) }}" class="dropdown-item d-flex align-items-center">
                                        <i class="flag-eg flag mr-2"></i> العربية
                                        @if (app()->getLocale() === 'ar')
                                            <i class="fas fa-check ml-auto text-success"></i>
                                        @endif
                                    </a>
                                </div>
                            </div>
                            <!-- Add this script to enable hover dropdown behavior -->
                            <script>
                                document.addEventListener("DOMContentLoaded", function () {
                                    var dropdown = document.querySelector('.header-dropdown');

                                    dropdown.addEventListener('mouseenter', function () {
                                        this.classList.add('show');
                                        this.querySelector('.dropdown-menu').classList.add('show');
                                    });

                                    dropdown.addEventListener('mouseleave', function () {
                                        this.classList.remove('show');
                                        this.querySelector('.dropdown-menu').classList.remove('show');
                                    });
                                });
                            </script>

                            <!-- Social Media Icons -->
                            <div class="social-icons d-flex gap-2">
                                @php
                                    $socialLinks = \App\Models\Setting::getSocialMediaLinks();
                                    $iconClasses = [
                                        'facebook'  => 'fab fa-facebook-f',
                                        'youtube'   => 'fab fa-youtube',
                                        'instagram' => 'fab fa-instagram',
                                        'x'         => 'fab fa-x-twitter',
                                        'snapchat'  => 'fab fa-snapchat-ghost',
                                        'tiktok'    => 'fab fa-tiktok',
                                    ];
                                @endphp
                                @foreach($socialLinks as $platform => $url)
                                    @if(!empty($url) && isset($iconClasses[$platform]))
                                        <a href="{{ $url }}" class="social-icon d-flex align-items-center justify-content-center" target="_blank" rel="noopener noreferrer">
                                            <i class="{{ $iconClasses[$platform] }}"></i>
                                        </a>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Header Middle Section - Logo, Search, Icons -->
            <div class="header-middle">
                <div class="container">
                    <div class="header-middle-container">
                        <!-- Logo (Left) -->
                        <div class="logo">
                            @if($logo)
                                <a href="{{ route('homepage') }}">
                                    <img src="{{ $logo }}" alt="{{ $siteName ?: config('app.name') }}" class="site-logo">
                                </a>
                            @else
                                <h1 class="website-name">{{ $siteName ?: config('app.name') }}</h1>
                            @endif
                        </div>

                        <!-- Right Section (Search, Contact, Icons) -->
                        <div class="header-middle-right" style="display: flex; justify-content: flex-end;">
                            <!-- Search Bar -->
                            <div class="header-search">
                                @livewire('light-global-search')
                            </div>

                            <!-- Contact Info -->
                            <div class="header-contact d-none d-lg-flex">
                                <div class="contact-icon">
                                    <i class="fas fa-phone-alt"></i>
                                </div>
                                <div class="contact-text">
                                    <span class="contact-label">{{ __('Call us now') }}</span>
                                    <a href="tel:{{ \App\Models\Setting::getContactDetails()['phone'] }}" class="contact-number">
                                        {{ \App\Models\Setting::getContactDetails()['phone'] }}
                                    </a>
                                </div>
                            </div>

                            <!-- Icons -->
                            <div class="header-icons">
                                <a href="{{ url('/client/login') }}" class="header-icon" title="Login">
                                    <i class="icon-user-2"></i>
                                </a>
                                <a href="{{ route('wishlist') }}" class="header-icon" title="Wishlist">
                                    <i class="icon-wishlist-2"></i>
                                    <span class="icon-badge">0</span>
                                </a>
                                <div class="dropdown cart-dropdown">
                                    @livewire('cart.cart-icon')
                                </div><!-- End .dropdown -->
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <style>
                /* Header Navigation Styles */
                .header-nav {
                    background-color: #ffffff;
                    box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
                    position: sticky;
                    top: 0;
                    z-index: 1000;
                }

                .main-nav {
                    display: flex;
                    justify-content: center;
                }

                .nav-menu {
                    display: flex;
                    list-style: none;
                    margin: 0;
                    padding: 0;
                    position: relative;
                }

                .nav-item {
                    position: relative;
                }

                .nav-link {
                    display: block;
                    padding: 1.2rem 1.5rem;
                    color: #333;
                    text-decoration: none;
                    font-weight: 600;
                    font-size: 1rem;
                    text-transform: uppercase;
                    letter-spacing: 0.5px;
                    transition: all 0.3s ease;
                    position: relative;
                }

                .nav-link:hover {
                    color: #4e4376;
                }

                .nav-item.active .nav-link {
                    color: #4e4376;
                }

                /* Underline animation */
                .nav-item:not(.active) .nav-link::after {
                    content: '';
                    position: absolute;
                    bottom: 1rem;
                    left: 1.5rem;
                    width: 0;
                    height: 2px;
                    background-color: #4e4376;
                    transition: width 0.3s ease;
                }

                .nav-item:not(.active) .nav-link:hover::after {
                    width: calc(100% - 3rem);
                }

                .nav-item.active .nav-link::before {
                    content: '';
                    position: absolute;
                    bottom: 1rem;
                    left: 1.5rem;
                    width: calc(100% - 3rem);
                    height: 2px;
                    background-color: #4e4376;
                }

                /* Mobile Menu */
                @media (max-width: 992px) {
                    .main-nav {
                        justify-content: flex-start;
                        overflow-x: auto;
                        -webkit-overflow-scrolling: touch;
                        scrollbar-width: none;
                    }

                    .main-nav::-webkit-scrollbar {
                        display: none;
                    }

                    .nav-menu {
                        flex-wrap: nowrap;
                        padding: 0 1rem;
                    }

                    .nav-link {
                        padding: 1rem 1.2rem;
                        font-size: 0.9rem;
                        white-space: nowrap;
                    }

                    .nav-link::after,
                    .nav-item.active .nav-link::before {
                        bottom: 0.8rem;
                        left: 1.2rem;
                    }

                    .nav-item:not(.active) .nav-link:hover::after,
                    .nav-item.active .nav-link::before {
                        width: calc(100% - 2.4rem);
                    }
                }

                /* Animation for active state */
                @keyframes fadeIn {
                    from { opacity: 0; transform: translateY(-5px); }
                    to { opacity: 1; transform: translateY(0); }
                }

                .nav-item.active {
                    animation: fadeIn 0.3s ease-out;
                }
            </style>

            <style>
                /* Modern Navigation Styles */
                .navbar {
                    background: linear-gradient(135deg, #2b5876 0%, #4e4376 100%) !important;
                    box-shadow: 0 4px 20px rgba(43, 88, 118, 0.2);
                    padding: 0.5rem 0;
                    transition: all 0.3s ease;
                }

                .navbar-brand {
                    font-family: 'Poppins', sans-serif;
                    font-weight: 700 !important;
                    font-size: 1.8rem !important;
                    background: linear-gradient(to right, #ffffff, #e0e9ff);
                    -webkit-background-clip: text;
                    background-clip: text;
                    -webkit-text-fill-color: transparent;
                    letter-spacing: 0.5px;
                    margin-right: 2rem;
                    transition: all 0.3s ease;
                }

                .navbar-brand:hover {
                    transform: translateY(-1px);
                }

                .navbar-nav {
                    gap: 1.5rem !important;
                }

                .nav-link {
                    font-family: 'Poppins', sans-serif;
                    font-weight: 500 !important;
                    font-size: 1.1rem !important;
                    color: rgba(255, 255, 255, 0.9) !important;
                    letter-spacing: 0.3px;
                    padding: 0.5rem 0 !important;
                    position: relative;
                    transition: all 0.3s ease;
                }

                .nav-link::before {
                    content: '';
                    position: absolute;
                    bottom: 0;
                    left: 0;
                    width: 0;
                    height: 3px;
                    background: linear-gradient(to right, #ffffff, #a7c4ff);
                    border-radius: 3px;
                    transition: width 0.4s cubic-bezier(0.22, 0.61, 0.36, 1);
                }

                .nav-link:hover {
                    color: white !important;
                    transform: translateY(-2px);
                }

                .nav-link:hover::before {
                    width: 100%;
                }

                .nav-link.active {
                    color: white !important;
                    font-weight: 600 !important;
                }

                .nav-link.active::before {
                    width: 100%;
                    background: white;
                }

                .navbar-toggler {
                    border: none;
                    padding: 0.5rem;
                }

                .navbar-toggler:focus {
                    box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.25);
                }

                .navbar-toggler-icon {
                    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255, 255, 255, 0.9%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
                }

                /* Mobile menu styles */
                @media (max-width: 991.98px) {
                    .navbar {
                        padding: 0.75rem 0;
                    }

                    .navbar-collapse {
                        background: linear-gradient(135deg, #2b5876 0%, #4e4376 100%);
                        padding: 1.5rem;
                        margin-top: 0.5rem;
                        border-radius: 0.5rem;
                        box-shadow: 0 10px 30px rgba(43, 88, 118, 0.3);
                    }

                    .navbar-nav {
                        gap: 1rem !important;
                    }

                    .nav-link {
                        padding: 0.75rem 1rem !important;
                        font-size: 1.05rem !important;
                    }

                    .nav-link::before {
                        height: 2px;
                        bottom: 0.5rem;
                    }
                }

                /* Animation for active item */
                @keyframes pulse {
                    0% { transform: translateY(0); }
                    50% { transform: translateY(-3px); }
                    100% { transform: translateY(0); }
                }

                .nav-item.active .nav-link {
                    animation: pulse 0.5s ease;
                }
            </style>
            <nav class="navbar navbar-expand-lg navbar-dark py-2">
                <div class="container">
                    <a class="navbar-brand" href="/">Home</a>
                    <button class="navbar-toggler nav" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
                        <ul class="navbar-nav gap-4">
                            <li class="nav-item">
                                <a class="nav-link" href="category.html">Categories</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="product.html">Products</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="blog.html">Blog</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="contact.html">Contact</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
        </div>
    </header>

    <!-- Mobile Menu -->
    <div class="mobile-menu-container">
        <div class="mobile-menu-wrapper">
            <span class="mobile-menu-close"><i class="fas fa-times"></i></span>
            <nav class="mobile-nav">
                <ul class="mobile-menu">
                    <li class="active"><a href="/">Home</a></li>
                    <li>
                        <a href="category.html">Categories</a>
                    </li>
                    <li>
                        <a href="product.html">Products</a>
                    </li>
                    <li>
                        <a href="#">Pages</a>
                        <ul>
                            <li><a href="{{ route('wishlist') }}">Wishlist</a></li>
                            <li><a href="cart.html">Shopping Cart</a></li>
                            <li><a href="checkout.html">Checkout</a></li>
                            <li><a href="dashboard.html">Dashboard</a></li>
                            <li><a href="about.html">About Us</a></li>
                            <li>
                                <a href="#">Blog</a>
                                <ul>
                                    <li><a href="blog.html">Blog List</a></li>
                                    <li><a href="single.html">Blog Post</a></li>
                                </ul>
                            </li>
                            <li><a href="contact.html">Contact Us</a></li>
                        </ul>
                    </li>
                    <li><a href="blog.html">Blog</a></li>
                    <li><a href="contact.html">Contact Us</a></li>
                </ul>
            </nav>

            <div class="social-icons p-4 text-center">
                @foreach($socialLinks as $platform => $url)
                    @if(!empty($url) && isset($iconClasses[$platform]))
                        <a href="{{ $url }}" class="social-icon mx-2" target="_blank" rel="noopener noreferrer">
                            <i class="{{ $iconClasses[$platform] }} fa-lg"></i>
                        </a>
                    @endif
                @endforeach
            </div>
        </div>
    </div>

    <main class="{{ $mainClass ?? 'main' }}">
        @yield('content')
    </main>

    <footer class="footer bg-dark">
        <div class="footer-middle">
            <div class="container">
                <div class="row">
                    <div class="col-lg-3 col-sm-6">
                        <div class="widget">
                            <h4 class="widget-title">Contact Info</h4>
                            <ul class="contact-info">
                                <li>
                                    <span class="contact-info-label">Address:</span>123 Street Name, City, England
                                </li>
                                <li>
                                    <span class="contact-info-label">Phone:</span><a href="tel:">(123)
                                        456-7890</a>
                                </li>
                                <li>
                                    <span class="contact-info-label">Email:</span> <a
                                        href="mailto:mail@example.com">mail@example.com</a>
                                </li>
                                <li>
                                    <span class="contact-info-label">Working Days/Hours:</span>
                                    Mon - Sun / 9:00 AM - 8:00 PM
                                </li>
                            </ul>
                            <div class="social-icons">
                                @foreach($socialLinks as $platform => $url)
                                    @if(!empty($url) && isset($iconClasses[$platform]))
                                        <a href="{{ $url }}" class="social-icon" target="_blank">
                                            <i class="{{ $iconClasses[$platform] }}"></i>
                                        </a>
                                    @endif
                                @endforeach
                            </div>
                        </div><!-- End .widget -->
                    </div><!-- End .col-lg-3 -->

                    <div class="col-lg-3 col-sm-6">
                        <div class="widget">
                            <h4 class="widget-title">Popular Tags</h4>

                            <div class="tagcloud">
                                <a href="#">Bag</a>
                            </div>
                        </div><!-- End .widget -->
                    </div><!-- End .col-lg-3 -->

                    <div class="col-lg-3 col-sm-6">
                        <div class="widget widget-newsletter">
                            <h4 class="widget-title">Subscribe newsletter</h4>
                            <p>Get all the latest information on events, sales and offers. Sign up for newsletter:
                            </p>
                            <form action="#" class="mb-0">
                                <input type="email" class="form-control m-b-3" placeholder="Email address">

                                <input type="submit" class="btn btn-primary shadow-none" value="Subscribe">
                            </form>
                        </div><!-- End .widget -->
                    </div><!-- End .col-lg-3 -->
                </div><!-- End .row -->
            </div><!-- End .container -->
        </div><!-- End .footer-middle -->

        <div class="container">
            <div class="footer-bottom">
                <div class="container d-sm-flex align-items-center">
                    <div class="footer-left">
                        <span class="footer-copyright">© Piky Host eCommerce. 2021. All Rights Reserved</span>
                    </div>

                    <div class="footer-right ml-auto mt-1 mt-sm-0">
                        <div class="payment-icons">
								<span class="payment-icon visa"
                                      style="background-image: url(assets/images/payments/payment-visa.svg)"></span>
                            <span class="payment-icon paypal"
                                  style="background-image: url(assets/images/payments/payment-paypal.svg)"></span>
                            <span class="payment-icon stripe"
                                  style="background-image: url(assets/images/payments/payment-stripe.png)"></span>
                            <span class="payment-icon verisign"
                                  style="background-image:  url(assets/images/payments/payment-verisign.svg)"></span>
                        </div>
                    </div>
                </div>
            </div><!-- End .footer-bottom -->
        </div><!-- End .container -->
    </footer><!-- End .footer -->
</div><!-- End .page-wrapper -->

<div class="mobile-menu-overlay"></div><!-- End .mobil-menu-overlay -->

<div class="mobile-menu-container">
    <div class="mobile-menu-wrapper">
        <span class="mobile-menu-close"><i class="fa fa-times"></i></span>
        <nav class="mobile-nav">$
            <ul class="mobile-menu">
                <li><a href="/">Home</a></li>
                <li>
                    <a href="">Categories</a>
                </li>
                <li>
                    <a href="product.html">Products</a>
                </li>
                <li>
                    <a href="#">Pages<span class="tip tip-hot">Hot!</span></a>
                    <ul>
                        <li>
                            <a href="{{ route('wishlist') }}">Wishlist</a>
                        </li>
                        <li>
                            <a href="cart.html">Shopping Cart</a>
                        </li>
                        <li>
                            <a href="checkout.html">Checkout</a>
                        </li>
                        <li>
                            <a href="dashboard.html">Dashboard</a>
                        </li>
                        <li>
                            <a href="login.html">Login</a>
                        </li>
                        <li>
                            <a href="forgot-password.html">Forgot Password</a>
                        </li>
                    </ul>
                </li>
                <li><a href="blog.html">Blog</a></li>
                <li><a href="#">Elements</a>
                    <ul class="custom-scrollbar">
                        <li><a href="element-accordions.html">Accordion</a></li>
                    </ul>
                </li>
            </ul>


            <ul class="mobile-menu">
                <li><a href="login.html">My Account</a></li>
                <li><a href="contact.html">Contact Us</a></li>
                <li><a href="blog.html">Blog</a></li>
                <li><a href="{{ route('wishlist') }}">My Wishlist</a></li>
                <li><a href="cart.html">Cart</a></li>
                <li><a href="login.html" class="login-link">Log In</a></li>
            </ul>
        </nav><!-- End .mobile-nav -->

        <form class="search-wrapper mb-2" action="#">
            <input type="text" class="form-control mb-0" placeholder="Search..." required />
            <button class="btn icon-search text-white bg-transparent p-0" type="submit"></button>
        </form>

        <div class="social-icons">
            @foreach($socialLinks as $platform => $url)
                @if(!empty($url) && isset($iconClasses[$platform]))
                    <a href="{{ $url }}" class="social-icon" target="_blank">
                        <i class="{{ $iconClasses[$platform] }}"></i>
                    </a>
                @endif
            @endforeach
        </div>
    </div><!-- End .mobile-menu-wrapper -->
</div><!-- End .mobile-menu-container -->

<div class="sticky-navbar">
    <div class="sticky-info">
        <a href="/">
            <i class="icon-home"></i>Home
        </a>
    </div>
    <div class="sticky-info">
        <a href="/categoires" class="">
            <i class="icon-bars"></i>Categories
        </a>
    </div>
    <div class="sticky-info">
        <a href="{{ route('wishlist') }}" class="">
            <i class="icon-wishlist-2"></i>Wishlist
        </a>
    </div>
    <div class="sticky-info">
        <a href="login.html" class="">
            <i class="icon-user-2"></i>Account
        </a>
    </div>
    <div class="sticky-info">
        <a href="cart.html" class="">
            <i class="icon-shopping-cart position-relative">
                <span class="cart-count badge-circle">3</span>
            </i>Cart
        </a>
    </div>
</div>

<a id="scroll-top" href="#top" title="Top" role="button"><i class="icon-angle-up"></i></a>

<!-- Plugins JS File -->
<script src="assets/js/jquery.min.js"></script>
<script src="assets/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/plugins.min.js"></script>

<!-- Main JS File -->
<script src="assets/js/main.min.js"></script>
@livewireScripts
</body>

</html>

