@extends('layouts.app')

@section('title', 'my cart')

@section('content')
    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    @livewire('shopping-cart')
@endsection
