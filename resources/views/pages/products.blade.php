@extends('layouts.app')

@section('title', __('Products'))

@section('content')
    <br>
    <div class="container text-center" dir="{{ $direction }}">
        <h1>{{ __('Products') }}</h1>
        <div class="content">
            <p>Welcome to the Products page!</p>
        </div>
    </div>
@endsection
