@extends('layouts.app')

@section('title', 'Wheel')

@php
    $mainClass = 'main main-test';
@endphp

@section('content')
    <livewire:wheel-spin-component :wheel="$wheel" />
@endsection

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f8;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            text-align: center;
        }

        #wheelCanvas {
            border: 6px solid #007bff;
            border-radius: 50%;
            background: white;
            transition: transform 5s cubic-bezier(0.33, 1, 0.68, 1);
        }

        .pointer {
            position: absolute;
            top: -15px;
            left: 50%;
            transform: translateX(-50%) rotate(180deg);
            font-size: 3rem;
            color: red;
        }

        .wheel-container {
            position: relative;
            width: 350px;
            height: 350px;
        }
    </style>
@endpush
