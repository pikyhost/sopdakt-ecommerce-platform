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
