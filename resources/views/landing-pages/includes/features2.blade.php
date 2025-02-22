<div class="row features2" id="features2">
    <div class="col-lg-6">
        <div class="info  " data-aos="fade-right" data-aos-duration="1500">
            <div class="section-title">
                <h2 class="title">{{$landingPage->feature2_title}}</h2>
                <p class="subtitle">
                    {{$landingPage->feature2_subtitle}}
                </p>
            </div>
            <ul class="feature-list">
                @foreach($landingPage->featuresItems()->where('type','feature2')->get() as $featureTwoItem)
                    <li>
                        <div class="icon">
                            <img style="max-width: 80px;max-height: 80px;object-fit: scale-down" src="{{asset($featureTwoItem->image)}}" alt="">
                        </div>
                        <div class="content">
                            <h4 class="title">{{$featureTwoItem->title}}</h4>
                            <p class="subtitle">{{$featureTwoItem->subtitle}}</p>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
    <div class="col-lg-6 d-flex order-first order-lg-last">
        <div class="about-img">
            <img src="{{asset($landingPage->feature2_image)}}" alt="">
            @if($landingPage->is_feature2_cta_button)
                <div class="d-flex justify-content-center mt-4">
                    <a
                        @if($landingPage->feature2_cta_button_link)  href="{{$landingPage->feature2_cta_button_link}}"
                        @else data-toggle="modal" data-target="#purchaseModal"
                        @endif class="mybtn3 mybtn-bg">
                        <span>
                            {{$landingPage->feature2_cta_button_text}}
                        </span>
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
