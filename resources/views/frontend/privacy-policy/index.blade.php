@extends('frontend.master')

@section('content')
    <!-- Breadcrumb Start -->
    <div class="container-fluid">
        <div class="row px-xl-5">
            <div class="col-12">
                <nav class="breadcrumb bg-light mb-30">
                    <a class="breadcrumb-item text-dark" href="{{ route('home') }}">Home</a>
                    <a class="breadcrumb-item text-dark" href="{{ route('shop') }}">Shop</a>
                    <span class="breadcrumb-item active">Privacy Policy</span>
                </nav>
            </div>
        </div>
    </div>
    <!-- Breadcrumb End -->

    <!-- Privacy Policy Section -->
    <div class="container-fluid">
        <div class="row px-xl-5">
            <div class="col-12">
                <h2 class="mb-4">Privacy Policy</h2>
                <p>{!! $policy->privacy_policy ?? 'No privacy policy available.' !!}</p>
            </div>
        </div>

        <div class="row px-xl-5">
            <div class="col-12">
                <h2 class="mb-4">Refund Policy</h2>
                <p>{!! $policy->refund_policy ?? 'No privacy policy available.' !!}</p>
            </div>
        </div>
    </div>
    <!-- Privacy Policy Section End -->
@endsection
