@extends('layouts.app')

@php
    $direction = app()->getLocale() === 'ar' ? 'rtl' : 'ltr';
@endphp

@section('title', __('policy.about_us'))

@section('content')
    <br>
    <div class="container text-center" dir="{{ $direction }}">
        <h1>{{ __('policy.about_us') }}</h1>
        <div class="content">
            {!! \App\Models\Policy::first()?->{"about_us_" . app()->getLocale()} ?? __('policy.no_policy_found') !!}
        </div>
    </div>
@endsection
