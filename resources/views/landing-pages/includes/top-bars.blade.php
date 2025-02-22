<div class="topbar ">
    <!-- Language Switcher -->
    <div class="language-switcher d-flex justify-content-end">
        @foreach(['en', 'ar'] as $locale)
            @if(app()->getLocale() !== $locale)
                <a href="{{ LaravelLocalization::getLocalizedURL($locale, null, [], true) }}"
                class="btn btn-sm {{ $locale === 'ar' ? 'btn-info' : 'btn-dark' }}"
                style="margin: 0 5px">
                    {{ strtoupper($locale) }}
                </a>
            @endif
        @endforeach
    </div>
    <!-- Left Section -->
    <div class="swiper-container left-swiper d-none d-lg-flex">
        <div class="swiper-wrapper">
            @if($landingPage->topBars->count()>1)
                @foreach($landingPage->topBars()->inRandomOrder()->get() as $topBar)
                    <a href="{{$topBar->link}}" target="_blank"
                    class="swiper-slide text-white">{{$topBar->title}}</a>
                @endforeach
            @endif
        </div>
    </div>

    <!-- Center Section -->
    <div class="swiper-container center-swiper"
        style="background-color: var(--topBar-dark-background-color);height: 100%">
        <div class="swiper-wrapper">
            @if($landingPage->topBars->count()>0)
                @foreach($landingPage->topBars()->inRandomOrder()->get() as $topBar)
                    <a href="{{$topBar->link}}" target="_blank"
                    class="swiper-slide" style="color: var(--topBar-dark-text-color)">{{$topBar->title}}</a>
                @endforeach
            @endif
        </div>
    </div>

    <!-- Right Section -->
    <div class="swiper-container right-swiper d-none d-lg-flex">
        <div class="swiper-wrapper">
            @if($landingPage->topBars->count()>1)
                @foreach($landingPage->topBars()->inRandomOrder()->get() as $topBar)
                    <a href="{{$topBar->link}}" target="_blank"
                    class="swiper-slide text-white">{{$topBar->title}}</a>
                @endforeach
            @endif
        </div>
    </div>
</div>
