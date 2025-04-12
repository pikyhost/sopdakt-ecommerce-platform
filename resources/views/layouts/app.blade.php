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
    $user = auth()->user();
    $isGuest = auth()->guest();
    $isClient = $user && $user->hasRole('client'); // Assuming Spatie Laravel Permissions
@endphp

    <!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>@yield('title', 'Default Title')</title>

    <meta name="keywords" content="HTML5 Template" />
    <meta name="description" content="{{ $siteName }} - Bootstrap eCommerce Template">
    <meta name="author" content="SW-THEMES">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ $favicon }}">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <script async src="https://www.googletagmanager.com/gtag/js?id=G-F5CFYBRQ0F"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'G-F5CFYBRQ0F');
    </script>

    <!-- Ensure correct asset loading for dynamic routes -->
    <base href="{{ url('/') }}/">

    <script>
        WebFontConfig = {
            google: {
                families: ['Open+Sans:300,400,600,700,800', 'Poppins:200,300,400,500,600,700,800', 'Oswald:300,400,500,600,700,800']
            }
        };
        (function(d) {
            var wf = d.createElement('script'),
                s = d.scripts[0];
            wf.src = 'assets/js/webfont.js';
            wf.async = true;
            s.parentNode.insertBefore(wf, s);
        })(document);
    </script>

    <!-- Plugins CSS File -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">

    <!-- Main CSS File -->
    <link rel="stylesheet" href="assets/css/demo18.min.css">
    <link rel="stylesheet" type="text/css" href="assets/vendor/fontawesome-free/css/all.min.css">
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
                const DISPLAY_DURATION = 5000;  // Increased from 3000 for better readability
                const ANIMATION_DURATION = 500;
                let currentIndex = 0;
                let rotationInterval;
                let progressInterval;
                let isPaused = false;

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

                    const slides = noticeTrack.children;
                    if (slides.length > 0) {
                        slides[0].classList.add('active');
                        startProgressBar();
                        startRotation();
                    }
                }

                function goToNextSlide() {
                    const slides = noticeTrack.children;
                    if (slides.length <= 1) return;

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
                        resetProgressBar();
                    }, ANIMATION_DURATION);
                }

                function startRotation() {
                    stopRotation();
                    rotationInterval = setInterval(goToNextSlide, DISPLAY_DURATION + ANIMATION_DURATION);
                }

                function stopRotation() {
                    clearInterval(rotationInterval);
                    clearInterval(progressInterval);
                    isPaused = true;
                }

                function startProgressBar() {
                    let startTime = Date.now();
                    const endTime = startTime + DISPLAY_DURATION;

                    progressBar.style.transition = `width ${DISPLAY_DURATION}ms linear`;
                    progressBar.style.width = '100%';

                    progressInterval = setInterval(() => {
                        const now = Date.now();
                        if (now >= endTime) {
                            clearInterval(progressInterval);
                        }
                    }, 100);
                }

                function resetProgressBar() {
                    progressBar.style.transition = 'none';
                    progressBar.style.width = '0%';

                    // Force reflow
                    void progressBar.offsetWidth;

                    startProgressBar();
                }

                function resumeRotation() {
                    if (isPaused) {
                        startRotation();
                        isPaused = false;
                    }
                }

                // Initialize the slider
                initSlider();

                // Event listeners
                noticeContainer.addEventListener('mouseenter', stopRotation);
                noticeContainer.addEventListener('mouseleave', resumeRotation);
                noticeContainer.addEventListener('touchstart', stopRotation);
                noticeContainer.addEventListener('touchend', resumeRotation);
            });
        </script>

        <style>
            .top-notice-bar {
                position: relative;
                background: #000;
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
                min-height: 44px;
            }

            .notice-slider-track {
                position: relative;
                width: 100%;
            }

            .notice-slide {
                position: absolute;
                width: 100%;
                top: 0;
                left: 0;
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
                color: #000;
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
                transition: width 100ms linear;
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

    <header class="header header-transparent">
        <div class="header-middle sticky-header">
            <div class="container-fluid">
                <div class="header-left">
                    <button class="mobile-menu-toggler text-white mr-2" type="button">
                        <i class="fas fa-bars"></i>
                    </button>
                    <style>
                        .website-name {
                            font-size: 26px;
                            font-weight: bold;
                            font-family: 'Poppins', sans-serif;
                            text-transform: uppercase;
                            letter-spacing: 1.2px;
                            text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.2);
                            background: linear-gradient(45deg, #1877F2, #1DA1F2, #E4405F);
                            -webkit-background-clip: text;
                            -webkit-text-fill-color: transparent;
                            display: block;
                            width: 100%;
                            white-space: normal; /* Allows text to wrap instead of cutting */
                            overflow: visible; /* Ensures full visibility */
                            word-break: break-word; /* Ensures long names break properly */
                        }
                    </style>

                    @if($logo)
                        <a href="{{ route('homepage') }}" class="logo">
                            <div class="logo-wrapper">
                                <img src="{{ $logo }}" alt="Site Logo" class="site-logo">
                            </div>
                        </a>
                    @else
                        <div class="site-name-wrapper">
                            <p class="website-name">{{ $siteName ?: __('No Logo Available') }}</p>
                        </div>
                    @endif
                </div>
                <!-- End .header-left -->

                <div class="header-center justify-content-between">
                    <nav class="main-nav w-100">
                        <ul class="menu">
                            <li class="active">
                                <a href="{{ url('/') }}">Home</a>
                            </li>
                            <li>
                                <a href="{{ url('/categories') }}">Categories</a>
                                <div class="megamenu megamenu-fixed-width megamenu-3cols">
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <a href="{{ url('/categories') }}" class="nolink">Categories Pages</a>
                                            <ul class="submenu">
                                                @foreach (\App\Models\Category::latest()->take(18)->get() as $category)
                                                    <li>
                                                        <a href="{{ route('category.products',  $category->slug) }}">{{ $category->name }}</a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>

                                        <!-- Dynamic Menu Banner -->
                                        @php
                                            $banner = \App\Models\Banner::where('type', 'category')->first();
                                            $locale = app()->getLocale();
                                        @endphp

                                        @if($banner)
                                            <div class="col-lg-4 p-0">
                                                <div class="menu-banner menu-banner-2">
                                                    <figure>
                                                        <img src="{{ \Illuminate\Support\Facades\Storage::url($banner->image) }}"
                                                             alt="Menu banner"
                                                             class="product-promo"
                                                             width="380" height="790">
                                                    </figure>
                                                    <div class="banner-content">
                                                        <h4>
                                                            <span class="">{{ $banner->getTranslation('subtitle', $locale) }}</span><br />
                                                            <b class="">{{ $banner->getTranslation('discount', $locale) }}</b>
                                                        </h4>
                                                    </div>
                                                    <a href="{{ $banner->button_url }}" class="btn btn-sm btn-dark">
                                                        {{ $banner->getTranslation('button_text', $locale) }}
                                                    </a>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </li>

                            <li>
                                <a href="{{ url('products') }}">Products</a>
                                <div class="megamenu megamenu-fixed-width">
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <a href="{{ url('products') }}" class="nolink">Products Pages</a>
                                            <ul class="submenu">
                                                @foreach (\App\Models\Product::latest()->take(18)->get() as $product)
                                                    <li>
                                                        <a href="{{ route('product.show', $product->slug) }}">{{ $product->name }}</a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>

                                        <!-- Dynamic Menu Banner -->
                                        @php
                                            $banner = \App\Models\Banner::where('type', 'product')->first();
                                            $locale = app()->getLocale();
                                        @endphp

                                        @if($banner)
                                            <div class="col-lg-4 p-0">
                                                <div class="menu-banner menu-banner-2">
                                                    <figure>
                                                        <img src="{{ \Illuminate\Support\Facades\Storage::url($banner->image) }}"
                                                             alt="Menu banner"
                                                             class="product-promo"
                                                             width="380" height="790">
                                                    </figure>
                                                    <div class="banner-content">
                                                        <h4>
                                                            <span class="">{{ $banner->getTranslation('subtitle', $locale) }}</span><br />
                                                            <b class="">{{ $banner->getTranslation('discount', $locale) }}</b>
                                                        </h4>
                                                    </div>
                                                    <a href="{{ $banner->button_url }}" class="btn btn-sm btn-dark">
                                                        {{ $banner->getTranslation('button_text', $locale) }}
                                                    </a>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </li>

                            <li class="d-none d-xl-block">
                                <a href="#">Pages</a>
                                <ul>
                                    <li><a href="{{ route('wishlist') }}">Wishlist</a></li>
                                    <li><a href="{{ url('/cart') }}">Shopping Cart</a></li>
                                    <li><a href="{{ url('/checkout') }}">Checkout</a></li>
                                    <li>
                                        <a href="{{ $isGuest ? url('/client/login') : ($isClient ? url('/client') : url('/admin')) }}">
                                            {{ $isGuest ? 'Login' : 'Dashboard' }}
                                        </a>
                                    </li>
                                    <li><a href="{{ url('/about-us') }}">About Us</a></li>
                                    <li><a href="{{url('/blogs')}}">Blogs</a></li>
                                    <li><a href="{{ route('privacy.policy') }}">Privacy Policy</a></li>
                                    <li><a href="{{ route('refund.policy') }}">Refund Policy</a></li>
                                    <li><a href="{{ route('terms.of.service') }}">Terms of Service</a></li>
                                    <li><a href="{{ route('contact.us') }}">Contact Us</a></li>
                                </ul>
                            </li>

                            <li><a href="{{ url('/blogs') }}">Blogs</a></li>
                        </ul>
                    </nav>
                </div>
                <!-- End .header-center -->

                <div class="header-right justify-content-end">
                    <div class="header-dropdowns">
                        <!-- End .header-dropown -->

                        <div class="header-dropdown">
                            <a href="#">
                                <i class="flag-{{ app()->getLocale() === 'ar' ? 'eg' : 'us' }} flag"></i>
                                {{ app()->getLocale() === 'ar' ? 'العربية' : 'English' }}
                            </a>
                            <div class="header-menu">
                                <ul>
                                    <li>
                                        <a href="{{ LaravelLocalization::getLocalizedURL('en', null, [], true) }}">
                                            <i class="flag-us flag mr-2"></i> English
                                            @if (app()->getLocale() === 'en')
                                                <i class="fas fa-check text-sm ml-1"></i>
                                            @endif
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ LaravelLocalization::getLocalizedURL('ar', null, [], true) }}">
                                            <i class="flag-eg flag mr-2"></i> العربية
                                            @if (app()->getLocale() === 'ar')
                                                <i class="fas fa-check text-sm ml-1"></i>
                                            @endif
                                        </a>
                                    </li>
                                </ul>
                            </div><!-- End .header-menu -->
                        </div><!-- End .header-dropdown -->

                        <!-- End .header-dropown -->
                    </div>
                    <!-- End .header-dropdowns -->

                    <a href="{{ url('/client/login') }}" class="header-icon" title="login"><i class="icon-user-2"></i></a>

                    <a href="{{route('wishlist') }}" class="header-icon" title="Wishlist"><i class="icon-wishlist-2">
                        </i></a>

                    @livewire('global-search')
                    <!-- End .header-search -->

                    <div class="dropdown cart-dropdown">
                        @livewire('cart.cart-icon')
                    </div><!-- End .dropdown -->
                    <!-- End .dropdown -->
                </div>
                <!-- End .header-right -->
            </div>
            <!-- End .container-fluid -->
        </div>
        <!-- End .header-middle -->
    </header>
    <!-- End .header -->

    <main class="{{ $mainClass ?? 'main' }}">
        @yield('content')
    </main>

    <footer class="footer font2">
        <div class="container-fluid">
            <div class="footer-top top-border d-flex align-items-center justify-content-between flex-wrap">
                <div class="footer-left widget-newsletter d-md-flex align-items-center">
                    <div class="widget-newsletter-info">
                        <h5 class="widget-newsletter-title text-white text-uppercase ls-0 mb-0">subscribe newsletter
                        </h5>
                        <p class="widget-newsletter-content mb-0">Get all the latest information on Events, Sales and Offers.</p>
                    </div>
                    <form id="subscriptionForm">
                        <div class="footer-submit-wrapper d-flex">
                            <input type="email" id="emailInput" class="form-control" placeholder="Email address..." size="40" required>
                            <button type="submit" class="btn btn-dark btn-sm">Subscribe</button>
                        </div>
                    </form>

                    <script>
                        document.getElementById('subscriptionForm').addEventListener('submit', function(event) {
                            event.preventDefault(); // Prevent default form submission
                            let email = document.getElementById('emailInput').value.trim();
                            if (email) {
                                window.location.href = `/client/register?email=` + encodeURIComponent(email);
                            }
                        });
                    </script>
                </div>
                <div class="footer-right">
                    <div class="social-icons">
                        @foreach($socialLinks as $platform => $url)
                            @if(!empty($url) && isset($iconClasses[$platform]))
                                <a href="{{ $url }}" class="social-icon" target="_blank">
                                    <i class="{{ $iconClasses[$platform] }}"></i>
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="footer-middle">
                <div class="row">
                    <div class="col-lg-3">
                        <div class="site-name-wrapper">
                            <p class="website-name">{{ $siteName ?: __('No Logo Available') }}</p>
                        </div>

                        <p class="footer-desc">Lorem ipsum dolor sit amet, consectetur adipis.</p>

                        <div class="ls-0 footer-question">
                            <h6 class="mb-0 text-white">QUESTIONS?</h6>
                            <h3 class="mb-3 text-white">
                                <a href="tel:{{ \App\Models\Setting::getContactDetails()['phone'] }}">
                                    {{ \App\Models\Setting::getContactDetails()['phone'] }}
                                </a>
                            </h3>
                        </div>
                    </div>
                    <!-- End .col-lg-3 -->

                    <div class="col-lg-3">
                        <div class="widget">
                            <h4 class="widget-title">Account</h4>

                            <div class="row">
                                <div class="col-md-6">
                                    <ul class="links">
                                        <li><a href="demo18-about.html">About us</a></li>
                                        <li><a href="demo18-contact.html">Contact us</a></li>
                                        <li><a href="dashboard.html">My Account</a></li>
                                        <li><a href="#">Payment Methods</a></li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <ul class="links">
                                        <li><a href="order.html">Order history</a></li>
                                        <li><a href="#">Advanced search</a></li>
                                        <li><a href="login.html">Login</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <!-- End .widget -->
                    </div>
                    <!-- End .col-lg-3 -->

                    <div class="col-lg-3">
                        <div class="widget">
                            <h4 class="widget-title">About</h4>

                            <div class="row">
                                <div class="col-md-6">
                                    <ul class="links">
                                        <li><a href="demo18-about.html">About {{ $siteName }}</a></li>
                                        <li><a href="#">Our Guarantees</a></li>
                                        <li><a href="#">Terms And Conditions</a></li>
                                        <li><a href="#">Privacy Policy</a></li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <ul class="links">
                                        <li><a href="#">Return Policy</a></li>
                                        <li><a href="#">Intellectual Property Claims</a></li>
                                        <li><a href="#">Site Map</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <!-- End .widget -->
                    </div>
                    <!-- End .col-lg-3 -->

                    <div class="col-lg-3">
                        <div class="widget text-lg-right">
                            <h4 class="widget-title">Features</h4>

                            <ul class="links">
                                <li><a href="#">Powerful Admin Panel</a></li>
                                <li><a href="#">Mobile &amp; Retina Optimized</a></li>
                                <li><a href="#">Super Fast HTML Template</a></li>
                            </ul>
                        </div>
                        <!-- End .widget -->
                    </div>
                    <!-- End .col-lg-3 -->
                </div>
                <!-- End .row -->
            </div>
            <div class="footer-bottom">
                <p class="footer-copyright text-lg-center mb-0">&copy; {{ $siteName }} . 2025. All Rights Reserved
                </p>
            </div>
            <!-- End .footer-bottom -->
        </div>
        <!-- End .container-fluid -->
    </footer>
    <!-- End .footer -->
</div>
<!-- End .page-wrapper -->

<div class="loading-overlay">
    <div class="bounce-loader">
        <div class="bounce1"></div>
        <div class="bounce2"></div>
        <div class="bounce3"></div>
    </div>
</div>

<div class="mobile-menu-overlay"></div>
<!-- End .mobil-menu-overlay -->

<div class="mobile-menu-container">
    <div class="mobile-menu-wrapper">
        <span class="mobile-menu-close"><i class="fa fa-times"></i></span>
        <nav class="mobile-nav">
            <ul class="mobile-menu">
                <li><a href="{{ url('/') }}">Home</a></li>

                <!-- Categories -->
                <li>
                    <a href="{{ url('/categories') }}">Categories</a>
                    <ul>
                        @foreach (\App\Models\Category::latest()->take(18)->get() as $category)
                            <li>
                                <a href="{{ route('category.products', $category->slug) }}">{{ $category->name }}</a>
                            </li>
                        @endforeach
                    </ul>
                </li>

                <!-- Products -->
                <li>
                    <a href="{{ url('/products') }}">Products</a>
                    <ul>
                        @foreach (\App\Models\Product::latest()->take(18)->get() as $product)
                            <li>
                                <a href="{{ route('product.show', $product->slug) }}">{{ $product->name }}</a>
                            </li>
                        @endforeach
                    </ul>
                </li>

                <!-- Pages -->
                <li>
                    <a href="#">Pages</a>
                    <ul>
                        <li><a href="{{ route('wishlist') }}">Wishlist</a></li>
                        <li><a href="{{ url('/cart') }}">Shopping Cart</a></li>
                        <li><a href="{{ url('/checkout') }}">Checkout</a></li>
                        <li>
                            <a href="{{ $isGuest ? url('/client/login') : ($isClient ? url('/client') : url('/admin')) }}">
                                {{ $isGuest ? 'Login' : 'Dashboard' }}
                            </a>
                        </li>
                        <li><a href="{{ url('/about-us') }}">About Us</a></li>
                        <li><a href="{{ url('/blogs') }}">Blog</a></li>
                        @if($isGuest)
                            <li><a href="{{ url('/client/login') }}">Login</a></li>
                        @elseif(!$isClient)
                            <li><a href="{{ url('/admin/login') }}">Login</a></li>
                        @endif                        <li><a href="{{ route('privacy.policy') }}">Privacy Policy</a></li>
                        <li><a href="{{ route('refund.policy') }}">Refund Policy</a></li>
                        <li><a href="{{ route('terms.of.service') }}">Terms of Service</a></li>
                        <li><a href="{{ route('contact.us') }}">Contact Us</a></li>
                    </ul>
                </li>

                <!-- Blog -->
                <li><a href="{{ url('/blogs') }}">Blog</a></li>
            </ul>

            <ul class="mobile-menu mt-2">
                <li>
                    <a href="{{ url($isClient ? 'client/my-profile' : 'admin/my-profile') }}">
                        My Account
                    </a>
                </li>
                <li><a href="{{ route('contact.us') }}">Contact Us</a></li>
                <li><a href="{{ route('wishlist') }}">My Wishlist</a></li>
                <li><a href="{{ url('cart') }}">Cart</a></li>
                @if($isGuest)
                    <li><a href="{{ url('/client/login') }}">Login</a></li>
                @elseif(!$isClient)
                    <li><a href="{{ url('/admin/login') }}">Login</a></li>
                @endif                <li><a href="{{ url('client/password-reset/request') }}">Forgot Password</a></li>
            </ul>
        </nav>

        <!-- Social Icons -->
        <div class="social-icons">
            @foreach($socialLinks as $platform => $url)
                @if(!empty($url) && isset($iconClasses[$platform]))
                    <a href="{{ $url }}" class="social-icon" target="_blank">
                        <i class="{{ $iconClasses[$platform] }}"></i>
                    </a>
                @endif
            @endforeach
        </div>
    </div>
</div>
<!-- End .mobile-menu-container -->

<div class="sticky-navbar">
    <div class="sticky-info">
        <a href="{{ url('/') }}">
            <i class="icon-home"></i>Home
        </a>
    </div>
    <div class="sticky-info">
        <a href="{{ url('/categories') }}" class="">
            <i class="icon-bars"></i>Categories
        </a>
    </div>
    <div class="sticky-info">
        <a href="{{ route('wishlist') }}" class="">
            <i class="icon-wishlist-2"></i>Wishlist
        </a>
    </div>
    <div class="sticky-info">
        <a href="{{ url($isClient ? 'client/my-profile' : 'admin/my-profile') }}" class="">
            <i class="icon-user-2"></i> Account
        </a>
    </div>
    <div class="sticky-info">
        <a href="{{ url('cart') }}" class="">
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
<script src="assets/js/jquery.appear.min.js"></script>

<!-- Main JS File -->
<script src="assets/js/main.min.js"></script>
@livewireScripts
</body>
</html>
