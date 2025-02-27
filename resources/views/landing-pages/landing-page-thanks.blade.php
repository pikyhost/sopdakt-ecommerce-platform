<!DOCTYPE html>
<html lang="en">
@include('landing-pages.includes.head-section')
<body>
    <header class="header">
        <h1>{{$settings?->website_name}}</h1>
    </header>

    <main class="main-content">
        <div class="thank-you-container">
            <div class="checkmark-circle">
                <div class="checkmark"></div>
            </div>
            <h2>{{__('Thank You for Your Order!')}}</h2>
            <p dir="rtl">{{__('Your request has been sent, thank you for choosing')}}
                {{$settings?->website_name}}. </p>
            <a href="{{route('landing-page.show-by-slug', $landingPage->slug)}}" class="cta-button">{{__('Continue Shopping')}}</a>
        </div>
    </main>

    <footer class="footer">
        <p>&copy; {{date('Y')}} {{$settings?->website_name}}. {{__('All rights reserved')}}.</p>
    </footer>
</body>
</html>
