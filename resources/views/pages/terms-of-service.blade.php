@extends('layouts.app')

@section('title', __('Terms of Service'))

@section('content')
    <br>
    <div>
        <h3>Terms & Conditions</h3>
    </div>
    <div>
            {!! \App\Models\Policy::first()?->{"terms_of_service_" . app()->getLocale()} ?? __('policy.no_policy_found') !!}
    </div>
@endsection
