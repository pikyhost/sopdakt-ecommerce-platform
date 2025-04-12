@extends('layouts.app')

@php
    use Illuminate\Support\Facades\Storage;
    $direction = app()->getLocale() === 'ar' ? 'rtl' : 'ltr';
    $about = App\Models\AboutUs::first();
@endphp

@section('title', __('About Us'))

@section('content')
    <br>
    <div class="about-us" dir="{{ $direction }}">
        <div class="page-header page-header-bg text-left"
             style="background: 50%/cover #D4E1EA url('{{ asset('assets/images/demoes/demo12/page-header-bg.jpg') }}');">
            <div class="container">
                <h1>{{ $about->header_title ?? __('WHO WE ARE') }}</h1>
            </div>
        </div>

        <nav aria-label="breadcrumb" class="breadcrumb-nav">
            <div class="container">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ url('/') }}">{{ $about->breadcrumb_home ?? __('Home') }}</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        {{ $about->breadcrumb_current ?? __('About Us') }}
                    </li>
                </ol>
            </div>
        </nav>

        <div class="container">
            <div class="history-section mt-4 pb-2 mb-6">
                <div class="row justify-content-center">
                    <div class="col-md-4">
                        <h2 class="about-title font4">{{ $about->about_title ?? __('ABOUT US') }}</h2>
                        <p class="text-bg">{{ $about->about_description_1 }}</p>
                        <p>{{ $about->about_description_2 }}</p>
                    </div>
                    <div class="col-md-4">
                        <figure>
                            <img class="m-auto" src="{{ $about->about_image ? Storage::url($about->about_image) : asset('assets/images/demoes/demo12/about/history.jpg') }}"
                                 width="307" height="371" alt="about image" />
                        </figure>
                    </div>
                    <div class="col-md-4">
                        <div class="accordion-section" id="accordion">
                            @for($i = 1; $i <= 4; $i++)
                                @php
                                    $title = $about->{'accordion_title_'.$i} ?? __('Section ') . $i;
                                    $content = $about->{'accordion_content_'.$i} ?? __('Content for section ') . $i;
                                @endphp
                                <div class="card card-accordion">
                                    <a class="card-header {{ $i > 1 ? 'collapsed' : '' }}"
                                       data-toggle="collapse"
                                       data-target="#collapse{{ $i }}"
                                       aria-expanded="{{ $i === 1 ? 'true' : 'false' }}"
                                       aria-controls="collapse{{ $i }}"
                                       href="#">
                                        {{ $title }}
                                    </a>
                                    <div id="collapse{{ $i }}" class="collapse {{ $i === 1 ? 'show' : '' }}" data-parent="#accordion">
                                        <p>{{ $content }}</p>
                                    </div>
                                </div>
                            @endfor
                        </div>
                    </div>
                </div>
            </div>
        </div>


        {{-- Team Section --}}
        @if($about->team_members)
            <div class="team-section py-5 bg-light" dir="{{ app()->isLocale('ar') ? 'rtl' : 'ltr' }}">
                <div class="container">
                    <h2 class="bout-title font4 text-center mb-5">{{ $about->team_title ?? 'OUR TEAM' }}</h2>

                    <div class="row justify-content-center">
                        @foreach($about->team_members as $memberRaw)
                            @php
                                $member = is_array($memberRaw) ? $memberRaw : (array) $memberRaw;
                            @endphp

                            <div class="col-md-3 col-6 mb-4">
                                <div class="team-info">
                                    <figure class="position-relative overflow-hidden">
                                        <img src="{{ isset($member['image']) ? Storage::url($member['image']) : asset('assets/images/demoes/demo10/team/team1.jpg') }}"
                                             class="w-100 team-img-style"
                                             width="270"
                                             height="319"
                                             alt="{{ $member['name'] ?? 'Team Member' }}"
                                             data-zoom-image="{{ isset($member['image']) ? Storage::url($member['image']) : asset('assets/images/demoes/demo10/team/team1.jpg') }}">

                                        <span class="prod-full-screen"
                                              onclick="showLightbox('{{ isset($member['image']) ? Storage::url($member['image']) : asset('assets/images/demoes/demo10/team/team1.jpg') }}', '{{ $member['name'] ?? 'Team Member' }}')">
                    <i class="fas fa-search-plus"></i>
                </span>
                                    </figure>

                                    <h5 class="team-name text-center mb-0">{{ $member['name'] ?? 'Team Member' }}</h5>
                                    @if(isset($member['position']))
                                        <p class="text-muted text-center">{{ $member['position'] }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach

                    </div>

                    <div class="text-center mt-4">
                        <a class="custom-join-btn" href="#">{{ __('Join Our Team') }}</a>
                    </div>
                </div>
            </div>

            {{-- Lightbox --}}
            <div id="lightbox" class="lightbox" onclick="hideLightbox()">
                <span class="close-btn" onclick="event.stopPropagation(); hideLightbox()">&times;</span>
                <img class="lightbox-content" id="lightbox-img">
                <div class="lightbox-caption" id="lightbox-caption"></div>
            </div>

            <style>
                /* Base styles */
                .team-info {
                    transition: all 0.3s ease;
                    padding: 15px;
                    border-radius: 8px;
                }

                .team-info:hover {
                    transform: translateY(-5px);
                    box-shadow: 0 10px 20px rgba(0,0,0,0.1);
                }

                .team-img-style {
                    width: 100%;
                    height: 320px;
                    object-fit: cover;
                    border-radius: 8px;
                    transition: transform 0.3s;
                    cursor: pointer;
                }

                .team-info:hover .team-img-style {
                    transform: scale(1.03);
                }

                .prod-full-screen {
                    position: absolute;
                    top: 15px;
                    right: 15px;
                    background-color: rgba(0, 0, 0, 0.5);
                    color: white;
                    padding: 8px 10px;
                    border-radius: 50%;
                    font-size: 1rem;
                    cursor: pointer;
                    transition: all 0.3s;
                    z-index: 2;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    width: 36px;
                    height: 36px;
                }

                .prod-full-screen:hover {
                    background-color: rgba(0, 0, 0, 0.8);
                    transform: scale(1.1);
                }

                .team-name {
                    margin-top: 15px;
                    font-weight: 600;
                    color: #333;
                }

                /* Lightbox styling */
                .lightbox {
                    display: none;
                    position: fixed;
                    z-index: 9999;
                    left: 0;
                    top: 0;
                    width: 100%;
                    height: 100%;
                    background-color: rgba(0, 0, 0, 0.9);
                    text-align: center;
                    padding-top: 50px;
                }

                .lightbox-content {
                    max-width: 90%;
                    max-height: 80vh;
                    margin: 0 auto;
                    display: block;
                    animation: fadeIn 0.3s;
                }

                @keyframes fadeIn {
                    from {opacity: 0;}
                    to {opacity: 1;}
                }

                .close-btn {
                    position: absolute;
                    top: 20px;
                    right: 30px;
                    color: white;
                    font-size: 40px;
                    font-weight: bold;
                    cursor: pointer;
                    transition: 0.3s;
                }

                .close-btn:hover {
                    color: #ccc;
                }

                .lightbox-caption {
                    color: white;
                    padding: 15px;
                    font-size: 1.2rem;
                    text-align: center;
                }

                /* RTL specific styles */
                [dir="rtl"] .prod-full-screen {
                    right: auto;
                    left: 15px;
                }

                [dir="rtl"] .close-btn {
                    right: auto;
                    left: 30px;
                }

                /* Button styles */
                .custom-join-btn {
                    background-color: #4a4a4a;
                    color: white !important;
                    padding: 10px 25px;
                    font-weight: bold;
                    text-transform: uppercase;
                    border: none;
                    text-align: center;
                    display: inline-block;
                }

                .custom-join-btn:hover {
                    background-color: #4a4a4a;
                    text-decoration: none;
                }
            </style>
        @endif

        {{-- Testimonial Section --}}
        @if($about->testimonial_content)
            <div class="testimonials-section py-5 bg-white">
                <div class="container">
                    <h2 class="about-title font4 text-center mb-5">{{ $about->testimonial_title ?? __('TESTIMONIALS') }}</h2>
                    <div class="row justify-content-center">
                        <div class="col-md-8">
                            <div class="testimonial text-center px-4">
                                <span class="display-3 text-primary">“</span>
                                <p class="mb-4 text-muted" style="font-size: 1.2rem;">
                                    {{ $about->testimonial_content }}
                                </p>
                                <span class="display-3 text-primary">”</span>

                                <div class="testimonial-owner d-flex flex-column align-items-center mt-4">
                                    <img src="{{ $about->testimonial_image ? Storage::url($about->testimonial_image) : asset('assets/images/demoes/demo10/team/team1.jpg') }}"
                                         alt="Testimonial"
                                         class="testimonial-img-style mb-3" />

                                    <h5 class="testimonial-title mb-1">{{ $about->testimonial_name }}</h5>
                                    <span class="text-muted">{{ $about->testimonial_role }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <style>
                    .testimonial-img-style {
                        width: 100px;
                        height: 100px;
                        object-fit: cover;
                        border-radius: 50%;
                        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
                        border: 3px solid #eaeaea;
                    }

                    @media (max-width: 576px) {
                        .testimonial-img-style {
                            width: 80px;
                            height: 80px;
                        }
                    }
                </style>
            </div>
        @endif
    </div>
@endsection
