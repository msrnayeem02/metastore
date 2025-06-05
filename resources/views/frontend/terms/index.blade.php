@extends('frontend.master')

@section('content')
    <!-- Breadcrumb Start -->
    <div class="container-fluid">
        <div class="row px-xl-5">
            <div class="col-12">
                <nav class="breadcrumb bg-light mb-30">
                    <a class="breadcrumb-item text-dark" href="{{ route('home') }}">Home</a>
                    <a class="breadcrumb-item text-dark" href="{{ route('shop') }}">Shop</a>
                    <span class="breadcrumb-item active">Terms Conditions</span>
                </nav>
            </div>
        </div>
    </div>
    <!-- Breadcrumb End -->

    <!-- Terms Conditions Section -->
    <div class="container-fluid">
        <div class="row px-xl-5">
            <div class="col-12">
                <h2 class="mb-4">Terms Conditions</h2>
                <p>{!! $policy->terms_conditions ?? 'No Terms Conditions available.' !!}</p>
            </div>
        </div>
    </div>
    <!-- Terms Conditions Section End -->
@endsection
