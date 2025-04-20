@extends('layouts.app')

@section('title', 'Wheel')

@php
    $mainClass = 'main main-test';
@endphp

@section('content')
    @if($wheel)
        <livewire:wheel-spin-component :wheel="$wheel" />
    @else
        <p class="text-red-500">No wheel data available.</p>
    @endif
@endsection
