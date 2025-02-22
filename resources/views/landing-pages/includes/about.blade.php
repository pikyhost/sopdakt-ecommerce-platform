<section class="about about_section" id="about">
    @if($landingPage->is_about_section_top_image)
        <img class="shape2" src="{{asset($landingPage->about_section_top_image??'assets/images/bg-shape2.png')}}"
            alt="">
    @endif

    @if($landingPage->is_about_section_bottom_image)
        <img class="shape" src="{{asset($landingPage->about_section_bottom_image??'assets/images/bg-shape.png')}}"
            alt="">
    @endif

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10 col-lg-8">
                <div class="section-title extra">
                    <h2 class="title">{{$landingPage->about_title}}</h2>
                    <p class="subtitle">
                        {{$landingPage->about_subtitle}}
                    </p>
                </div>
            </div>
        </div>
        <div class="row">
            @foreach($landingPage->aboutItems as  $aboutItem)
                <div class="col-lg-4">
                    <div class="box">
                        <div class="inner-box">
                            <div class="icon">
                                <img src="{{asset($aboutItem->image)}}" alt="">
                            </div>
                            <h4 class="title">{{$aboutItem->title}}</h4>
                            <p class="text">
                                {{$aboutItem->subtitle}}
                            </p>
                            @if($aboutItem->is_cta_button)
                                <div class="d-flex justify-content-center mt-2">
                                    <a @if($aboutItem->cta_button_link)  href="{{$aboutItem->cta_button_link}}"
                                    @else
                                        data-toggle="modal" data-target="#purchaseModal"
                                    @endif
                                    class="mybtn3 mybtn-bg"><span>
                                        {{$aboutItem->cta_button_text}}
                                        </span>
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
