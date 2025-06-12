<nav class="navbar navbar-expand-lg navbar-light">
    <a class="navbar-brand" href="{{route('landing-page.show-by-slug', $landingPage->slug)}}">
        <img src="{{asset('storage/'.$landingPageSettings?->landing_page_header_image)}}" alt="Header Image">
    </a>

    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#mainmenu"
            aria-controls="mainmenu" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="mainmenu">
        <ul class="navbar-nav ml-auto">
            @if($landingPage->is_home)
                @if($landingPageNavItems->where('name','home')->first()?->status)
                    <li class="nav-item active">
                        <a class="nav-link" href="#home">
                            {{__($landingPageNavItems->where('name','home')->first()?->display_name)}}
                        </a>
                    </li>
                @endif
            @endif
            @if($landingPage->is_about)
                @if($landingPageNavItems->where('name','about')->first()?->status)
                    <li class="nav-item">
                        <a class="nav-link" href="#about">
                            {{__($landingPageNavItems->where('name','about')->first()?->display_name)}}
                        </a>
                    </li>
                @endif
            @endif
            @if($landingPage->is_features1)
                @if($landingPageNavItems->where('name','feature1')->first()?->status)
                    <li class="nav-item">
                        <a class="nav-link" href="#features1">
                            {{__($landingPageNavItems->where('name','feature1')->first()?->display_name)}}
                        </a>
                    </li>
                @endif
            @endif
            @if($landingPage->is_features2)
                @if($landingPageNavItems->where('name','feature2')->first()?->status)
                    <li class="nav-item">
                        <a class="nav-link" href="#features2">
                            {{__($landingPageNavItems->where('name','feature2')->first()?->display_name)}}
                        </a>
                    </li>
                @endif
            @endif
            @if($landingPage->is_features)
                @if($landingPageNavItems->where('name','feature3')->first()?->status)
                    <li class="nav-item">
                        <a class="nav-link" href="#feature">
                            {{__($landingPageNavItems->where('name','feature3')->first()?->display_name)}}
                        </a>
                    </li>
                @endif
            @endif
            @if($landingPage->is_deal_of_the_week)
                @if($landingPageNavItems->where('name','deal_of_the_week')->first()?->status)
                    <li class="nav-item">
                        <a class="nav-link" href="#dealofweek">
                            {{__($landingPageNavItems->where('name','deal_of_the_week')->first()?->display_name)}}
                        </a>
                    </li>
                @endif
            @endif
            @if($landingPage->is_products)
                @if($landingPageNavItems->where('name','products')->first()?->status)
                    <li class="nav-item">
                        <a class="nav-link" href="#pricing">
                            {{__($landingPageNavItems->where('name','products')->first()?->display_name)}}
                        </a>
                    </li>
                @endif
            @endif
            @if($landingPage->is_why_choose_us)
                @if($landingPageNavItems->where('name','why_choose_us')->first()?->status)
                    <li class="nav-item">
                        <a class="nav-link" href="#video">
                            {{__($landingPageNavItems->where('name','why_choose_us')->first()?->display_name)}}
                        </a>
                    </li>
                @endif
            @endif
            @if($landingPage->is_compare)
                @if($landingPageNavItems->where('name','compare')->first()?->status)
                    <li class="nav-item">
                        <a class="nav-link" href="#compares">
                            {{__($landingPageNavItems->where('name','compare')->first()?->display_name)}}
                        </a>
                    </li>
                @endif
            @endif
            @if($landingPage->is_feedbacks)
                @if($landingPageNavItems->where('name','feedback')->first()?->status)
                    <li class="nav-item">
                        <a class="nav-link" href="#testimonial-area">
                            {{__($landingPageNavItems->where('name','feedback')->first()?->display_name)}}
                        </a>
                    </li>
                @endif
            @endif
            @if($landingPage->is_faq)
                @if($landingPageNavItems->where('name','faq')->first()?->status)
                    <li class="nav-item">
                        <a class="nav-link" href="#faq">
                            {{__($landingPageNavItems->where('name','faq')->first()?->display_name)}}
                        </a>
                    </li>
                @endif
            @endif
            @if($landingPageNavItems->where('name','contact')->first()?->status)
                <li class="nav-item">
                    <a class="nav-link" href="#contact">
                        {{__($landingPageNavItems->where('name','contact')->first()?->display_name)}}
                    </a>
                </li>
            @endif

        </ul>
    </div>
</nav>
