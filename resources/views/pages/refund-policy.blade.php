@extends('layouts.app')

@php
    $direction = app()->getLocale() === 'ar' ? 'rtl' : 'ltr';
@endphp

@section('title', __('My shopping cart'))

@section('content')
    <br>
    <div class="container text-center" dir="{{ $direction }}">

    </div>
@endsection
