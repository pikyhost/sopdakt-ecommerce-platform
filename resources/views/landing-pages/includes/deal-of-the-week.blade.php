<section class="dealofweek" id="dealofweek">
    @if($landingPage->is_deal_of_the_week_section_top_image)
        <img class="shape2"
            src="{{asset($landingPage->deal_of_the_week_section_top_image??'assets/images/bg-shape2.png')}}"
            alt="">
    @endif
    @if($landingPage->is_deal_of_the_week_section_bottom_image)
        <img class="shape"
            src="{{asset($landingPage->deal_of_the_week_section_bottom_image??'assets/images/bg-shape.png')}}"
            alt="">
    @endif
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10 col-lg-8">
                <div class="section-title">
                    <h2 class="title">{{$landingPage->deal_of_the_week_title}}</h2>
                    <p class="subtitle">
                        {{$landingPage->deal_of_the_week_subtitle}}
                    </p>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="deal-slider-area  " data-aos="fade-up" data-aos-duration="1500">
                    <div class="deal-slider owl-carousel owl-theme owl-loaded">

                        @foreach($landingPage->dealOfTheWeekItems as $dealOfTheWeekItem)
                            <div class="item">
                                <div class="content">
                                    <div class="row">
                                        <div class="col-lg-6 mb-lg-0 mb-3">
                                            <div class="left-area h-100 ">
                                                <div id="main-slider" class="splide main-slider w-100 mb-2">
                                                    <div class="splide__track">
                                                        <ul class="splide__list">

                                                            @foreach($dealOfTheWeekItem->varieties as $dealOfTheWeekItemVariety)
                                                                @if($dealOfTheWeekItemVariety->image)
                                                                    <li class="splide__slide">
                                                                        <a
                                                                            class="glightbox">
                                                                            <img alt="ecommerce"
                                                                                src="{{asset($dealOfTheWeekItemVariety->image)}}">
                                                                        </a>
                                                                    </li>
                                                                @else
                                                                    <li class="splide__slide">
                                                                        <a
                                                                            class="glightbox">
                                                                            <img alt="ecommerce"
                                                                                src="{{asset($dealOfTheWeekItem->image)}}">
                                                                        </a>
                                                                    </li>
                                                                @endif
                                                            @endforeach

                                                        </ul>
                                                    </div>
                                                </div>

                                                <div id="thumbnail-slider" class="splide thumbnail-slider">
                                                    <div class="splide__track">
                                                        <ul class="splide__list">

                                                            @foreach($dealOfTheWeekItem->varieties as $variety)
                                                                {{--                                                                    @if($dealOfTheWeekItemVariety->image)--}}
                                                                <li title="{{$variety->color->name}}"
                                                                    class="splide__slide d-flex justify-content-center align-items-center">
                                                                    <div title="{{$variety->color->name}}"
                                                                        class="border"
                                                                        style="width: 30px;height: 30px;border-radius: 50%;background-color: {{$variety->color->code}}">
                                                                    </div>
                                                                </li>
                                                                {{--                                                                    @endif--}}
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 d-flex">
                                            <div class="right-area">
                                                <ul class="stars">
                                                    @for($i=0;$i<$dealOfTheWeekItem->rate;$i++)
                                                        <li>
                                                            <i class="fas fa-star"></i>
                                                        </li>
                                                    @endfor
                                                </ul>
                                                <h4 class="name">
                                                    {{$dealOfTheWeekItem->title}}
                                                </h4>
                                                <p class="description">
                                                    {{$dealOfTheWeekItem->subtitle}}
                                                </p>
                                                <div class="price">
                                                    <p>
                                                        <span>
                                                            {{$landingPageSettings?->currency_code}} {{$dealOfTheWeekItem->after_discount_price}}

                                                        </span>
                                                        <del>
                                                            {{$landingPageSettings?->currency_code}} {{$dealOfTheWeekItem->price}}
                                                        </del>
                                                    </p>
                                                </div>
                                                <div class="deal-counter">
                                                    <div data-countdown="{{$dealOfTheWeekItem->end_date}}"></div>
                                                </div>
                                                @if($dealOfTheWeekItem->cta_button)
                                                    <div class="links">
                                                        <a @if($dealOfTheWeekItem->cta_button_link)  href="{{$dealOfTheWeekItem->cta_button_link}}"
                                                        @else
                                                            {{--                                                                   data-toggle="modal" data-target="#purchaseModal"--}}
                                                            onclick="handleCheckout()"
                                                        @endif
                                                        class="mybtn3 mybtn-bg"><span>{{__($dealOfTheWeekItem->cta_button_text)}}</span>
                                                        </a>
                                                    </div>
                                                @endif
                                            </div>


                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
