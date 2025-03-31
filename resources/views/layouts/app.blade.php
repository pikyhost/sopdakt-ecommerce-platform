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
        $topNotice = App\Models\TopNotice::where('is_active', true)->first();
        $locale = app()->getLocale(); // Get current locale
    @endphp

    @if($topNotice)
        <div class="top-notice bg-primary text-white">
            <div class="container text-center">
                <h5 class="d-inline-block">
                    {!! $locale === 'ar' ? $topNotice->content_ar : $topNotice->content_en !!}
                </h5>

                @if($topNotice->cta_text_en && $topNotice->cta_url)
                    <a href="{{ $topNotice->cta_url }}" class="category">
                        {{ $locale === 'ar' ? $topNotice->cta_text_ar : $topNotice->cta_text_en }}
                    </a>
                @endif

                @if($topNotice->cta_text_2_en && $topNotice->cta_url_2)
                    <a href="{{ $topNotice->cta_url_2 }}" class="category ml-2 mr-3">
                        {{ $locale === 'ar' ? $topNotice->cta_text_2_ar : $topNotice->cta_text_2_en }}
                    </a>
                @endif

                @if($topNotice->limited_time_text_en)
                    <small>
                        {{ $locale === 'ar' ? $topNotice->limited_time_text_ar : $topNotice->limited_time_text_en }}
                    </small>
                @endif

                <button title="Close (Esc)" type="button" class="mfp-close">×</button>
            </div><!-- End .container -->
        </div><!-- End .top-notice -->
    @endif

    <header class="header">
        <style>
            .header-top .top-message {
                font-weight: 500;
                letter-spacing: 0.5px;
            }

            .header-top .separator {
                width: 1px;
                height: 20px;
                opacity: 0.2;
            }

            .header-dropdown > a {
                display: flex;
                align-items: center;
                transition: all 0.3s ease;
                padding: 0.25rem 0;
            }

            .header-dropdown > a:hover {
                opacity: 0.8;
            }

            .header-dropdown:hover .header-menu {
                display: block;
            }

            .header-menu ul {
                list-style: none;
                padding: 0;
                margin: 0;
            }

            .header-menu .dropdown-item {
                display: flex;
                align-items: center;
                padding: 0.5rem 1rem;
                color: #333;
                transition: all 0.2s ease;
                white-space: nowrap;
            }

            .header-menu .dropdown-item:hover {
                background-color: #f8f9fa;
                color: #007bff;
                text-decoration: none;
            }

            /* Social Icons */
            .social-icons .social-icon {
                width: 30px;
                height: 30px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                border-radius: 50%;
                transition: all 0.3s ease;
            }

            .social-icons .social-icon:hover {
                transform: translateY(-2px);
                opacity: 0.9;
            }

            .menu > li {
                position: relative;
            }

            .menu > li > a {
                font-weight: 600;
                letter-spacing: 0.5px;
                transition: all 0.3s ease;
                position: relative;
            }

            .menu > li > a:after {
                content: '';
                position: absolute;
                bottom: 0;
                left: 0;
                width: 0;
                height: 3px;
                background-color: #fff;
                transition: all 0.3s ease;
            }

            .menu > li:hover > a:after,
            .menu > li.active > a:after {
                width: 100%;
            }

            .menu > li:hover .megamenu {
                display: block;
            }

            .megamenu h5 {
                font-size: 1rem;
                font-weight: 700;
                color: #333;
                border-bottom: 2px solid #f5f5f5;
                padding-bottom: 0.75rem;
                margin-bottom: 1rem;
            }

            .menu-banner .banner-content {
                position: absolute;
                bottom: 0;
                left: 0;
                right: 0;
                padding: 1.5rem;
                background: linear-gradient(to top, rgba(0, 0, 0, 0.7), transparent);
            }

            .menu-banner h4 {
                font-size: 1.5rem;
                font-weight: 700;
                color: #fff;
                line-height: 1.2;
            }

            .menu-banner h4 b {
                font-size: 2.5rem;
            }

            .menu-banner .btn {
                margin-top: 1rem;
            }

            .mobile-menu > li {
                margin-bottom: 0.5rem;
            }

            .mobile-menu > li > a {
                display: block;
                padding: 0.75rem 0;
                color: #333;
                font-weight: 600;
                border-bottom: 1px solid #eee;
            }

            .mobile-menu ul {
                list-style: none;
                padding: 0 0 0 1rem;
                display: none;
            }

            .mobile-menu ul li a {
                display: block;
                padding: 0.5rem 0;
                color: #666;
            }

            /* Animations */
            @keyframes fadeIn {
                from { opacity: 0; }
                to { opacity: 1; }
            }

            @keyframes slideDown {
                from { transform: translateY(-100%); }
                to { transform: translateY(0); }
            }

            /* Responsive Adjustments */
            @media (max-width: 991px) {
                .header-middle .header-center {
                    order: 3;
                    width: 100%;
                    margin-top: 1rem;
                }
            }

            @media (max-width: 767px) {
                .header-top .header-right {
                    justify-content: center;
                    width: 100%;
                }

                .header-top .separator {
                    display: none;
                }
            }

            /* RTL Support */
            [dir="rtl"] .header-menu {
                left: auto;
                right: 0;
            }

            [dir="rtl"] .megamenu-fixed-width {
                transform: translateX(50%);
            }

            [dir="rtl"] .mobile-menu-container {
                left: auto;
                right: -320px;
            }

            [dir="rtl"] .mobile-menu-container.open {
                right: 0;
                left: auto;
            }
        </style>
        <!-- Header Top Section -->
        <div class="header-top bg-dark text-white py-2">
            <div class="container">
                <div class="d-flex flex-column flex-lg-row justify-content-between align-items-center">
                    @if (!empty($topNotice->header_message_en) || !empty($topNotice->header_message_ar))
                        <div class="header-left d-none d-sm-block mb-2 mb-lg-0">
                            <p class="top-message text-uppercase mb-0 font-size-sm">
                                <i class="fas fa-bullhorn mr-2"></i>
                                {{ $locale === 'ar' ? $topNotice->header_message_ar : $topNotice->header_message_en }}
                            </p>
                        </div>
                    @endif

                    <div class="header-right d-flex align-items-center">
                        <!-- Quick Links Dropdown -->
                        <div class="header-dropdown dropdown-expanded d-none d-lg-block mr-3">
                            <a href="#" class="text-white d-flex align-items-center">
                                <i class="fas fa-link mr-1"></i>
                                <span>Quick Links</span>
                            </a>
                            <div class="header-menu bg-white shadow-lg">
                                <ul class="py-2">
                                    <li><a href="{{ url('/client/my-profile') }}" class="dropdown-item"><i class="fas fa-user-circle mr-2"></i>My Account</a></li>
                                    <li><a href="{{ url('/about-us') }}" class="dropdown-item"><i class="fas fa-info-circle mr-2"></i>About Us</a></li>
                                    <li><a href="{{ url('/blogs') }}" class="dropdown-item"><i class="fas fa-blog mr-2"></i>Blogs</a></li>
                                    <li><a href="{{ route('wishlist') }}" class="dropdown-item"><i class="fas fa-heart mr-2"></i>My Wishlist</a></li>
                                    <li><a href="{{ url('/cart') }}" class="dropdown-item"><i class="fas fa-shopping-cart mr-2"></i>Cart</a></li>
                                    <li><a href="{{ url('/client/login') }}" class="dropdown-item"><i class="fas fa-sign-in-alt mr-2"></i>Log In</a></li>
                                </ul>
                            </div>
                        </div>

                        <span class="separator bg-light mx-3 d-none d-lg-block"></span>

                        <!-- Language Selector -->
                        <div class="header-dropdown mr-3">
                            <a href="#" class="text-white d-flex align-items-center">
                                <i class="flag-{{ app()->getLocale() === 'ar' ? 'eg' : 'us' }} flag mr-1"></i>
                                <span>{{ app()->getLocale() === 'ar' ? 'العربية' : 'English' }}</span>
                            </a>
                            <div class="header-menu bg-white shadow-lg">
                                <ul class="py-2">
                                    <li>
                                        <a href="{{ LaravelLocalization::getLocalizedURL('en', null, [], true) }}" class="dropdown-item">
                                            <i class="flag-us flag mr-2"></i> English
                                            @if (app()->getLocale() === 'en')
                                                <i class="fas fa-check text-success ml-1"></i>
                                            @endif
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ LaravelLocalization::getLocalizedURL('ar', null, [], true) }}" class="dropdown-item">
                                            <i class="flag-eg flag mr-2"></i> العربية
                                            @if (app()->getLocale() === 'ar')
                                                <i class="fas fa-check text-success ml-1"></i>
                                            @endif
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <span class="separator bg-light mx-3 d-none d-lg-block"></span>

                        <!-- Social Media Icons -->
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

                        <div class="social-icons d-flex">
                            @foreach($socialLinks as $platform => $url)
                                @if(!empty($url) && isset($iconClasses[$platform]))
                                    <a href="{{ $url }}" class="social-icon text-white mx-2" target="_blank" rel="noopener noreferrer">
                                        <i class="{{ $iconClasses[$platform] }}"></i>
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Header Middle Section -->
        <div class="header-middle sticky-header bg-white shadow-sm py-3" data-sticky-options="{'mobile': true}">
            <div class="container">
                <div class="d-flex align-items-center justify-content-between">
                    <!-- Logo and Mobile Toggle -->
                    <div class="header-left d-flex align-items-center">
                        <button class="mobile-menu-toggler text-dark mr-3 d-lg-none" type="button">
                            <i class="fas fa-bars fa-lg"></i>
                        </button>

                        @if($logo)
                            <a href="{{ route('homepage') }}" class="logo mr-4">
                                <div class="logo-wrapper">
                                    <img src="{{ $logo }}" alt="Site Logo" class="site-logo" style="max-height: 50px;">
                                </div>
                            </a>
                        @else
                            <div class="site-name-wrapper">
                                <h1 class="website-name font-weight-bold mb-0">{{ $siteName ?: config('app.name') }}</h1>
                            </div>
                        @endif
                    </div>

                    <!-- Search Bar -->
                    <div class="header-center flex-grow-1 mx-4 d-none d-lg-block">
                        @livewire('light-global-search')
                    </div>

                    <!-- Right Icons -->
                    <div class="header-right d-flex align-items-center">
                        <div class="header-contact d-none d-lg-flex align-items-center mr-4">
                            <div class="icon-wrapper bg-primary rounded-circle p-2 mr-2">
                                <i class="fas fa-phone-alt text-white"></i>
                            </div>
                            <div>
                                <span class="d-block text-muted font-size-xs">{{ __('Call us now') }}</span>
                                <a href="tel:{{ \App\Models\Setting::getContactDetails()['phone'] }}" class="text-dark font-weight-bold">
                                    {{ \App\Models\Setting::getContactDetails()['phone'] }}
                                </a>
                            </div>
                        </div>

                        <div class="d-flex">
                            <a href="{{ url('/client/login') }}" class="header-icon mx-3 position-relative" title="login">
                                <i class="icon-user-2 text-dark"></i>
                            </a>

                            <a href="{{ route('wishlist') }}" class="header-icon mx-3 position-relative" title="wishlist">
                                <i class="icon-wishlist-2 text-dark"></i>
                                <span class="wishlist-count badge badge-pill badge-primary">0</span>
                            </a>

                            <div class="dropdown cart-dropdown ml-3">
                                @livewire('cart.cart-icon')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Header Bottom Navigation -->
        <div class="header-bottom sticky-header d-none d-lg-block bg-primary" data-sticky-options="{'mobile': false}">
            <div class="container">
                <nav class="main-nav w-100">
                    <ul class="menu d-flex justify-content-between">
                        <li class="active">
                            <a href="/" class="text-white py-3 px-3 d-block">Home</a>
                        </li>
                        <li>
                            <a href="category.html" class="text-white py-3 px-3 d-block">Categories</a>
                            <div class="megamenu megamenu-fixed-width megamenu-3cols border-0 shadow-lg">
                                <div class="row mx-0">
                                    <div class="col-lg-4 px-4 py-4">
                                        <h5 class="text-uppercase font-weight-bold mb-3">Shop by Category</h5>
                                    </div>
                                    <div class="col-lg-4 px-4 py-4">
                                        <h5 class="text-uppercase font-weight-bold mb-3">Featured Products</h5>
                                    </div>
                                    <div class="col-lg-4 p-0">
                                        <div class="menu-banner h-100">
                                            <div class="h-100 w-100 bg-cover" style="background-image: url('assets/images/menu-banner.jpg'); min-height: 300px;">
                                                <div class="banner-content p-4 text-white d-flex flex-column justify-content-between h-100">
                                                    <div>
                                                        <h4 class="font-weight-bold mb-0">SUMMER SALE</h4>
                                                        <p class="mb-3">Up to 50% off</p>
                                                    </div>
                                                    <a href="category.html" class="btn btn-light btn-sm align-self-start">SHOP NOW</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li>
                            <a href="product.html" class="text-white py-3 px-3 d-block">Products</a>
                            <div class="megamenu megamenu-fixed-width border-0 shadow-lg">
                                <div class="row mx-0">
                                    <div class="col-lg-4 px-4 py-4">
                                        <h5 class="text-uppercase font-weight-bold mb-3">Product Types</h5>
                                    </div>
                                    <div class="col-lg-4 px-4 py-4">
                                        <h5 class="text-uppercase font-weight-bold mb-3">Product Layouts</h5>
                                    </div>
                                    <div class="col-lg-4 p-0">
                                        <div class="menu-banner menu-banner-2 h-100">
                                            <div class="h-100 w-100 bg-cover" style="background-image: url('assets/images/menu-banner-1.jpg'); min-height: 300px;">
                                                <div class="banner-tag bg-danger text-white px-3 py-1 d-inline-block mb-2">HOT</div>
                                                <div class="banner-content p-4 text-white">
                                                    <h4 class="font-weight-bold mb-1">NEW COLLECTION</h4>
                                                    <p class="mb-3">Limited Time Offer</p>
                                                    <a href="category.html" class="btn btn-light btn-sm">SHOP NOW</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li>
                            <a href="#" class="text-white py-3 px-3 d-block">Pages</a>
                            <ul class="bg-white shadow-lg py-2">
                                <li><a href="{{ route('wishlist') }}" class="dropdown-item py-2 px-4"><i class="fas fa-heart mr-2 text-primary"></i>Wishlist</a></li>
                                <li><a href="cart.html" class="dropdown-item py-2 px-4"><i class="fas fa-shopping-cart mr-2 text-primary"></i>Shopping Cart</a></li>
                                <li><a href="checkout.html" class="dropdown-item py-2 px-4"><i class="fas fa-credit-card mr-2 text-primary"></i>Checkout</a></li>
                                <li><a href="dashboard.html" class="dropdown-item py-2 px-4"><i class="fas fa-tachometer-alt mr-2 text-primary"></i>Dashboard</a></li>
                                <li><a href="about.html" class="dropdown-item py-2 px-4"><i class="fas fa-info-circle mr-2 text-primary"></i>About Us</a></li>
                                <li><a href="#" class="dropdown-item py-2 px-4 d-flex justify-content-between align-items-center" data-toggle="submenu">
                                        <span><i class="fas fa-blog mr-2 text-primary"></i>Blog</span>
                                        <i class="fas fa-chevron-right text-muted font-size-xs"></i>
                                    </a>
                                    <ul class="bg-white shadow-lg py-2">
                                        <li><a href="blog.html" class="dropdown-item py-2 px-4">Blog List</a></li>
                                        <li><a href="single.html" class="dropdown-item py-2 px-4">Blog Post</a></li>
                                    </ul>
                                </li>
                                <li><a href="contact.html" class="dropdown-item py-2 px-4"><i class="fas fa-envelope mr-2 text-primary"></i>Contact Us</a></li>
                            </ul>
                        </li>
                        <li><a href="blog.html" class="text-white py-3 px-3 d-block">Blog</a></li>
                        <li><a href="contact.html" class="text-white py-3 px-3 d-block">Contact Us</a></li>
                    </ul>
                </nav>
            </div>
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
                        <ul>
                            <li><a href="product.html">Simple Product</a></li>
                            <li><a href="product-extended-layout.html">Extended Layout</a></li>
                        </ul>
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
                            <h4 class="widget-title">Customer Service</h4>

                            <ul class="links">
                                <li><a href="#">Help & FAQs</a></li>
                                <li><a href="#">Order Tracking</a></li>
                                <li><a href="#">Shipping & Delivery</a></li>
                                <li><a href="#">Orders History</a></li>
                                <li><a href="#">Advanced Search</a></li>
                                <li><a href="dashboard.html">My Account</a></li>
                                <li><a href="#">Careers</a></li>
                                <li><a href="about.html">About Us</a></li>
                                <li><a href="#">Corporate Sales</a></li>
                                <li><a href="#">Privacy</a></li>
                            </ul>
                        </div><!-- End .widget -->
                    </div><!-- End .col-lg-3 -->

                    <div class="col-lg-3 col-sm-6">
                        <div class="widget">
                            <h4 class="widget-title">Popular Tags</h4>

                            <div class="tagcloud">
                                <a href="#">Bag</a>
                                <a href="#">Black</a>
                                <a href="#">Blue</a>
                                <a href="#">Clothes</a>
                                <a href="#">Fashion</a>
                                <a href="#">Hub</a>
                                <a href="#">Shirt</a>
                                <a href="#">Shoes</a>
                                <a href="#">Skirt</a>
                                <a href="#">Sports</a>
                                <a href="#">Sweater</a>
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

<div class="loading-overlay">
    <div class="bounce-loader">
        <div class="bounce1"></div>
        <div class="bounce2"></div>
        <div class="bounce3"></div>
    </div>
</div>

<div class="mobile-menu-overlay"></div><!-- End .mobil-menu-overlay -->

<div class="mobile-menu-container">
    <div class="mobile-menu-wrapper">
        <span class="mobile-menu-close"><i class="fa fa-times"></i></span>
        <nav class="mobile-nav">$
            <ul class="mobile-menu">
                <li><a href="/">Home</a></li>
                <li>
                    <a href="">Categories</a>
                    <ul>
                        <li><a href="category.html">Full Width Banner</a></li>
                        <li><a href="category-banner-boxed-slider.html">Boxed Slider Banner</a></li>
                        <li><a href="category-banner-boxed-image.html">Boxed Image Banner</a></li>
                        <li><a href="category-sidebar-left.html">Left Sidebar</a></li>
                        <li><a href="category-sidebar-right.html">Right Sidebar</a></li>
                        <li><a href="category-off-canvas.html">Off Canvas Filter</a></li>
                        <li><a href="category-horizontal-filter1.html">Horizontal Filter 1</a></li>
                        <li><a href="category-horizontal-filter2.blade.php">Horizontal Filter 2</a></li>
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
                    <a href="product.html">Products</a>
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

            <ul class="mobile-menu mt-2 mb-2">
                <li class="border-0">
                    <a href="#">
                        Special Offer!
                    </a>
                </li>
                <li class="border-0">
                    <a href="#" target="_blank">
                        Buy X!
                        <span class="tip tip-hot">Hot</span>
                    </a>
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

