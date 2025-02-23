<div id="home" class="hero-area home_section">

    @if($landingPage->is_home_section_top_image)
        <img class="shape2" src="{{asset('storage/'.$landingPage->home_section_top_image??'assets/images/bg-shape2.png')}}" alt="">
    @endif

    @if($landingPage->is_home_section_bottom_image)
        <img class="shape" src="{{asset('storage/'.$landingPage->home_section_bottom_image??'assets/images/bg-shape.png')}}" alt="">
    @endif

    <div class="container">
        <div class="row">
            <div class="col-lg-6 d-flex align-self-center justify-content-center justify-content-md-start">
                <div class="left-content">
                    <div class="content">
                        <h1 class="title">
                            {{$landingPage->home_title}}
                        </h1>

                        <p class="subtitle">
                            {{$landingPage->home_subtitle}}
                        </p>
                        @if($landingPage->home_cta_button)
                            <div class="links">
                                <a @if($landingPage->home_cta_button_link)  href="{{$landingPage->home_cta_button_link}}"
                                @else data-toggle="modal" data-target="#purchaseModal" @endif
                                class="mybtn3 mybtn-bg"><span>{{$landingPage->home_cta_button_text}}</span> </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-lg-6 order-first order-lg-last">
                <div class="right-img">
                    <div class="discount-circle">
                        @if($landingPage->home_discount > 0)
                            <div class="discount-circle-inner">
                                <div class="price">
                                    {{$landingPage->home_discount}}%
                                    <span>Off</span>
                                </div>
                            </div>
                        @endif
                    </div>
                    @if($landingPage->home_image)
                        <img class="img-fluid img" src="{{asset('storage/'.$landingPage->home_image)}}" alt="">
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
