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
                            {{ $about->about_description_1 ?? 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. has been the industry\'s standard dummy' }}
                        </p>
                        <p>{{ $about->about_description_2 ?? 'long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using "Content here, content here", making it look like readable English. Many desktop publishing packages and web page.' }}</p>
                    </div>
                    <div class="col-md-4">
                        <figure>
                            <img class="m-auto" src="{{ $about->about_image ? Storage::url($about->about_image) : asset('assets/images/demoes/demo12/about/history.jpg') }}"
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
                                        {{ $about->{'accordion_title_'.$i} ?? ($i === 1 ? 'Company History' : ($i === 2 ? 'Our Vision' : ($i === 3 ? 'Our Mission' : 'Funcfacts'))) }}
                                    </a>

                                    <div id="collapse{{ $i }}" class="collapse {{ $i === 1 ? 'show' : '' }}"
                                         data-parent="#accordion">
                                        <p>{{ $about->{'accordion_content_'.$i} ?? 'leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop.' }}</p>
                                    </div>
                                </div>
                            @endfor
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if ($about->team_members || $about->team_members_ar)
            @php
                $locale = app()->getLocale();

                $team = $locale === 'ar' ? $about->team_members_ar : $about->team_members;

                $staticTeamMembers = [
                    ['name' => 'John Doe', 'image' => 'assets/images/demoes/demo10/team/team2.jpg'],
                    ['name' => 'Jessica Doe', 'image' => 'assets/images/demoes/demo10/team/team1.jpg'],
                    ['name' => 'Rick Edward Doe', 'image' => 'assets/images/demoes/demo10/team/team3.jpg'],
                    ['name' => 'Melinda Wolosky', 'image' => 'assets/images/demoes/demo10/team/team4.jpg'],
                ];

                $teamMembersToShow = is_array($team) && count($team) > 0 ? $team : $staticTeamMembers;
            @endphp

            <div class="team-section">
                <div class="container text-center">
                    <h2 class="about-title text-left font4">{{ $about->team_title ?? 'Our Team' }}</h2>

                    <div class="row justify-content-center">
                        @foreach ($teamMembersToShow as $index => $member)
                            @php
                                $name = $member['name'] ?? $staticTeamMembers[$index]['name'];
                                $imagePath = $member['image'] ?? $staticTeamMembers[$index]['image'];
                                $imageUrl = Str::startsWith($imagePath, 'http') || Str::startsWith($imagePath, 'assets/')
                                    ? asset($imagePath)
                                    : Storage::url($imagePath);
                            @endphp

                            <div class="col-md-3 col-6">
                                <div class="team-info mb-3">
                                    <figure>
                                        <a href="#">
                                            <img src="{{ $imageUrl }}"
                                                 data-zoom-image="{{ $imageUrl }}"
                                                 class="w-100" width="270" height="319" alt="Team" />
                                        </a>

                                        <span class="prod-full-screen">
                                    <i class="fas fa-search"></i>
                                </span>
                                    </figure>

                                    <h5 class="team-name text-center mb-0">{{ $name }}</h5>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if ($about?->cta_text && $about?->cta_url)
                        <a class="btn font4" href="{{ $about->cta_url }}">{{ $about->cta_text }}</a>
                    @endif
                </div>
            </div>
        @endif
        
    @if($about->testimonial_content || true)
            <div class="testimonials-section">
                <div class="container">
                    <h2 class="about-title font4 text-center">{{ $about->testimonial_title ?? 'TESTIMONIALS' }}</h2>

                    <div class="row">
                        <div class="col-md-12 offset-xl-3 col-xl-6 offset-lg-2 col-lg-8">
                            <div class="testimonial">
                                <blockquote style="color:#5e6065">
                                    <p>{{ $about->testimonial_content ?? 'Long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using "Content here, content here"' }}</p>
                                </blockquote>

                                <div class="testimonial-owner justify-content-center text-center flex-column">
                                    <figure>
                                        <img src="{{ $about->testimonial_image ? Storage::url($about->testimonial_image) : asset('assets/images/demoes/demo12/clients/client1.jpg') }}" alt="client">
                                    </figure>

                                    <div>
                                        <h5 class="testimonial-title">{{ $about->testimonial_name ?? 'John Doe' }}</h5>
                                        <span>{{ $about->testimonial_role ?? 'Porto Founder' }}</span>
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

@push('styles')
    <link rel="stylesheet" href="assets/css/demo12.min.css">
@endpush
