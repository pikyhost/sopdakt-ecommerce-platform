@extends('layouts.app')

@section('title', 'Category products')

@section('content')
    <div class="category-banner-container bg-gray">
        <div class="category-banner banner text-uppercase position-relative"
             style="background: no-repeat center/cover;
         background-image: url('{{ $category->getMainCategoryImageUrl() ?? asset('assets/images/default-banner.jpg') }}');
         min-height: 200px;">
            <div class="container position-relative">
                <div class="row align-items-center">

                    <!-- Banner Title & CTA Button -->
                    <div class="col-lg-5 col-md-6 col-sm-12 text-center text-md-left">
                        @php
                            $titleColor = $category->title_banner_color ?? '#000000';
                            $ctaBgColor = $category->cta_banner_background_color ?? '#000000';
                            $ctaTextColor = $category->cta_banner_text_color ?? '#ffffff';
                            $ctaUrl = $category->cta_banner_url ?? '#';

                            // Ensure contrast readability if text color is missing
                            if (!$category->cta_banner_text_color) {
                                $r = hexdec(substr(str_replace('#', '', $ctaBgColor), 0, 2));
                                $g = hexdec(substr(str_replace('#', '', $ctaBgColor), 2, 2));
                                $b = hexdec(substr(str_replace('#', '', $ctaBgColor), 4, 2));

                                // Calculate brightness (YIQ formula)
                                $brightness = (($r * 299) + ($g * 587) + ($b * 114)) / 1000;

                                // Choose black or white based on brightness
                                $ctaTextColor = ($brightness > 150) ? '#000000' : '#ffffff';
                            }
                        @endphp

                            <!-- Dynamic Title -->
                        <h3 style="color: {{ $titleColor }}; font-size: 32px; font-weight: bold; line-height: 1.2;">
                            {!! nl2br(e($category->title_banner_text)) !!}
                        </h3>

                        <!-- CTA Button -->
                        <a href="{{ $ctaUrl }}"
                           class="btn"
                           style="background-color: {{ $ctaBgColor }};
                       color: {{ $ctaTextColor }};
                       border: 2px solid {{ $ctaTextColor }};
                       padding: 10px 20px;
                       font-weight: bold;
                       text-transform: uppercase;
                       border-radius: 5px;
                       transition: all 0.3s ease;">
                            {{ $category->cta_banner_text }}
                        </a>
                    </div>

                    <!-- Labels Section -->
                    <div class="coupon-sale-content">
                        @foreach($category->labels as $index => $label)
                            @if($index === 0)
                                <!-- First Label: "Exclusive COUPON" -->
                                <h4 class="m-b-1 coupon-sale-text text-transform-none"
                                    style="background-color: {{ $label->background_color_code ?? '#ffffff' }};
                color: {{ $label->color_code ?? '#000000' }};
                padding: 5px 10px;
                display: inline-block;
                font-weight: bold;
                border-radius: 3px;">
                                    {{ $label->getTranslation('title', app()->getLocale()) }}
                                </h4>
                            @elseif($index === 1)
                                <!-- Second Label: "$100 OFF" -->
                                <h5 class="mb-2 coupon-sale-text d-block ls-10 p-0"
                                    style="font-size: 22px; font-weight: bold;">
                                    <b class="text-dark">{{ $label->getTranslation('title', app()->getLocale()) }}</b>
                                </h5>
                            @endif
                        @endforeach
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <nav aria-label="breadcrumb" class="breadcrumb-nav">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ url('/') }}"><i class="icon-home"></i></a>
                </li>

                @if ($category->parent)
                    <li class="breadcrumb-item">
                        <a href="{{ route('category.products', $category->parent->slug) }}">
                            {{ $category->parent->name }}
                        </a>
                    </li>
                @endif

                <li class="breadcrumb-item active" aria-current="page">
                    {{ $category->name }}
                </li>
            </ol>
        </nav>
        <livewire:category-products-list :category="$category" />

    </div><!-- End .container -->

    <div class="mb-4"></div><!-- margin -->
@endsection
