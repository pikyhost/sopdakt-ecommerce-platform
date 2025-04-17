@extends('layouts.app')

@section('title', __('order_complete'))

@php
    $direction = app()->getLocale() === 'ar' ? 'rtl' : 'ltr';
@endphp

@section('content')
    @include('order-wizard')

    <div class="container text-center" dir="{{ $direction }}">
        @if(session('success'))
            <div class="alert alert-success text-{{ $direction === 'rtl' ? 'right' : 'left' }}">
                {{ session('success') }}
            </div>
        @endif

        {{-- Thank You GIF --}}
        <img src="https://media.giphy.com/media/111ebonMs90YLu/giphy.gif" alt="Thank You" class="img-fluid my-4" style="max-height: 250px;">

        <h1>{{ __('order_created_successfully') }}</h1>
        <p>{{ __('order_confirmation_message') }}</p>
        <br>
        <a href="{{ url('/') }}" class="btn btn-primary">{{ __('go_to_home') }}</a>
    </div>
    <br>
@endsection
