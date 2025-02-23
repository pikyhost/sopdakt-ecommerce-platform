<!DOCTYPE html>
<html lang="en">
@include('landing-pages.includes.head-section')

<body class="home9 position-relative " style="@if($landingPage->is_counter_section) padding-bottom:4.687rem; @endif">
    <div class="preloader" id="preloader">
        <div class="preloader-inner">
            <div class="cube-wrapper">
                <div class="cube-folding">
                    <span class="leaf1"></span>
                    <span class="leaf2"></span>
                    <span class="leaf3"></span>
                    <span class="leaf4"></span>
                </div>
                <span class="loading" data-name="Loading">{{__('Loading')}}</span>
            </div>
        </div>
    </div>

    <header class="navigation">
        @if($landingPage->topBars->count()>0)
            @include('landing-pages.includes.top-bars')
        @endif

        <div class="container">
            <div class="row">
                <div class="col-lg-12 p-0">
                    @include('landing-pages.includes.nav')
                </div>
            </div>
        </div>
    </header>

    @if($landingPage->is_home)
        @include('landing-pages.includes.home')
    @endif

    @if($landingPage->title)
        @include('landing-pages.includes.product-criteria')
    @endif

    @if($landingPage->is_about)
        @include('landing-pages.includes.about')
    @endif

    @if($landingPage->is_features1 || $landingPage->is_features2)
        <section class="whaybest">

            @if($landingPage->is_features1_section_top_image)
                <img class="shape2" src="{{asset('storage/'.$landingPage->features1_section_top_image??'assets/images/bg-shape2.png')}}" alt="">
            @endif

            @if($landingPage->is_features2_section_bottom_image)
                <img class="shape" src="{{asset('storage/'.$landingPage->features2_section_bottom_image??'assets/images/bg-shape.png')}}" alt="">
            @endif

            <div class="container ">
                @if($landingPage->is_features1)
                    @include('landing-pages.includes.features1')
                @endif

                @if($landingPage->is_features2)
                    @include('landing-pages.includes.features2')
                @endif
            </div>
        </section>
    @endif

    @if($landingPage->is_features)
        @include('landing-pages.includes.features')
    @endif

    @if($landingPage->is_deal_of_the_week)
        @include('landing-pages.includes.deal-of-the-week')
    @endif

    @if($landingPage->is_products)
        @include('landing-pages.includes.products')
    @endif

    @if($landingPage->is_why_choose_us)
        @include('landing-pages.includes.why-choose-us')
    @endif

    @if($landingPage->is_compare)
        @include('landing-pages.includes.compare')
    @endif

    @if($landingPage->is_feedbacks)
        @include('landing-pages.includes.feedbacks')
    @endif

    @if($landingPage->is_faq)
        @include('landing-pages.includes.faq')
    @endif

    @include('landing-pages.includes.contact')

    @if($landingPage->is_footer)
        @include('landing-pages.includes.footer')
    @endif

    <div class="bottomtotop">
        <i class="fa fa-chevron-right"></i>
    </div>

    <div class="whatsappIcon">
        <a href="https://wa.me/{{$settings->whatsapp_number ?? ''}}" target="_blank">
            <i class="fab fa-whatsapp"></i>
        </a>
    </div>

    @include('landing-pages.includes.checkout')

    @if($landingPage->is_counter_section)
        @include('landing-pages.includes.counter')
    @endif

    @include('landing-pages.includes.script-section')
</body>
</html>
