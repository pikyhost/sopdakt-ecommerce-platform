<section id="faq" class="faq">
    @if($landingPage->is_faq_section_top_image)
        <img class="shape2" src="{{asset('storage/'.$landingPage->faq_section_top_image??'assets/images/bg-shape2.png')}}" alt="">
    @endif

    @if($landingPage->is_faq_section_bottom_image)
        <img class="shape" src="{{asset('storage/'.$landingPage->faq_section_bottom_image??'assets/images/bg-shape.png')}}" alt="">
    @endif

    <div class="container">
        <div class="section-title extra">
            <h2 class="title">{{$landingPage->faq_title}}</h2>
            <p class="subtitle">
                {{$landingPage->faq_subtitle}}
            </p>
        </div>
        <div class="row">
            <div class="col-lg-6">
                @foreach($landingPage->faqsItems as $faqItem)
                    <div class="panel-group accordion  " id="accordion-{{$faqItem->id}}"
                        data-aos="fade-right" data-aos-duration="1500">
                        <div class="panel">
                            <div class="panel-heading">
                                <h4 data-toggle="collapse" aria-expanded="false" data-target="#one{{$faqItem->id}}"
                                    aria-controls="one"
                                    class="panel-title collapsed">
                                    {{$faqItem->question}}
                                </h4>
                            </div>
                            <div id="one{{$faqItem->id}}" class="panel-collapse collapse"
                                aria-labelledby="one{{$faqItem->id}}"
                                data-parent="#accordion-{{$faqItem->id}}">
                                <div class="panel-body">
                                    {{$faqItem->answer}}
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="col-lg-6 d-flex justify-content-center align-items-center">
                <div class="faq-img  " data-aos="fade-left" data-aos-duration="1500">
                    <img src="{{asset('storage/'.$landingPage->faq_image)}}" alt="faq-image" class=" ">
                </div>
            </div>
        </div>
    </div>
</section>
