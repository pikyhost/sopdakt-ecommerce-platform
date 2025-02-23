<div class="row row-one features1" id="features1">
    <div class="col-lg-6 d-flex">
        <div class="about-img">
            <img src="{{asset('storage/'.$landingPage->feature1_image)}}" alt="">
            @if($landingPage->is_feature1_cta_button)
                <div class="d-flex justify-content-center mt-4">
                    <a
                        @if($landingPage->feature1_cta_button_link)  href="{{$landingPage->feature1_cta_button_link}}"
                        @else data-toggle="modal" data-target="#purchaseModal"
                        @endif
                        class="mybtn3 mybtn-bg">
                        <span>
                            {{$landingPage->feature1_cta_button_text}}
                        </span>
                    </a>
                </div>
            @endif
        </div>
    </div>
    <div class="col-lg-6">
        <div class="info" data-aos="fade-left" data-aos-duration="1500">
            <div class="section-title">
                <h2 class="title">{{$landingPage->feature1_title}}</h2>
                <p class="subtitle">
                    {{$landingPage->feature1_subtitle}}
                </p>
            </div>
            <ul class="feature-list">
                @foreach($landingPage->featuresItems()->where('type','feature1')->get() as $featureOneItem)
                    <li>
                        <div class="icon">
                            <img style="max-width: 80px;max-height: 80px;object-fit: scale-down"
                                src="{{asset('storage/'.$featureOneItem->image)}}" alt="">
                        </div>
                        <div class="content">
                            <h4 class="title">{{$featureOneItem->title}}</h4>
                            <p class="subtitle">{{$featureOneItem->subtitle}}
                            </p>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
