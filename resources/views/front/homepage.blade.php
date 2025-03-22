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
@endphp

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>{{ $siteName }}</title>

    <meta name="keywords" content="HTML5 Template" />
    <meta name="description" content="Porto - Bootstrap eCommerce Template">
    <meta name="author" content="SW-THEMES">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="assets/images/icons/favicon.png">

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
                                            <a href="#" class="nolink">VARIATION 1</a>
                                            <ul class="submenu">
                                                <li><a href="category.html">Fullwidth Banner</a></li>
                                                <li><a href="category-banner-boxed-slider.html">Boxed Slider
                                                        Banner</a>
                                                </li>
                                                <li><a href="category-banner-boxed-image.html">Boxed Image
                                                        Banner</a>
                                                </li>
                                                <li><a href="category.html">Left Sidebar</a></li>
                                                <li><a href="category-sidebar-right.html">Right Sidebar</a></li>
                                                <li><a href="category-off-canvas.html">Off Canvas Filter</a></li>
                                                <li><a href="category-horizontal-filter1.html">Horizontal
                                                        Filter1</a>
                                                </li>
                                                <li><a href="category-horizontal-filter2.html">Horizontal
                                                        Filter2</a>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="col-lg-4">
                                            <a href="#" class="nolink">VARIATION 2</a>
                                            <ul class="submenu">
                                                <li><a href="category-list.html">List Types</a></li>
                                                <li><a href="category-infinite-scroll.html">Ajax Infinite Scroll</a>
                                                </li>
                                                <li><a href="category.html">3 Columns Products</a></li>
                                                <li><a href="category-4col.html">4 Columns Products</a></li>
                                                <li><a href="category-5col.html">5 Columns Products</a></li>
                                                <li><a href="category-6col.html">6 Columns Products</a></li>
                                                <li><a href="category-7col.html">7 Columns Products</a></li>
                                                <li><a href="category-8col.html">8 Columns Products</a></li>
                                            </ul>
                                        </div>
                                        <div class="col-lg-4 p-0">
                                            <div class="menu-banner">
                                                <figure>
                                                    <img src="assets/images/menu-banner.jpg" alt="Menu banner" width="300" height="300">
                                                </figure>
                                                <div class="banner-content">
                                                    <h4>
                                                        <span class="">UP TO</span><br />
                                                        <b class="">50%</b>
                                                        <i>OFF</i>
                                                    </h4>
                                                    <a href="demo18-shop.html" class="btn btn-sm btn-dark">SHOP
                                                        NOW</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- End .megamenu -->
                            </li>
                            <li>
                                <a href="demo18-product.html">Products</a>
                                <div class="megamenu megamenu-fixed-width">
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <a href="#" class="nolink">PRODUCT PAGES</a>
                                            <ul class="submenu">
                                                <li><a href="product.html">SIMPLE PRODUCT</a></li>
                                                <li><a href="product-variable.html">VARIABLE PRODUCT</a></li>
                                                <li><a href="product.html">SALE PRODUCT</a></li>
                                                <li><a href="product.html">FEATURED & ON SALE</a></li>
                                                <li><a href="product-custom-tab.html">WITH CUSTOM TAB</a></li>
                                                <li><a href="product-sidebar-left.html">WITH LEFT SIDEBAR</a></li>
                                                <li><a href="product-sidebar-right.html">WITH RIGHT SIDEBAR</a></li>
                                                <li><a href="product-addcart-sticky.html">ADD CART STICKY</a></li>
                                            </ul>
                                        </div>
                                        <!-- End .col-lg-4 -->

                                        <div class="col-lg-4">
                                            <a href="#" class="nolink">PRODUCT LAYOUTS</a>
                                            <ul class="submenu">
                                                <li><a href="product-extended-layout.html">EXTENDED LAYOUT</a></li>
                                                <li><a href="product-grid-layout.html">GRID IMAGE</a></li>
                                                <li><a href="product-full-width.html">FULL WIDTH LAYOUT</a></li>
                                                <li><a href="product-sticky-info.html">STICKY INFO</a></li>
                                                <li><a href="product-sticky-both.html">LEFT & RIGHT STICKY</a></li>
                                                <li><a href="product-transparent-image.html">TRANSPARENT IMAGE</a>
                                                </li>
                                                <li><a href="product-center-vertical.html">CENTER VERTICAL</a></li>
                                                <li><a href="#">BUILD YOUR OWN</a></li>
                                            </ul>
                                        </div>
                                        <!-- End .col-lg-4 -->

                                        <div class="col-lg-4 p-0">
                                            <div class="menu-banner menu-banner-2">
                                                <figure>
                                                    <img src="assets/images/menu-banner-1.jpg" alt="Menu banner" class="product-promo" width="380" height="790">
                                                </figure>
                                                <i>OFF</i>
                                                <div class="banner-content">
                                                    <h4>
                                                        <span class="">UP TO</span><br />
                                                        <b class="">50%</b>
                                                    </h4>
                                                </div>
                                                <a href="demo18-shop.html" class="btn btn-sm btn-dark">SHOP NOW</a>
                                            </div>
                                        </div>
                                        <!-- End .col-lg-4 -->
                                    </div>
                                    <!-- End .row -->
                                </div>
                                <!-- End .megamenu -->
                            </li>
                            <li class="d-none d-xl-block">
                                <a href="#">Pages</a>
                                <ul>
                                    <li><a href="wishlist.html">Wishlist</a></li>
                                    <li><a href="cart.html">Shopping Cart</a></li>
                                    <li><a href="checkout.html">Checkout</a></li>
                                    <li><a href="dashboard.html">Dashboard</a></li>
                                    <li><a href="demo18-about.html">About Us</a></li>
                                    <li><a href="#">Blog</a>
                                        <ul>
                                            <li><a href="blog.html">Blog</a></li>
                                            <li><a href="single.html">Blog Post</a></li>
                                        </ul>
                                    </li>
                                    <li><a href="demo18-contact.html">Contact Us</a></li>
                                    <li><a href="login.html">Login</a></li>
                                    <li><a href="forgot-password.html">Forgot Password</a></li>
                                </ul>
                            </li>
                            <li><a href="blog.html">Blog</a></li>
                            <li class="d-none d-xxl-block"><a href="#">Special Offer!</a></li>
                            <li><a href="https://1.envato.market/DdLk5" target="_blank">Buy Porto!</a></li>
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

                    <a href="{{ url('/client/my-wishlist') }}" class="header-icon" title="Wishlist"><i class="icon-wishlist-2"><span
                                class="badge-circle">2</span></i></a>

                    <div class="header-icon header-search header-search-popup header-search-category text-right">
                        <a href="#" class="search-toggle" role="button"><i class="icon-magnifier"></i></a>
                        <form action="#" method="get">
                            <div class="header-search-wrapper">
                                <input type="search" class="form-control" name="q" id="q" placeholder="Search..." required>
                                <div class="select-custom">
                                    <select id="cat" name="cat">
                                        <option value="">All Categories</option>
                                        <option value="4">Fashion</option>
                                        <option value="12">- Women</option>
                                        <option value="13">- Men</option>
                                        <option value="66">- Jewellery</option>
                                        <option value="67">- Kids Fashion</option>
                                        <option value="5">Electronics</option>
                                        <option value="21">- Smart TVs</option>
                                        <option value="22">- Cameras</option>
                                        <option value="63">- Games</option>
                                        <option value="7">Home &amp; Garden</option>
                                        <option value="11">Motors</option>
                                        <option value="31">- Cars and Trucks</option>
                                        <option value="32">- Motorcycles &amp; Powersports</option>
                                        <option value="33">- Parts &amp; Accessories</option>
                                        <option value="34">- Boats</option>
                                        <option value="57">- Auto Tools &amp; Supplies</option>
                                    </select>
                                </div>
                                <!-- End .select-custom -->
                                <button class="btn icon-magnifier p-0" title="search" type="submit"></button>
                            </div>
                            <!-- End .header-search-wrapper -->
                        </form>
                    </div>
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
            $settings = \App\Models\HomePageSetting::getCached();
        @endphp

        @if($settings)
            <section class="home-slider-container">
                <div class="home-slider owl-carousel with-dots-container" data-owl-options='{"nav": true, "dots": true, "loop": true, "autoplay": true, "autoplayTimeout": 5000, "animateOut": "fadeOut"}'>
                    <div class="home-slide home-slide1 banner" style="background-color: #111">
                        <div class="slide-bg" style="background-image: url('{{ asset($settings->background_image) }}');"></div>
                        <ul class="slide-bg scene">
                            <li class="layer" data-depth="0.05">
                                <img src="{{ asset($settings->layer_image) }}" alt="Layer Image" />
                            </li>
                        </ul>
                        <div class="home-slide-content">
                            <h2 class="text-white text-transform-uppercase">{{ $settings->main_heading }}</h2>
                            <h3 class="text-white d-inline-block">{{ $settings->discount_text }}</h3>
                            <h4 class="text-white text-uppercase d-inline-block">{{ $settings->discount_value }}</h4>
                            <h5 class="float-left text-white">{{ __('Starting At') }}</h5>
                            <h6 class="float-left coupon-sale-text font-weight-bold text-secondary">
                                <sup>{{ $settings->currency_symbol }}</sup>{{ number_format($settings->starting_price, 2) }}
                            </h6>
                            <a href="{{ $settings->button_url }}" class="btn btn-light">{{ $settings->button_text }}</a>
                        </div>
                    </div>

                    <div class="home-slide home-slide2 banner" style="background-color: #111;">
                        <div class="slide-bg" style="background-image: url('{{ asset($settings->background_image) }}'); transform: scaleX(-1);"></div>
                        <ul class="slide-bg scene">
                            <li class="layer" data-depth="0.05">
                                <img src="{{ asset($settings->layer_image) }}" alt="Layer Image" />
                            </li>
                        </ul>
                        <div class="home-slide-content">
                            <h2 class="text-white text-transform-uppercase">{{ $settings->main_heading }}</h2>
                            <h3 class="text-white d-inline-block">{{ $settings->discount_text }}</h3>
                            <h4 class="text-white text-uppercase d-inline-block">{{ $settings->discount_value }}</h4>
                            <h5 class="float-left text-white">{{ __('Starting At') }}</h5>
                            <h6 class="float-left coupon-sale-text font-weight-bold text-secondary">
                                <sup>{{ $settings->currency_symbol }}</sup>{{ number_format($settings->starting_price, 2) }}
                            </h6>
                            <a href="{{ $settings->button_url }}" class="btn btn-light">{{ $settings->button_text }}</a>
                        </div>
                    </div>
                </div>

                <div class="home-slider-thumbs">
                    <a href="#" class="owl-dot">
                        <img src="{{ asset($settings->thumbnail_image) }}" alt="Slide Thumb">
                    </a>
                    <a href="#" class="owl-dot">
                        <img src="{{ asset($settings->thumbnail_image) }}" alt="Slide Thumb">
                    </a>
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
        </div>

        <!-- End .produts-filter-container-->

        <section class="product-banner-section">
            <div class="banner" style="background-color: #111;">
                <figure class="w-100 appear-animate" data-animation-name="fadeIn">
                    <img src="assets/images/demoes/demo18/product-section-slider/slide-1.jpg" alt="product banner">
                </figure>
                <div class="container-fluid">
                    <div class="position-relative h-100">
                        <div class="banner-layer banner-layer-middle">
                            <h3 class="text-white text-uppercase ls-n-25 m-b-4 appear-animate" data-animation-name="fadeInUpShorter" data-animation-duration="1000" data-animation-delay="200">Ultra Boost</h3>
                            <img class="m-b-4 appear-animate" data-animation-name="fadeInUpShorter" data-animation-duration="1000" data-animation-delay="400" src="assets/images/demoes/demo18/product-section-slider/img-1.png" alt="img" width="540" height="100">
                            <a href="demo18-shop.html" class="btn btn-light appear-animate" data-animation-name="fadeInUpShorter" data-animation-duration="1000" data-animation-delay="600">Shop Now</a>
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
                                        <a href="{{ route('category.products', $product->category->slug ?? '#') }}" class="product-category">
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
                                    <span class="product-price">${{ $product->discount_price_for_current_country }}</span>
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
                                <img src="assets/images/demoes/demo18/banners/banner1.jpg" alt="banner" width="873" height="151">
                            </figure>
                            <div class="banner-layer banner-layer-middle d-flex align-items-center justify-content-between">
                                <div class="">
                                    <h4 class="mb-0">Summer Sale</h4>
                                    <h5 class="text-uppercase mb-0">20% off</h5>
                                </div>
                                <a href="demo18-shop.html" class="btn btn-dark">Shop now</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="banner appear-animate" data-animation-name="fadeInRightShorter" data-animation-delay="400" style="background-color: #111;">
                            <figure>
                                <img src="assets/images/demoes/demo18/banners/banner2.jpg" alt="banner" width="873" height="151">
                            </figure>
                            <div class="banner-layer banner-layer-middle d-flex align-items-center justify-content-between">
                                <div class="">
                                    <h4 class="text-white mb-0">Flash Sale</h4>
                                    <h5 class="text-uppercase text-white mb-0">30% off</h5>
                                </div>
                                <a href="demo18-shop.html" class="btn btn-light">Shop now</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End .container-fluid -->
        </section>

        <section class="explore-section d-flex align-items-center" data-parallax="{'speed': 1.8,  'enableOnMobile': true}" data-image-src="assets/images/demoes/demo18/bg-2.jpg" style="background-color: #111;">
            <div class="container-fluid text-center position-relative appear-animate" data-animation-name="fadeInUpShorter">
                <h3 class="line-height-1 ls-n-25 text-white text-uppercase m-b-4">Explore the best of you</h3>
                <a href="demo18-shop.html" class="btn btn-light">Shop Now</a>
            </div>
            <!-- End .container -->
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

                                <p>Porto has very powerful admin features to help customer to build their own shop in minutes without any special skills in web development.</p>
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
                    <form action="#">
                        <div class="footer-submit-wrapper d-flex">
                            <input type="email" class="form-control" placeholder="Email address..." size="40" required>
                            <button type="submit" class="btn btn-dark btn-sm">Subscribe</button>
                        </div>
                    </form>
                </div>
                <div class="footer-right">
                    <div class="social-icons">
                        <a href="#" class="social-icon social-facebook icon-facebook" target="_blank"></a>
                        <a href="#" class="social-icon social-twitter icon-twitter" target="_blank"></a>
                        <a href="#" class="social-icon social-instagram icon-instagram" target="_blank"></a>
                    </div>
                    <!-- End .social-icons -->
                </div>
            </div>
            <div class="footer-middle">
                <div class="row">
                    <div class="col-lg-3">
                        <a href="demo18.html">
                            <img src="assets/images/demoes/demo18/logo.png" alt="Logo" class="logo">
                        </a>

                        <p class="footer-desc">Lorem ipsum dolor sit amet, consectetur adipis.</p>

                        <div class="ls-0 footer-question">
                            <h6 class="mb-0 text-white">QUESTIONS?</h6>
                            <h3 class="mb-3 text-white"><a href="tel:1-888-123-456">1-888-123-456</a></h3>
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
                                        <li><a href="demo18-about.html">About Porto</a></li>
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
                <p class="footer-copyright text-lg-center mb-0">&copy; Porto eCommerce. 2021. All Rights Reserved
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
                            <a href="wishlist.html">Wishlist</a>
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

            <ul class="mobile-menu mt-2 mb-2">
                <li class="border-0">
                    <a href="#">
                        Special Offer!
                    </a>
                </li>
                <li class="border-0">
                    <a href="https://1.envato.market/DdLk5" target="_blank">
                        Buy Porto!
                        <span class="tip tip-hot">Hot</span>
                    </a>
                </li>
            </ul>

            <ul class="mobile-menu">
                <li><a href="login.html">My Account</a></li>
                <li><a href="demo18-contact.html">Contact Us</a></li>
                <li><a href="blog.html">Blog</a></li>
                <li><a href="wishlist.html">My Wishlist</a></li>
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
            <a href="#" class="social-icon social-facebook icon-facebook" target="_blank">
            </a>
            <a href="#" class="social-icon social-twitter icon-twitter" target="_blank">
            </a>
            <a href="#" class="social-icon social-instagram icon-instagram" target="_blank">
            </a>
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
        <a href="wishlist.html" class="">
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

<div class="newsletter-popup mfp-hide bg-img" id="newsletter-popup-form" style="background: #f1f1f1 no-repeat center/cover url(assets/images/newsletter_popup_bg.jpg)">
    <div class="newsletter-popup-content">
        <img src="assets/images/logo-black.png" alt="Logo" class="logo-newsletter" width="111" height="44">
        <h2>Subscribe to newsletter</h2>

        <p>
            Subscribe to the Porto mailing list to receive updates on new arrivals, special offers and our promotions.
        </p>

        <form action="#">
            <div class="input-group">
                <input type="email" class="form-control" id="newsletter-email" name="newsletter-email" placeholder="Your email address" required />
                <input type="submit" class="btn btn-primary" value="Submit" />
            </div>
        </form>
        <div class="newsletter-subscribe">
            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" value="0" id="show-again" />
                <label for="show-again" class="custom-control-label">
                    Don't show this popup again
                </label>
            </div>
        </div>
    </div>
    <!-- End .newsletter-popup-content -->

    <button title="Close (Esc)" type="button" class="mfp-close">
        ×
    </button>
</div>
<!-- End .newsletter-popup -->

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
