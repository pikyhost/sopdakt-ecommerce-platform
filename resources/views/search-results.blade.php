@extends('layouts.app')

@section('title', __('Search Results'))

@section('content')
    <div class="container py-5">
        <h1 class="mb-4">Search Results for "{{ $query }}"</h1>

        <div class="row">
            <div class="col-md-8 mx-auto">
                @livewire('search-results-page', ['query' => $query])
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <!-- Bootstrap CSS (only for product page) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
@endpush

@push('scripts')
    <!-- Bootstrap JS (only for product page) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@endpush

