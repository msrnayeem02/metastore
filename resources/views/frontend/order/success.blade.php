@extends('frontend.master')

@section('content')
    <div class="container-fluid py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body text-center">
                        <i class="fas fa-check-circle text-success fa-4x mb-3"></i>
                        <h2 class="mb-4">Order Placed Successfully!</h2>
                        <div class="alert alert-info">
                            <h4>Invoice Number: {{ $order->invoice }}</h4>
                        </div>
                        <p class="mb-4">Thank you for your order. We will process it soon.</p>
                        <div class="mt-4">
                            <a href="{{ route('shop') }}" class="btn btn-primary">
                                Continue Shopping
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
