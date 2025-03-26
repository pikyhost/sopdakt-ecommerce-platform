@extends('layouts.app')

@php
    $direction = app()->getLocale() === 'ar' ? 'rtl' : 'ltr';
@endphp

@section('title', __('My shopping cart'))

@section('content')
    <br>
    <div class="container text-center" dir="{{ $direction }}">
        <h1>{{ __('policy.privacy_policy') }}</h1>
        <div class="content">
            {!! \App\Models\Policy::first()?->{"privacy_policy_" . app()->getLocale()} ?? __('policy.no_policy_found') !!}
        </div>
    </div>
@endsection
