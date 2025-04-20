@extends('layouts.app')

@section('title', 'Wheel')

@php
    $mainClass = 'main main-test';
@endphp

@section('content')
    <livewire:wheel-spin-component :wheel="$wheel" />
@endsection


@push('scripts')
    <!-- ضعه في layout الرئيسي قبل إغلاق </body> -->
    <script src="//unpkg.com/alpinejs" defer></script>
@endpush
