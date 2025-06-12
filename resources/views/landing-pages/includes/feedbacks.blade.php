<section class="testimonial-area" id="testimonial-area">
    @if($landingPage->is_feedbacks_section_top_image)
        <img class="shape2" src="{{asset('storage/'.$landingPage->feedbacks_section_top_image??'assets/images/bg-shape2.png')}}" alt="">
    @endif

    @if($landingPage->is_feedbacks_section_bottom_image)
        <img class="shape" src="{{asset('storage/'.$landingPage->feedbacks_section_bottom_image??'assets/images/bg-shape.png')}}" alt="">
    @endif

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10 col-lg-8">
                <div class="section-title extra">
                    <h2 class="title">{{$landingPage->feedback_title}}</h2>
                    <p class="subtitle">
                        {{$landingPage->feedback_subtitle}}
                    </p>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-md-10 col-lg-7">
                <div class="testimonial-img  " data-aos="fade-up" data-aos-duration="1500">
                    <img class="img1" src="{{asset('assets/images/testimonialimage/1.jpg')}}" alt="">
                    <img class="img2" src="{{asset('assets/images/testimonialimage/2.jpg')}}" alt="">
                    <img class="img3" src="{{asset('assets/images/testimonialimage/1.jpg')}}" alt="">
                    <img class="img4" src="{{asset('assets/images/testimonialimage/2.jpg')}}" alt="">
                    <img class="img5" src="{{asset('assets/images/testimonialimage/1.jpg')}}" alt="">
                    <img class="img6" src="{{asset('assets/images/testimonialimage/2.jpg')}}" alt="">
                </div>
                <div class="testimonial-slider  owl-carousel owl-theme owl-loaded "
                    data-aos="fade-up" data-aos-duration="1500">
                    @foreach($landingPage->feedbacksItems as $feedbackItem)
                        <div class="item">
                            <div class="client">
                                <div class="client-image">
                                    <img src="{{asset('storage/'.$feedbackItem->image)}}" class="img-fluid"
                                        alt="">
                                </div>
                                <p class="client-say">
                                    {{$feedbackItem->comment}}
                                </p>
                                <h4 class="client-name">
                                    <a href="#">
                                        {{$feedbackItem->user_name}}
                                    </a>
                                </h4>
                                <h5 class="designation"> {{$feedbackItem->user_position}}</h5>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
