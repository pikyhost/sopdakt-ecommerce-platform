@extends('layouts.app')

@php
    $direction = app()->getLocale() === 'ar' ? 'rtl' : 'ltr';
@endphp

@section('title', __('My shopping cart'))

@section('content')
    <br>
    <div class="container text-center" dir="{{ $direction }}">
    @if(session('error'))
        <div class="alert alert-danger text-{{ $direction === 'rtl' ? 'right' : 'left' }}">
            {{ session('error') }}
        </div>
    @endif
    </div>

    @livewire('shopping-cart')
@endsection
