@extends('layouts.app')

@php
    $direction = app()->getLocale() === 'ar' ? 'rtl' : 'ltr';
@endphp

@section('title', __('policy.contact_us'))

@section('content')
    <br>
    <div class="container text-center" dir="{{ $direction }}">
        <h1>{{ __('policy.contact_us') }}</h1>
        <div class="content">
            {!! \App\Models\Policy::first()?->{"contact_us_" . app()->getLocale()} ?? __('policy.no_policy_found') !!}
        </div>
    </div>
@endsection
