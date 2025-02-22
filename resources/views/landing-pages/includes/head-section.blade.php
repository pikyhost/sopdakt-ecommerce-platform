{!! $settings?->facebook_pixel_code !!}
{!! $settings?->tiktok_pixel_code !!}
{!! $settings?->google_pixel_code !!}
{!! $settings?->snapchat_pixel_code !!}

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
