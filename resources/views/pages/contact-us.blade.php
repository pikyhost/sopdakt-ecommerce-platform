@extends('layouts.app')

@php
    $direction = app()->getLocale() === 'ar' ? 'rtl' : 'ltr';
@endphp

@section('title', __('policy.contact_us'))

@section('content')
    <div class="main contact-two">
        <div id="map"></div><!-- End #map -->

        <div class="container">
            <div class="row ">
                <div class="col-md-8">
                    <h2 class="contact-title">Leave a <strong>Message</strong></h2>

                    <form action="#">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group required-field">
                                    <label for="contact-name">Name</label>
                                    <input type="text" class="form-control" id="contact-name" name="contact-name"
                                           required>
                                </div><!-- End .form-group -->

                                <div class="form-group required-field">
                                    <label for="contact-email">Email</label>
                                    <input type="email" class="form-control" id="contact-email" name="contact-email"
                                           required>
                                </div><!-- End .form-group -->

                                <div class="form-group">
                                    <label for="contact-subject">Subject</label>
                                    <input type="text" class="form-control" id="contact-subject"
                                           name="contact-subject">
                                </div><!-- End .form-group -->
                            </div>

                            <div class="col-lg-6">
                                <div class="form-group required-field mb-0">
                                    <label for="contact-message">Message</label>
                                    <textarea cols="30" rows="1" id="contact-message" class="form-control"
                                              name="contact-message" required></textarea>
                                </div><!-- End .form-group -->

                                <div class="form-footer">
                                    <button type="submit" class="btn btn-primary">Send Message</button>
                                </div><!-- End .form-footer -->
                            </div>
                        </div>
                    </form>
                </div><!-- End .col-md-8 -->

                <div class="col-md-4">
                    <h2 class="contact-title">Contact <strong>Details</strong></h2>

                    <div class="contact-info">
                        <div class="porto-sicon-box d-flex align-items-center">
                            <div class="porto-icon">
                                <i class="fa fa-phone"></i>
                            </div>
                            <div class="porto-sicon-description">
                                0201 203 2032<br>
                                0201 203 2032
                            </div>
                        </div>
                        <div class="porto-sicon-box  d-flex align-items-center">
                            <div class="porto-icon">
                                <i class="fas fa-mobile-alt mobile-phone"></i>
                            </div>
                            <div class="porto-sicon-description">
                                201-123-39223<br>
                                02-123-3928
                            </div>
                        </div>
                        <div class="porto-sicon-box  d-flex align-items-center">
                            <div class="porto-icon">
                                <i class="fa fa-envelope"></i>
                            </div>
                            <div class="porto-sicon-description">
                                porto@gmail.com<br>
                                porto@portotemplate.com
                            </div>
                        </div>
                        <div class="porto-sicon-box  d-flex align-items-center">
                            <div class="porto-icon">
                                <i class="fab fa-skype"></i>
                            </div>
                            <div class="porto-sicon-description">
                                porto_skype<br>
                                porto_templete
                            </div>
                        </div>
                    </div><!-- End .contact-info -->
                </div><!-- End .col-md-4 -->
            </div><!-- End .row -->
        </div><!-- End .container -->
    </div>
@endsection

@push('scripts')<!-- Google Map-->
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDc3LRykbLB-y8MuomRUIY0qH5S6xgBLX4"></script>
    <script src="assets/js/map.js"></script>
@endpush

@push('styles')
    <!-- Main CSS File -->
    <link rel="stylesheet" href="assets/css/demo12.min.css">
@endpush
