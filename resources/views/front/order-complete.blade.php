@extends('layouts.app')

@section('title', 'order complete')

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

        <h1>Order Created Successfully</h1>
        <p>Your order has been placed successfully. A confirmation email has been sent to your email address.</p>
        <br>
        <a href="{{ url('/') }}" class="btn btn-primary">Go to Home</a>
    </div>
    <br>
@endsection
