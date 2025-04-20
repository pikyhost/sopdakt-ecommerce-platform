@extends('layouts.app')

@section('title', 'Wheel')

@php
    $mainClass = 'main main-test';
@endphp

@section('content')
    @livewire('wheel-spin-component')
@endsection
