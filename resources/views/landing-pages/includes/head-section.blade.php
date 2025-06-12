
<head>
    @if ($showSettings)
        {!! $settings?->facebook_pixel_code !!}
        {!! $settings?->tiktok_pixel_code !!}
        {!! $settings?->google_pixel_code !!}
        {!! $settings?->snapchat_pixel_code !!}
    @endif

    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="title" content="{{$landingPage->meta_title}}">
    <meta name="description" content="{{$landingPage->meta_description}}">
    <meta name="keywords" content="{{$landingPage->meta_keywords}}">
    <meta name="author" content="Kareem Hamed">
    <title> {{$landingPage->slug}} </title>
    <link rel="shortcut icon" href="{{asset($landingPage->header_image)}}" type="image/x-icon">
    <link rel="stylesheet" href="{{asset('assets/css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/fontawesome.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/flaticon.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/animate.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/owl.carousel.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/magnific-popup.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/aos.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/porto_style.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/style_landing.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/responsive.css')}}">
    <link rel="stylesheet" href="{{asset('plugins/sweetalerts2/sweetalerts2.css')}}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/css/splide.min.css">

    <style>
        :root {
            --primary: #000000;
            --primary-accent: #262626;
            --primary-gradient: #000000;
            --primary-gradient-accent: #000000;
            --primary-background-color: #121111;
            --product-criteria-title-color: #ffffff;
            --product-criteria-price-color: #ff0000;
            --product-criteria-price-after-discount-color: #ffffff;
            --product-criteria-description-color: #ffffff;
            --product-criteria-sku-label-color: #f7f3f3;
            --product-criteria-sku-color: #ffffff;
            --product-criteria-background-color: #2c2c2c;
            --product-criteria-cta-button-background-color: #ff0000;
            --product-criteria-cta-button-hover-background-color: #000000;
            --product-criteria-cta-button-border-color: #ff0000;
            --product-criteria-cta-button-text-hover-color: #ffffff;
            --product-criteria-cta-button-text-color: #ffffff;
            --product-criteria-feature-color: #ffffff;
            --home-section-background-color: #000000;
            --home-section-title-color: #ffffff;
            --home-section-subtitle-color: #ffffff;
            --about-section-background-color: #ffffff;
            --about-section-boxes-background-color: #e8e8e8;
            --about-section-box-title-color-hover: #000000;
            --about-section-box-subtitle-color-hover: #000000;
            --features12-section-background-color: #000000;
            --features12-section-box-title-color: #d9afaf;
            --features12-section-box-subtitle-color: #a7b125;
            --faq-background-color: #000000;
            --feature3-section-background-color: #000000;
            --feature3-section-box-background-color: #000000;
            --feature3-section-box-title-color: #000000;
            --feature3-section-box-subtitle-color: #000000;
            --deal-of-the-week-section-background-color: #000000;
            --deal-of-the-week-section-box-background-color: #ffffff;
            --deal-of-the-week-section-box-title-color: #000000;
            --deal-of-the-week-section-box-subtitle-color: #000000;
            --deal-of-the-week-section-box-price-color: #000000;
            --products-section-background-color: #f0f0f0;
            --products-section-item-background-color: #db1f1f;
            --products-section-item-image-background-color: #e0d7d7;
            --products-section-item-title-color: #c81919;
            --why-choose-us-section-background-color: #000000;
            --compare-section-background-color: #000000;
            --compare-section-product-title-color: #fafafa;
            --compare-section-product-subtitle-color: #000000;
            --compare-section-table-property-title-color: #000000;
            --feedbacks-section-background-color: #000000;
            --feedbacks-section-comment-color: #000000;
            --feedbacks-section-client-name-color: #000000;
            --feedbacks-section-client-position-color: #ffffff;
            --feedbacks-section-box-background-color: #ffffff;
            --faq-section-question-background-color: #000000;
            --faq-section-question-color: #f5f5f5;
            --faq-section-answer-background-color: #000000;
            --faq-section-answer-color: #ffffff;
            --contact-section-background-color: #000000;
            --contact-section-boxes-text-color: #000000;
            --contact-section-box-background-color: #ffffff;
            --contact-section-form-input-field-background-color: #ffffff;
            --contact-section-form-input-field-text-color: #000000;
            --footer-section-background-color: #000000;
            --footer-section-subtitle-color: #ffffff;
            --about-section-title-color: #000000;
            --about-section-subtitle-color: #000000;
            --features1-section-title-color: #df0c0c;
            --features1-section-subtitle-color: #46ba1c;
            --features2-section-title-color: #000000;
            --features2-section-subtitle-color: #2eaf2c;
            --feature3-section-title-color: #f4f0f0;
            --feature3-section-subtitle-color: #000000;
            --deel-of-the-week-section-title-color: #ffffff;
            --deel-of-the-week-section-subtitle-color: #ffffff;
            --products-section-title-color: #ffffff;
            --products-section-subtitle-color: #ffffff;
            --why-choose-us-section-title-color: #ffffff;
            --why-choose-us-section-subtitle-color: #ffffff;
            --compare-section-title-color: #ffffff;
            --compare-section-subtitle-color: #000000;
            --feedbacks-section-title-color: #ffffff;
            --feedbacks-section-subtitle-color: #ffffff;
            --faq-section-title-color: #ffffff;
            --faq-section-subtitle-color: #fcf7f7;
            --contact-section-title-color: #ffffff;
            --contact-section-subtitle-color: #ffffff;
            --deal-of-the-week-counter-color: #f50000;
            --nav-bar-items-not-active-text-color: #ffffff;
            --nav-bar-items-text-color: #000000;
            --nav-bar-background-color: #ffffff;
            --home-section-img-circles-color: #ffffff;
            --home-section-cta-button-background-color: #ffffff;
            --home-section-cta-button-hover-background-color: #ffffff;
            --home-section-cta-button-border-color: #292929;
            --home-section-cta-button-text-hover-color: #000000;
            --home-section-cta-button-text-color: #ffffff;
            --about-section-cta-button-background-color: #ff0000;
            --about-section-cta-button-hover-background-color: #000000;
            --about-section-cta-button-border-color: #ff0000;
            --about-section-cta-button-text-hover-color: #000000;
            --about-section-cta-button-text-color: #ffffff;
            --deal-of-the-week-section-cta-button-background-color: #ff0000;
            --deal-of-the-week-section-cta-button-hover-background-color: #000000;
            --deal-of-the-week-section-cta-button-border-color: #ff0000;
            --deal-of-the-week-section-cta-button-text-hover-color: #ffffff;
            --deal-of-the-week-section-cta-button-text-color: #ffffff;
            --products-section-cta-button-background-color: #ffffff;
            --products-section-cta-button-hover-background-color: #ffffff;
            --products-section-cta-button-border-color: #ffffff;
            --products-section-cta-button-text-hover-color: #d64c4c;
            --products-section-cta-button-text-color: #ffffff;
            --compare-section-cta-button-background-color: #ff0000;
            --compare-section-cta-button-hover-background-color: #ffffff;
            --compare-section-cta-button-border-color: #ff0000;
            --compare-section-cta-button-text-hover-color: #000000;
            --compare-section-cta-button-text-color: #ffffff;
            --features1-section-cta-button-background-color: #ff0000;
            --features1-section-cta-button-hover-background-color: #ffffff;
            --features1-section-cta-button-border-color: #ff0000;
            --features1-section-cta-button-text-hover-color: #000000;
            --features1-section-cta-button-text-color: #ffffff;
            --features2-section-cta-button-background-color: #ff0000;
            --features2-section-cta-button-hover-background-color: #ffffff;
            --features2-section-cta-button-border-color: #ff0000;
            --features2-section-cta-button-text-hover-color: #000000;
            --features2-section-cta-button-text-color: #ffffff;
            --features-section-cta-button-background-color: #ff0000;
            --features-section-cta-button-hover-background-color: #ffffff;
            --features-section-cta-button-border-color: #ff0000;
            --features-section-cta-button-text-hover-color: #000000;
            --features-section-cta-button-text-color: #ffffff;
            --top-bar-background-color: #ff3200;
            --top-bar-text-color: #ffffff;
            --topBar-dark-background-color: #ff3200;
            --topBar-dark-text-color: #ffffff;
            --counter-section-background-color: #000000;
            --counter-section-counter-color: #ffffff;
            --counter-section-cta-button-color: #ff0000;
            --counter-section-cta-button-text-color: #ffffff;
            --shadow-color: rgba(255, 255, 255, 0.1);
            --footer-border-color: rgba(255, 255, 255, 0.15);
        }

        .body {
            background-color: #FFF !important;
        }
        .rtl-header {
            display: flex;
            flex-direction: row-reverse;
        }

        .ltr-header {
            display: flex;
            flex-direction: row;
        }

        .close-left {
            position: absolute;
            left: 15px;
            top: 15px;
        }

        .close-right {
            position: absolute;
            right: 15px;
            top: 15px;
        }

        @media only screen and (max-width: 991px) {
            .home9 .navigation .navbar #mainmenu ul .nav-item .nav-link {
                color: white;
            }
        }

        .product-single-details .ratings-container .ratings:before {
            color: #f9bd22;
        }

        .navbar-light .navbar-toggler-icon {
            background-color: black;
            background-image: url("data:image/svg+xml;charset=utf8,%3Csvg viewBox='0 0 30 30' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath stroke='white' stroke-width='2' stroke-linecap='round' stroke-miterlimit='10' d='M4 7h22M4 15h22M4 23h22'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-size: 100% 100%;
        }

        .mfp-content {
            position: relative;
            height: 600px !important;
        }

        @media(max-width: 768px) {
            .mfp-content {
                height: 700px !important;
        }

        }

        .mfp-iframe-holder .mfp-close {
            position: absolute !important;
            top: -300px;
            left: 110px;
        }

        .mfp-iframe-holder {
            padding-bottom: 0px !important;
        }

    </style>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.css"/>

    <style>
        .topbar {
            background-color: var(--top-bar-background-color);
            color: var(--top-bar-text-color);
            height: 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 15px;
            overflow: hidden;
        }

        .swiper-container {
            height: 30px;
            width: 100%;
        }

        .swiper-slide {
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 0.875rem;
        }

        .video .video-wrapper .video-box .overly {
            background-image: url("{{ asset($landingPage->why_choose_us_section_top_image) }}");
            opacity: 1;
        }

        .navigation {
            background-color: black;
        }

        .btn-link {
            color: black;
            background-color: white;
        }

        .btn-link:hover {
            color: red;
        }

        .pt-5, .py-5 {
            padding-top: 7rem !important;
        }

        .section-title.extra .title,
        .section-title.extra .subtitle,
        .section-title .title,
        .section-title .subtitle,
        .categori,
        .panel-title.collapsed,
        .panel-body,
        .client-say,
        .rounded-0,
        .swiper-slide,
        .swiper-slide.text-white {
            direction: rtl;
        }
    </style>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Almarai:wght@300;400;700;800&family=Cairo:wght@200..1000&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: "Almarai", serif;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: "Almarai", serif;
        }
    </style>
</head>
