<footer class="footer" id="footer">
    @if($landingPage->is_footer_section_top_image)
        <img class="shape2" src="{{asset('storage/'.$landingPage->footer_section_top_image??'assets/images/bg-shape2.png')}}" alt="">
    @endif

    @if($landingPage->is_footer_section_bottom_image)
        <img class="shape" src="{{asset('storage/'.$landingPage->footer_section_bottom_image??'assets/images/bg-shape.png')}}" alt="">
    @endif

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="footer-info-area">
                    <div class="footer-logo">
                        <a href="#" class="logo-link">
                            <img src="{{asset('storage/'.$landingPage->footer_image)}}" alt="">
                        </a>
                    </div>
                    <div class="text">
                        {{$landingPage->footer_subtitle}}
                    </div>
                </div>

                <div class="fotter-social-links">
                    <ul>
                        <li>
                            <a target="_blank" href="{{$settings?->facebook_link}}" class="facebook">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                        </li>
                        <li>
                            <a target="_blank" href="{{$settings?->twitter_link}}" class="twitter">
                                <i class="fab fa-twitter"></i>
                            </a>
                        </li>
                        <li>
                            <a target="_blank" href="{{$settings?->linkedin_link}}" class="linkedin">
                                <i class="fab fa-linkedin-in"></i>
                            </a>
                        </li>
                        <li>
                            <a target="_blank" href="{{$settings?->facebook_link}}" class="google-plus">
                                <i class="fab fa-youtube"></i>
                            </a>
                        </li>
                        <li>
                            <a target="_blank" href="{{$settings?->instagram_link}}"
                            style="background-color: #8a3ab9">
                                <i class="fab fa-instagram"></i>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</footer>
