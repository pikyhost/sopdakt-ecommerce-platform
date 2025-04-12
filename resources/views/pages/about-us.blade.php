@extends('layouts.app')

@php
    $direction = app()->getLocale() === 'ar' ? 'rtl' : 'ltr';
    $about = App\Models\AboutUs::first();
@endphp

@section('title', __('About Us'))

@section('content')
    <br>
    <div class="about-us" dir="{{ $direction }}">
        <div class="page-header page-header-bg text-left"
             style="background: 50%/cover #D4E1EA url('assets/images/demoes/demo12/page-header-bg.jpg');">
            <div class="container">
                <h1>{{ $about->header_title ?? 'WHO WE ARE' }}</h1>
            </div><!-- End .container -->
        </div><!-- End .page-header -->

        <nav aria-label="breadcrumb" class="breadcrumb-nav">
            <div class="container">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ $about->breadcrumb_home ?? 'Home' }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $about->breadcrumb_current ?? 'About Us' }}</li>
                </ol>
            </div><!-- End .container -->
        </nav>

        <div class="container">
            <div class="history-section mt-4 pb-2 mb-6">
                <div class="row justify-content-center">
                    <div class="col-md-4">
                        <h2 class="about-title font4">{{ $about->about_title ?? 'ABOUT US' }}</h2>
                        <p class="text-bg">
                            {{ $about->about_description_1 ?? 'Lorem Ipsum is simply dummy text of the printing and typesetting industry.' }}
                        </p>
                        <p>
                            {{ $about->about_description_2 ?? 'Long established fact that a reader will be distracted by the readable content of a page when looking at its layout.' }}
                        </p>
                    </div>
                    <div class="col-md-4">
                        <figure>
                            <img class="m-auto" src="{{ $about->about_image ?? 'assets/images/demoes/demo12/about/history.jpg' }}"
                                 width="307" height="371" alt="history image" />
                        </figure>
                    </div>
                    <div class="col-md-4">
                        <div class="accordion-section" id="accordion">
                            @for($i = 1; $i <= 4; $i++)
                                <div class="card card-accordion">
                                    <a class="card-header {{ $i > 1 ? 'collapsed' : '' }}" href="#"
                                       data-toggle="collapse" data-target="#collapse{{ $i }}"
                                       aria-expanded="{{ $i === 1 ? 'true' : 'false' }}"
                                       aria-controls="collapse{{ $i }}">
                                        {{ $about->{'accordion_title_'.$i} ?? 'Section '.$i }}
                                    </a>

                                    <div id="collapse{{ $i }}" class="collapse {{ $i === 1 ? 'show' : '' }}"
                                         data-parent="#accordion">
                                        <p>{{ $about->{'accordion_content_'.$i} ?? 'Content for section '.$i }}</p>
                                    </div>
                                </div>
                            @endfor
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if($about->team_members)
            <div class="team-section">
                <div class="container text-center">
                    <h2 class="about-title text-left font4">{{ $about->team_title ?? 'OUR TEAM' }}</h2>

                    <div class="row justify-content-center">
                        @foreach($about->team_members as $member)
                            <div class="col-md-3 col-6">
                                <div class="team-info mb-3">
                                    <figure>
                                        <a href="#">
                                            <img src="{{ asset($member['image'] ?? 'assets/images/demoes/demo10/team/team1.jpg') }}"
                                                 data-zoom-image="{{ asset($member['image'] ?? 'assets/images/demoes/demo10/team/team1.jpg') }}"
                                                 class="w-100" width="270" height="319" alt="Team" />
                                        </a>

                                        <span class="prod-full-screen">
                    <i class="fas fa-search"></i>
                </span>
                                    </figure>

                                    <h5 class="team-name text-center mb-0">{{ $member['name'] ?? 'Team Member' }}</h5>
                                </div>
                            </div>
                        @endforeach
                    </div><!-- End .row -->

                    <a class="btn font4" href="#">JOIN OUR TEAM</a>
                </div>
            </div>
        @endif

        @if($about->testimonial_content)
            <div class="testimonials-section">
                <div class="container">
                    <h2 class="about-title font4 text-center">{{ $about->testimonial_title ?? 'TESTIMONIALS' }}</h2>

                    <div class="row">
                        <div class="col-md-12 offset-xl-3 col-xl-6 offset-lg-2 col-lg-8">
                            <div class="testimonial">
                                <blockquote style="color:#5e6065">
                                    <p>{{ $about->testimonial_content }}</p>
                                </blockquote>

                                <div class="testimonial-owner justify-content-center text-center flex-column">
                                    <figure>
                                        <img src="{{ $about->testimonial_image ?? 'assets/images/demoes/demo12/clients/client1.jpg' }}"
                                             alt="client">
                                    </figure>

                                    <div>
                                        <h5 class="testimonial-title">{{ $about->testimonial_name }}</h5>
                                        <span>{{ $about->testimonial_role }}</span>
                                    </div>
                                </div><!-- End .testimonial-owner -->
                            </div><!-- End .testimonial -->
                        </div>
                    </div>
                </div><!-- End .container -->
            </div><!-- End .testimonials-section -->
        @endif
    </div>
@endsection
