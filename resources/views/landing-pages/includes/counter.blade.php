<div class="count-down-container d-none d-flex fadeIn" style="z-index: 100;">
    <div data-countdown="{{$landingPage->counter_section_end_date}}"></div>
    <a class="btn btn-primary rounded-0"
    @if($landingPage->counter_section_cta_button_link)
        href="{{$landingPage->counter_section_cta_button_link}}"
    @else
        onclick="handleCheckout()"
        @endif >{{$landingPage->counter_section_cta_button_text}}</a>
</div>
