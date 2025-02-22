<section class="contact" id="contact" style="direction: {{app()->getLocale() == 'ar' ? 'rtl' : 'ltr'}}">
    @if($landingPage->is_contact_us_section_top_image)
        <img class="shape2" src="{{asset($landingPage->contact_us_section_top_image??'assets/images/bg-shape2.png')}}"
            alt="">
    @endif
    @if($landingPage->is_contact_us_section_bottom_image)
        <img class="shape" src="{{asset($landingPage->contact_us_section_bottom_image??'assets/images/bg-shape.png')}}"
            alt="">
    @endif
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10 col-lg-8">
                <div class="section-title">
                    <h2 class="title">{{__('Contact Us')}}</h2>
                    <p class="subtitle">
                        {{__('We are always happy to hear from you. Our entire team is waiting to serve you.')}}
                    </p>
                </div>
            </div>
        </div>
        @php
            $phoneNumbers=\App\Models\WebsiteContactPhone::get();
            $emails=\App\Models\WebsiteContactEmail::get();
        @endphp
        <div class="row">
            @if($phoneNumbers->count())
                <div class="col-lg-4 col-md-6">
                    <div class="info-box box1 " data-aos="fade-right" data-aos-duration="1500">
                        <div class="left">
                            <div class="icon">
                                <i class="fas fa-phone"></i>
                            </div>
                        </div>

                        <div class="right">
                            <div class="content">
                                @foreach($phoneNumbers as $phoneNumber)
                                    <p>{{$phoneNumber->phone}}</p>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            @if($emails->count())
                <div class="col-lg-4 col-md-6">
                    <div class="info-box box2">
                        <div class="left">
                            <div class="icon">
                                <i class="fas fa-envelope"></i>
                            </div>
                        </div>
                        <div class="right">
                            <div class="content">

                                @foreach($emails as $email)
                                    <p>{{$email->email}}</p>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            @if($landingPageSettings?->address)

                <div class="col-lg-4 col-md-6">
                    <div class="info-box box3">
                        <div class="left">
                            <div class="icon">
                                <i class="fas fa-map-marked-alt"></i>
                            </div>
                        </div>
                        <div class="right">
                            <div class="content">
                                <p>{{$landingPageSettings?->address}}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        <div class="row" style="direction: {{app()->getLocale() == 'ar' ? 'rtl' : 'ltr'}}">
            <div class="col-lg-6">
                <div class="contact-form-wrapper  " data-aos="fade-right" data-aos-duration="1500">
                    <form method="POST" >
                        {{-- action="{{route('contact-form.submit')}}" --}}
                        @csrf
                        <div class="row">
                            <input type="hidden" name="landing_page_id" value="{{$landingPage->id}}">
                            <div class="col-md-12">
                                <x-input type="text" class="input-field borderd" id="name" name="name"
                                        placeholder="{{__('Your Name')}}" disable-label="true"
                                        required/>
                            </div>
                            <div class="col-md-12">
                                <x-input type="email" class="input-field borderd" id="email" name="email"
                                        disable-label="true"
                                        placeholder="{{__('Enter Your Email')}}" required/>
                            </div>
                            <div class="col-md-12">
                                <x-input type="text" class="input-field borderd" id="phone" name="phone"
                                        disable-label="true"
                                        placeholder="{{__('Enter Your phone')}}" required/>
                            </div>
                            <div class="col-12">
                                <x-text-area class="input-field borderd textarea" rows="3" id="message" name="message"
                                            disable-label="true"
                                            placeholder="{{__('Write your message here')}}" required></x-text-area>
                            </div>
                            <div class="col-12" style="text-align: {{app()->getLocale() == 'ar' ? 'right' : 'left'}}">
                                <button type="submit" class="mybtn3 mybtn-bg"><span>{{__('Send Message')}}</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            @if($landingPageSettings?->map_link)
                <div class="col-lg-6">
                    <div class="google_map_wrapper h-100 d-flex justify-content-center align-items-center">
                        <iframe
                            src="{{$landingPageSettings?->map_link}}"
                            frameborder="0" style="border:0;" allowfullscreen="" aria-hidden="false"
                            tabindex="0"></iframe>
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>
