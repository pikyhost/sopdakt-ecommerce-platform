@extends('layouts.app')

@section('title', __('order_complete'))

@php
    $mainClass = 'main main-test';
@endphp

@section('content')
    @include('order-wizard')
    <div class="container text-center">
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <h1>{{ __('order_created_successfully') }}</h1>
        <p>{{ __('order_confirmation_message') }}</p>
        <br>
        <a href="{{ url('/') }}" class="btn btn-primary">{{ __('go_to_home') }}</a>
    </div>
    <br>
@endsection
