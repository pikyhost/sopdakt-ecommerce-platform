@extends('layouts.app')

@php
    $direction = app()->getLocale() === 'ar' ? 'rtl' : 'ltr';
@endphp

@section('title', __('My wishlist'))

@section('content')
    @livewire('wishlist')
@endsection
