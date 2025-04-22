@extends('layouts.app')

@php
    $direction = app()->getLocale() === 'ar' ? 'rtl' : 'ltr';
@endphp

@section('title', __('Pay Online'))

@section('content')
    <br>
    <div class="max-w-4xl mx-auto p-4">
        <h2 class="text-xl font-semibold mb-4">Complete Your Payment</h2>

        <iframe
            src="{{ $iframeUrl }}"
            frameborder="0"
            style="width: 100%; height: 600px;"
            allowfullscreen>
        </iframe>
    </div>
@endsection
