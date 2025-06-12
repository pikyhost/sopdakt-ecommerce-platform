@extends('layouts.app')

@section('title', 'Checkout')

@php
    $mainClass = 'main main-test';
@endphp

@section('content')
    @livewire('checkout')
@endsection
