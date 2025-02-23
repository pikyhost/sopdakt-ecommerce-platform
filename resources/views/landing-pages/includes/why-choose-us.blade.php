<section class="video" id="video">
    @if($landingPage->is_why_choose_us_section_top_image)
        <img class="shape2" src="{{ asset('assets/images/bg-shape2.png') }}" alt="Background Shape">
    @endif

    @if($landingPage->is_why_choose_us_section_bottom_image)
        <img class="shape" src="{{asset('storage/'.$landingPage->why_choose_us_section_bottom_image??'assets/images/bg-shape.png')}}" alt="">
    @endif

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10 col-lg-8">
                <div class="section-title">
                    <h2 class="title">{{$landingPage->why_choose_us_title}}</h2>
                    <p class="subtitle">
                        {{$landingPage->why_choose_us_subtitle}}
                    </p>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6">
                <div class="video-wrapper">
                    <div class="video-box  " data-aos="fade-right" data-aos-duration="1500">
                        <div class="overly"></div>
                        <div class="play-icon">
                            <a href="{{asset($landingPage->why_choose_us_video)}}"
                            class="video-play-btn mfp-iframe">
                                <i class="fas fa-play"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="row  " data-aos="fade-left" data-aos-duration="1500">
                    @foreach($landingPage->whyChooseUsItems as $whyChooseUsItem)
                        <div class="col-lg-6">
                            <div class="fun-box">
                                <div class="inner-content "
                                    style="background-color: {{$whyChooseUsItem->background_color}};">
                                    <div class="icon">
                                        <img src="{{asset('storage/'.$whyChooseUsItem->image)}}">
                                    </div>
                                    <h5 class="categori"
                                        style="color:{{$whyChooseUsItem->text_color}}">{{$whyChooseUsItem->title}}</h5>
                                </div>
                            </div>
                        </div>
                    @endforeach


                </div>
            </div>
        </div>
    </div>
</section>
