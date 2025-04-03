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
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>{{ $siteName }}</title>

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
                                            <a href="#" class="nolink">Categories Pages</a>
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
                                    <li><a href="{{ url('/client') }}">Dashboard</a></li>
                                    <li><a href="{{ url('/about-us') }}">About Us</a></li>
                                    <li><a href="{{url('/blogs')}}">Blog</a></li>
                                    <li><a href="{{ url('/client/login') }}">Login</a></li>
                                    <li><a href="{{ route('privacy.policy') }}">Privacy Policy</a></li>
                                    <li><a href="{{ route('refund.policy') }}">Refund Policy</a></li>
                                    <li><a href="{{ route('terms.of.service') }}">Terms of Service</a></li>
                                    <li><a href="{{ route('contact.us') }}">Contact Us</a></li>
                                </ul>
                            </li>

                            <li><a href="{{ url('/blogs') }}">Blog</a></li>
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
                        <div class="header-dropdown">
                            <a href="#">LINKS</a>
                            <div class="header-menu">
                                <ul>
                                    <li><a href="{{ url('/about-us') }}">ABOUT US</a>
                                    </li>
                                    <li><a href="{{ url('/contact-us') }}">CONTACT US</a></li>
                                </ul>
                            </div>
                            <!-- End .header-menu -->
                        </div>
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

    <main class="main">
        @php
            $homeSettings = \App\Models\HomePageSetting::getCached();
        @endphp

        @if($homeSettings)
            <section class="home-slider-container">
                <div class="home-slider owl-carousel with-dots-container"
                     data-owl-options='{"dots": true, "loop": true, "autoplay": true, "autoplayTimeout": 5000, "animateOut": "fadeOut"}'>

                    <!-- Slider 1 -->
                    <div class="home-slide home-slide1 banner" style="background-color: #111">
                        <div class="slide-bg" style="background-image: url('{{ $homeSettings->getSlider1ImageUrl() ?? asset('assets/images/demoes/demo18/slider/home-slide-back.jpg') }}');"></div>
                        <div class="home-slide-content">
                            <h2 class="text-white text-transform-uppercase line-height-1">{{ $homeSettings->main_heading ?? 'Spring / Summer Season' }}</h2>
                            <h3 class="text-white d-inline-block line-height-1 ls-0 text-center">{{ $homeSettings->discount_text ?? 'Up to' }}</h3>
                            <h4 class="text-white text-uppercase line-height-1 d-inline-block">{{ $homeSettings->discount_value  }}</h4>
                            <h5 class="float-left text-white text-uppercase line-height-1 ls-n-20">
                                {{ __('Starting At') }}
                            </h5>
                            <h6 class="float-left coupon-sale-text line-height-1 ls-n-20 font-weight-bold text-secondary">
                                <sup>{{ $homeSettings->currency_symbol ?? '$' }}</sup>{{ $homeSettings->starting_price ?? '19' }}<sup>99</sup>
                            </h6>
                            <a href="{{ $homeSettings->button_url ?? '#' }}" class="btn btn-light d-inline-block">{{ $homeSettings->button_text ?? 'Shop Now' }}</a>
                        </div>
                    </div>

                    <!-- Slider 2 (Remove scaleX and adjust text) -->
                    <div class="home-slide home-slide1 banner" style="background-color: #111;">
                        <div class="slide-bg" style="background-image: url('{{ $homeSettings->getSlider2ImageUrl() ?? asset('assets/images/demoes/demo18/slider/home-slide-back.jpg') }}');"></div>
                        <div class="home-slide-content">
                            <h2 class="text-white text-transform-uppercase line-height-1">{{ $homeSettings->main_heading ?? 'Spring / Summer Season' }}</h2>
                            <h3 class="text-white d-inline-block line-height-1 ls-0 text-center">{{ $homeSettings->discount_text ?? 'Up to' }}</h3>
                            <h4 class="text-white text-uppercase line-height-1 d-inline-block">{{ $homeSettings->discount_value  }}</h4>
                            <h5 class="float-left text-white text-uppercase line-height-1 ls-n-20">Starting At</h5>
                            <h6 class="float-left coupon-sale-text line-height-1 ls-n-20 font-weight-bold text-secondary">
                                <sup>{{ $homeSettings->currency_symbol ?? '$' }}</sup>{{ $homeSettings->starting_price ?? '19' }}<sup>99</sup>
                            </h6>
                            <a href="{{ $homeSettings->button_url ?? '#' }}" class="btn btn-light d-inline-block">{{ $homeSettings->button_text ?? 'Shop Now' }}</a>
                        </div>
                    </div>

                </div>

                <!-- Slider Thumbnails -->
                <div class="home-slider-thumbs">
                    <div class="owl-dot" data-index="0">
                        <img src="{{ $homeSettings->getSlider1ThumbnailUrl() ?? asset('assets/images/demoes/demo18/slider/slide-1-thumb.jpg') }}" alt="Slide Thumb">
                    </div>
                    <div class="owl-dot" data-index="1">
                        <img src="{{ $homeSettings->getSlider2ThumbnailUrl() ?? asset('assets/images/demoes/demo18/slider/slide-2-thumb.jpg') }}" alt="Slide Thumb">
                    </div>
                </div>

            </section>
        @endif

        <style>
            /* --- NAVIGATION ARROWS --- */
            .home-slider .owl-nav {
                position: absolute;
                top: 50%;
                width: 100%;
                transform: translateY(-50%);
                display: flex;
                justify-content: space-between;
            }

            .home-slider .owl-prev,
            .home-slider .owl-next {
                background: rgba(255, 255, 255, 0.7);
                color: #111;
                font-size: 40px; /* Bigger Arrows */
                padding: 20px;
                border-radius: 50%;
                transition: all 0.3s ease-in-out;
                position: absolute;
                top: 50%;
                transform: translateY(-50%);
                cursor: pointer;
            }

            /* Left Arrow */
            .home-slider .owl-prev {
                left: 20px;
            }

            /* Right Arrow */
            .home-slider .owl-next {
                right: 20px;
            }

            /* Hover Effect */
            .home-slider .owl-prev:hover,
            .home-slider .owl-next:hover {
                background: #fff;
                color: #111;
                transform: translateY(-50%) scale(1.1);
            }

            /* --- DOT NAVIGATION --- */
            .home-slider .owl-dots {
                text-align: center;
                margin-top: 15px;
            }

            .home-slider .owl-dot {
                width: 12px;
                height: 12px;
                background: rgba(255, 255, 255, 0.5);
                border-radius: 50%;
                display: inline-block;
                margin: 5px;
                transition: all 0.3s;
            }

            .home-slider .owl-dot.active {
                background: #fff;
                width: 16px;
                height: 16px;
            }
        </style>

        <div class="products-filter-container bg-gray">
            <div class="container-fluid">
                @livewire('filtered-products')
            </div>
            @livewire('product-compare')
        </div>

        <!-- End .produts-filter-container-->

        <section class="product-banner-section">
            <div class="banner" style="background-color: #111;">
                <figure class="w-100 appear-animate" data-animation-name="fadeIn">
                    <img src="{{ $homeSettings?->getCenterImageUrl() ?? asset('assets/images/demoes/demo18/product-section-slider/slide-1.jpg') }}" alt="product banner">
                </figure>
                <div class="container-fluid">
                    <div class="position-relative h-100">
                        <div class="banner-layer banner-layer-middle">
                            <h3 class="text-white text-uppercase ls-n-25 m-b-4 appear-animate"
                                data-animation-name="fadeInUpShorter"
                                data-animation-duration="1000"
                                data-animation-delay="200">
                                {{ $homeSettings->center_main_heading ?? 'Ultra Boost' }}
                            </h3>

                            <a href="{{ $homeSettings->center_button_url ?? '#' }}"
                               class="btn btn-light appear-animate"
                               data-animation-name="fadeInUpShorter"
                               data-animation-duration="1000"
                               data-animation-delay="600">
                                {{ $homeSettings->center_button_text ?? 'Shop Now' }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- End .product-banner-section -->

        <section class="product-slider-section bg-gray">
            <div class="container-fluid">
                <h2 class="subtitle text-center text-uppercase mb-2 appear-animate" data-animation-name="fadeIn">
                    Featured Products
                </h2>

                <div class="featured-products owl-carousel owl-theme show-nav-hover nav-outer nav-image-center mb-3 appear-animate"
                     data-owl-options="{
            'dots': false,
            'nav': true,
            'margin': 20,
            'responsive': {
                '992': {
                    'items': 4
                },
                '1200': {
                    'items': 6
                }
            }
        }" data-animation-name="fadeIn">

                    @forelse($products as $product)
                        <div class="product-default inner-quickview inner-icon">
                            <figure>
                                <a href="{{ route('product.show', $product->slug) }}">
                                    <img src="{{ $product->getFirstMediaUrl('feature_product_image', 'thumb') }}" width="205" height="205" alt="{{ $product->name }}">
                                </a>
                                {{--                                <div class="btn-icon-group">--}}
                                {{--                                    @livewire('add-to-cart-home-page', ['product' => $product])--}}
                                {{--                                </div>--}}
                                <a href="{{ route('product.show', $product->slug) }}"
                                   class="btn-quickview"
                                   title="Quick View"
                                   onclick="event.stopPropagation();">
                                    View Details
                                </a>
                            </figure>
                            <div class="product-details">
                                <div class="category-wrap">
                                    <div class="category-list">
                                        <a href="{{ route('category.products', $product->category->slug) }}" class="product-category">
                                            {{ $product->category->name ?? 'Uncategorized' }}
                                        </a>
                                    </div>
                                    @livewire('love-button-home-page', ['product' => $product], key('love-' . $product->id))
                                </div>
                                <h3 class="product-title">
                                    <a href="{{ route('product.show', $product->slug) }}">{{ $product->name }}</a>
                                </h3>
                                <div class="ratings-container">
                                    <div class="product-ratings">
                                        <span class="ratings" style="width: {{ $product->getRatingPercentage() }}%"></span>
                                    </div>
                                </div>
                                <div class="price-box">
                                    <span class="product-price">
    @php
        $currency = \App\Models\Setting::getCurrency();
        $symbol = $currency?->code ?? '';
        $locale = app()->getLocale();
        $price = (float) ($product->after_discount_price ?? $product->price);
        $formattedPrice = number_format($price, 2); // Format price with two decimal places
    @endphp
                                        {{ $locale === 'en' ? "{$formattedPrice} {$symbol}" : "{$symbol} {$formattedPrice}" }}
</span>

                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center">
                            <p>No featured products available.</p>
                        </div>
                    @endforelse

                </div>

                <div class="row">
                    <div class="col-lg-6">
                        <div class="banner appear-animate" data-animation-name="fadeInLeftShorter" style="background-color: #fff;">
                            <figure>
                                <img src="{{ $homeSettings->getLast1ImageUrl() ?? asset('assets/images/demoes/demo18/banners/banner1.jpg') }}" alt="banner" width="873" height="151">
                            </figure>
                            <div class="banner-layer banner-layer-middle d-flex align-items-center justify-content-between">
                                <div>
                                    <h4 class="mb-0">{{ $homeSettings->last1_heading ?? 'Summer Sale' }}</h4>
                                    <h5 class="text-uppercase mb-0">{{ $homeSettings->last1_subheading ?? '20% off' }}</h5>
                                </div>
                                <a href="{{ $homeSettings->last1_button_url ?? '#' }}" class="btn btn-dark">
                                    {{ $homeSettings->last1_button_text ?? 'Shop Now' }}
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="banner appear-animate" data-animation-name="fadeInRightShorter" data-animation-delay="400" style="background-color: #111;">
                            <figure>
                                <img src="{{ $homeSettings->getLast2ImageUrl() ?? asset('assets/images/demoes/demo18/banners/banner2.jpg') }}" alt="banner" width="873" height="151">
                            </figure>
                            <div class="banner-layer banner-layer-middle d-flex align-items-center justify-content-between">
                                <div>
                                    <h4 class="text-white mb-0">{{ $homeSettings->last2_heading ?? 'Flash Sale' }}</h4>
                                    <h5 class="text-uppercase text-white mb-0">{{ $homeSettings->last2_subheading ?? '30% off' }}</h5>
                                </div>
                                <a href="{{ $homeSettings->last2_button_url ?? '#' }}" class="btn btn-light">
                                    {{ $homeSettings->last2_button_text ?? 'Shop Now' }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <!-- End .container-fluid -->
        </section>

        <section class="explore-section d-flex align-items-center"
                 data-parallax="{'speed': 1.8,  'enableOnMobile': true}"
                 data-image-src="{{ $homeSettings->getLatestImageUrl() ?? asset('assets/images/demoes/demo18/bg-2.jpg') }}"
                 style="background-color: #111;">

            <div class="container-fluid text-center position-relative appear-animate" data-animation-name="fadeInUpShorter">
                <h3 class="line-height-1 ls-n-25 text-white text-uppercase m-b-4">
                    {{ $homeSettings->latest_heading ?? __('Explore the best of you') }}
                </h3>
                <a href="{{ $homeSettings->latest_button_url ?? '#' }}" class="btn btn-light">
                    {{ $homeSettings->latest_button_text ?? __('Shop Now') }}
                </a>
            </div>
        </section>
        <!-- End .explore-section -->

        <section class="feature-boxes-container">
            <div class="container-fluid appear-animate" data-animation-name="fadeInUpShorter">
                <div class="row no-gaps m-0 ">
                    <div class="col-sm-6 col-lg-3">
                        <div class="feature-box feature-box-simple text-center mb-2">
                            <div class="feature-box-icon">
                                <i class="icon-earphones-alt text-white"></i>
                            </div>

                            <div class="feature-box-content p-0">
                                <h3 class="text-white">Customer Support</h3>
                                <h5 class="line-height-1">Need Assistance?</h5>

                                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis nec vestibulum magna, et dapib.</p>
                            </div>
                            <!-- End .feature-box-content -->
                        </div>
                        <!-- End .feature-box -->
                    </div>
                    <!-- End .col-sm-6.col-lg-3 -->

                    <div class="col-sm-6 col-lg-3">
                        <div class="feature-box feature-box-simple text-center mb-2">
                            <div class="feature-box-icon">
                                <i class="icon-credit-card text-white"></i>
                            </div>

                            <div class="feature-box-content p-0">
                                <h3 class="text-white">Secured Payment</h3>
                                <h5 class="line-height-1">Safe &amp; Fast</h5>

                                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis nec vestibulum magna, et dapibus lacus. Lorem ipsum dolor sit amet.</p>
                            </div>
                            <!-- End .feature-box-content -->
                        </div>
                        <!-- End .feature-box -->
                    </div>
                    <!-- End .col-sm-6 col-lg-3 -->

                    <div class="col-sm-6 col-lg-3">
                        <div class="feature-box feature-box-simple text-center mb-2">
                            <div class="feature-box-icon">
                                <i class="icon-action-undo text-white"></i>
                            </div>
                            <div class="feature-box-content p-0">
                                <h3 class="text-capitalize text-white">Free Returns</h3>
                                <h5 class="line-height-1">Easy &amp; Free</h5>

                                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis nec vestibulum magna, et dapib.</p>
                            </div>
                            <!-- End .feature-box-content -->
                        </div>
                        <!-- End .feature-box -->
                    </div>
                    <!-- col-sm-6 col-lg-3 -->

                    <div class="col-sm-6 col-lg-3">
                        <div class="feature-box feature-box-simple text-center mb-2">
                            <div class="feature-box-icon">
                                <i class="icon-shipping text-white"></i>
                            </div>
                            <div class="feature-box-content p-0">
                                <h3 class="text-white">Free Shipping</h3>
                                <h5 class="line-height-1">Made To Help You</h5>

                                <p>{{ $siteName }} has very powerful admin features to help customer to build their own shop in minutes without any special skills in web development.</p>
                            </div>
                            <!-- End .feature-box-content -->
                        </div>
                        <!-- End .feature-box -->
                    </div>
                    <!-- End .col-sm-6 col-lg-3 -->
                </div>
                <!-- End .row -->
            </div>
            <!-- End .container-->
        </section>
        <!-- End .feature-boxes-container -->
    </main>
    <!-- End .main -->

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
                <li><a href="demo18.html">Home</a></li>
                <li>
                    <a href="demo18-shop.html">Categories</a>
                    <ul>
                        <li><a href="category.html">Full Width Banner</a></li>
                        <li><a href="category-banner-boxed-slider.html">Boxed Slider Banner</a></li>
                        <li><a href="category-banner-boxed-image.html">Boxed Image Banner</a></li>
                        <li><a href="category-sidebar-left.html">Left Sidebar</a></li>
                        <li><a href="category-sidebar-right.html">Right Sidebar</a></li>
                        <li><a href="category-off-canvas.html">Off Canvas Filter</a></li>
                        <li><a href="category-horizontal-filter1.html">Horizontal Filter 1</a></li>
                        <li><a href="category-horizontal-filter2.html">Horizontal Filter 2</a></li>
                        <li><a href="#">List Types</a></li>
                        <li><a href="category-infinite-scroll.html">Ajax Infinite Scroll<span
                                    class="tip tip-new">New</span></a></li>
                        <li><a href="category.html">3 Columns Products</a></li>
                        <li><a href="category-4col.html">4 Columns Products</a></li>
                        <li><a href="category-5col.html">5 Columns Products</a></li>
                        <li><a href="category-6col.html">6 Columns Products</a></li>
                        <li><a href="category-7col.html">7 Columns Products</a></li>
                        <li><a href="category-8col.html">8 Columns Products</a></li>
                    </ul>
                </li>
                <li>
                    <a href="demo18-product.html">Products</a>
                    <ul>
                        <li>
                            <a href="#" class="nolink">PRODUCT PAGES</a>
                            <ul>
                                <li><a href="product.html">SIMPLE PRODUCT</a></li>
                                <li><a href="product-variable.html">VARIABLE PRODUCT</a></li>
                                <li><a href="product.html">SALE PRODUCT</a></li>
                                <li><a href="product.html">FEATURED & ON SALE</a></li>
                                <li><a href="product-sticky-info.html">WIDTH CUSTOM TAB</a></li>
                                <li><a href="product-sidebar-left.html">WITH LEFT SIDEBAR</a></li>
                                <li><a href="product-sidebar-right.html">WITH RIGHT SIDEBAR</a></li>
                                <li><a href="product-addcart-sticky.html">ADD CART STICKY</a></li>
                            </ul>
                        </li>
                        <li>
                            <a href="#" class="nolink">PRODUCT LAYOUTS</a>
                            <ul>
                                <li><a href="product-extended-layout.html">EXTENDED LAYOUT</a></li>
                                <li><a href="product-grid-layout.html">GRID IMAGE</a></li>
                                <li><a href="product-full-width.html">FULL WIDTH LAYOUT</a></li>
                                <li><a href="product-sticky-info.html">STICKY INFO</a></li>
                                <li><a href="product-sticky-both.html">LEFT & RIGHT STICKY</a></li>
                                <li><a href="product-transparent-image.html">TRANSPARENT IMAGE</a></li>
                                <li><a href="product-center-vertical.html">CENTER VERTICAL</a></li>
                                <li><a href="#">BUILD YOUR OWN</a></li>
                            </ul>
                        </li>
                    </ul>
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
                            <a href="{{ url('checkout') }}">Checkout</a>
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
                <li>
                    <a href="#">Elements</a>
                    <ul class="custom-scrollbar">
                        <li><a href="element-accordions.html">Accordion</a></li>
                        <li><a href="element-alerts.html">Alerts</a></li>
                        <li><a href="element-animations.html">Animations</a></li>
                        <li><a href="element-banners.html">Banners</a></li>
                        <li><a href="element-buttons.html">Buttons</a></li>
                        <li><a href="element-call-to-action.html">Call to Action</a></li>
                        <li><a href="element-countdown.html">Count Down</a></li>
                        <li><a href="element-counters.html">Counters</a></li>
                        <li><a href="element-headings.html">Headings</a></li>
                        <li><a href="element-icons.html">Icons</a></li>
                        <li><a href="element-info-box.html">Info box</a></li>
                        <li><a href="element-posts.html">Posts</a></li>
                        <li><a href="element-products.html">Products</a></li>
                        <li><a href="element-product-categories.html">Product Categories</a></li>
                        <li><a href="element-tabs.html">Tabs</a></li>
                        <li><a href="element-testimonial.html">Testimonials</a></li>
                    </ul>
                </li>
            </ul>

            <ul class="mobile-menu">
                <li><a href="login.html">My Account</a></li>
                <li><a href="demo18-contact.html">Contact Us</a></li>
                <li><a href="blog.html">Blog</a></li>
                <li><a href="{{ route('wishlist') }}">My Wishlist</a></li>
                <li><a href="cart.html">Cart</a></li>
                <li><a href="login.html" class="login-link">Log In</a></li>
            </ul>
        </nav>
        <!-- End .mobile-nav -->

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
    </div>
    <!-- End .mobile-menu-wrapper -->
</div>
<!-- End .mobile-menu-container -->

<div class="sticky-navbar">
    <div class="sticky-info">
        <a href="demo18.html">
            <i class="icon-home"></i>Home
        </a>
    </div>
    <div class="sticky-info">
        <a href="demo18-shop.html" class="">
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
<script src="assets/js/jquery.appear.min.js"></script>

<!-- Main JS File -->
<script src="assets/js/main.min.js"></script>
@livewireScripts
</body>
</html>
