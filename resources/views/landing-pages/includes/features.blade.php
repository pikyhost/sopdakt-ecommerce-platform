<section class="feature" id="feature">
    @if($landingPage->is_features3_section_top_image)
        <img class="shape2" src="{{asset($landingPage->features3_section_top_image??'assets/images/bg-shape2.png')}}" alt="">
    @endif

    @if($landingPage->is_features3_section_bottom_image)
        <img class="shape" src="{{asset($landingPage->features3_section_bottom_image??'assets/images/bg-shape.png')}}" alt="">
    @endif

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10 col-lg-8">
                <div class="section-title">
                    <h2 class="title">{{$landingPage->feature_title}}</h2>
                    <p class="subtitle">
                        {{$landingPage->feature_subtitle}}
                    </p>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-4">
                <div class="left-feature" data-aos="fade-right" data-aos-duration="1500">
                    @foreach($landingPage->featuresItems->where('type','=','feature')->filter(function ($value, $key) {return $key % 2 == 0;}) as $featureItem)
                        <div class="feature-box">
                            <div class="feature-circle">
                                <div class="feature-circle-inner"><i class="fas fa-plus"></i></div>
                            </div>
                            <div class="details">
                                <h4>
                                    <a href="#" class="title">
                                        {{$featureItem->title}}
                                    </a>
                                </h4>
                                <p class="text">{{$featureItem->subtitle}}</p>
                            </div>
                            <div class="icon-area">
                                <div class="icon">
                                    <img src="{{asset($featureItem->image)}}" alt="">
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="col-lg-4 d-flex justify-content-center">
                <div class="center-feature align-self-center">
                    <img src="{{asset($landingPage->feature_image)}}" alt="">
                    @if($landingPage->is_feature_cta_button)
                        <div class="d-flex justify-content-center mt-4">
                            <a
                                @if($landingPage->feature_cta_button_link)  href="{{$landingPage->feature_cta_button_link}}"
                                @else data-toggle="modal" data-target="#purchaseModal"
                                @endif class="mybtn3 mybtn-bg">
                                <span>{{$landingPage->feature_cta_button_text}}</span>
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <div class="col-lg-4">
                <div class="right-feature">
                    @foreach($landingPage->featuresItems->where('type','=','feature')->filter(function ($value, $key) {return $key % 2 != 0;}) as $featureItem)
                        <div class="feature-box">
                            <div class="feature-circle">
                                <div class="feature-circle-inner"><i class="fas fa-plus"></i></div>
                            </div>
                            <div class="details">
                                <h4>
                                    <a href="#" class="title">
                                        {{$featureItem->title}}
                                    </a>
                                </h4>
                                <p class="text">{{$featureItem->subtitle}}</p>
                            </div>
                            <div class="icon-area">
                                <div class="icon">
                                    <img src="{{asset($featureItem->image)}}" alt="">
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
