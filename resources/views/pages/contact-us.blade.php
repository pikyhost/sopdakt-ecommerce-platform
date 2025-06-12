@extends('layouts.app')

@php
    $direction = app()->getLocale() === 'ar' ? 'rtl' : 'ltr';
@endphp

@section('title', __('policy.contact_us'))

@section('content')
    <div class="main contact-two">
        <div class="container">
            <div class="row ">
                <div class="col-md-8">
                    <h2 class="contact-title">{{ __('Leave a') }} <strong>{{ __('Message') }}</strong></h2>

                    @livewire('contact-form')
                </div><!-- End .col-md-8 -->

                <div class="col-md-4">
                    <h2 class="contact-title">{{ __('Contact') }} <strong>{{ __('Details') }}</strong></h2>

                    <div class="contact-info">
                        <div class="porto-sicon-box d-flex align-items-center">
                            <div class="porto-icon">
                                <i class="fa fa-phone"></i>
                            </div>
                            <div class="porto-sicon-description">
                                {{ App\Helpers\ContactSettings::get('phone1') }}<br>
                                {{ App\Helpers\ContactSettings::get('phone2') }}
                            </div>
                        </div>
                        <div class="porto-sicon-box  d-flex align-items-center">
                            <div class="porto-icon">
                                <i class="fas fa-mobile-alt mobile-phone"></i>
                            </div>
                            <div class="porto-sicon-description">
                                {{ App\Helpers\ContactSettings::get('mobile1') }}<br>
                                {{ App\Helpers\ContactSettings::get('mobile2') }}
                            </div>
                        </div>
                        <div class="porto-sicon-box  d-flex align-items-center">
                            <div class="porto-icon">
                                <i class="fa fa-envelope"></i>
                            </div>
                            <div class="porto-sicon-description">
                                {{ App\Helpers\ContactSettings::get('email1') }}<br>
                                {{ App\Helpers\ContactSettings::get('email2') }}
                            </div>
                        </div>
                        <div class="porto-sicon-box  d-flex align-items-center">
                            <div class="porto-icon">
                                <i class="fab fa-skype"></i>
                            </div>
                            <div class="porto-sicon-description">
                                {{ App\Helpers\ContactSettings::get('skype1') }}<br>
                                {{ App\Helpers\ContactSettings::get('skype2') }}
                            </div>
                        </div>
                    </div><!-- End .contact-info -->
                </div><!-- End .col-md-4 -->
            </div><!-- End .row -->
        </div><!-- End .container -->
    </div>
@endsection

@push('styles')
    <!-- Main CSS File -->
    <link rel="stylesheet" href="assets/css/demo12.min.css">
@endpush
