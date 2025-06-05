@extends('frontend.master')

@section('content')

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        .product-img-hover {
            overflow: hidden;
            display: block;
            position: relative;
            border-radius: 8px;
        }

        .product-img-hover img {
            transition: all 0.4s ease-in-out;
        }

        .product-img-hover:hover img {
            transform: scale(1.1) translateY(-10px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }
    </style>

    <div class="container-fluid">
        <div class="row px-xl-5">
            <div class="col-12">
                <nav class="breadcrumb bg-light mb-30">
                    <a class="breadcrumb-item text-dark" href="{{ route('home') }}">Home</a>
                    <a class="breadcrumb-item text-dark" href="{{ route('shop') }}">Shop</a>
                    <span class="breadcrumb-item active">{{ $product->title }}</span>
                </nav>
            </div>
        </div>
    </div>

    <div class="container-fluid pb-5">
        <div class="row px-xl-5">
            <div class="col-lg-5 mb-30">
                <div id="product-carousel" class="carousel slide" data-ride="carousel">
                    <div class="carousel-inner bg-light">
                        <div class="carousel-item active">
                            <img class="w-100 h-100" src="{{ asset($product->image) }}" alt="{{ $product->title }}">
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-7 h-auto mb-30">
                <div class="h-100 bg-light p-30">
                    <h3>{{ $product->title }}</h3>
                    <h3 class="font-weight-semi-bold mb-4">৳{{ $product->price }}</h3>
                    <p class="mb-4">{{ $product->description }}</p>

                    <dl class="row">
                        <dt class="col-sm-3">Category</dt>
                        <dd class="col-sm-9">{{ $product->category?->name ?? 'N/A' }}</dd>

                        <dt class="col-sm-3">Sub-category</dt>
                        <dd class="col-sm-9">{{ $product->subcategory?->name ?? 'N/A' }}</dd>

                        @if (is_array($product->variant_items) && count($product->variant_items) > 0)
                            @foreach ($product->variant_items as $item)
                                @php
                                    $variant = \App\Models\Variant::find($item['variant_id']);
                                    $variantValue = \App\Models\VariantValue::find($item['variant_value_id']);
                                @endphp
                                <dt class="col-sm-3">{{ $variant->name ?? 'N/A' }}</dt>
                                <dd class="col-sm-9">{{ $variantValue->name ?? 'N/A' }}</dd>
                            @endforeach
                        @endif
                    </dl>

                    <div class="d-flex align-items-center mb-4 pt-2">
                        <button class="btn btn-primary px-3 add-to-cart" data-id="{{ $product->id }}">
                            <i class="fa fa-shopping-cart mr-1"></i> Add To Cart
                        </button>
                    </div>

                    <div>
                        <button class="btn btn-primary px-3 buy-now" data-id="{{ $product->id }}">
                            Buy Now
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid py-5">
        <h2 class="section-title position-relative text-uppercase mx-xl-5 mb-4">
            <span class="bg-secondary pr-3">You May Also Like</span>
        </h2>
        <div class="row px-xl-5">
            @foreach ($relatedProducts as $relatedProduct)
                <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                    <div class="bg-white shadow-sm rounded p-3 h-100 d-flex flex-column justify-content-between">
                        <div class="text-center">
                            <a href="{{ route('product.details', ['id' => $relatedProduct->id]) }}"
                                class="product-img-hover">
                                <img src="{{ asset($relatedProduct->image) }}" alt="{{ $relatedProduct->title }}"
                                    class="img-fluid rounded mb-2" style="height: 220px; width: 100%; object-fit: cover;">
                            </a>

                            <h3 class="mt-2 text-start">{{ $relatedProduct->title }}</h3>

                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <div class="d-flex align-items-center">
                                    <h5 class="text-primary mb-0">৳ {{ $relatedProduct->price }}</h5>
                                </div>
                                <p class="mb-0"
                                    style="font-size: 14px; color: {{ $relatedProduct->stock_status == 'In stock' ? 'green' : 'red' }}">
                                    {{ $relatedProduct->stock_status ?? 'In stock' }}
                                </p>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-3">
                            <button class="btn btn-sm btn-outline-primary w-50 mr-1 add-to-cart"
                                data-id="{{ $relatedProduct->id }}">
                                Add To Cart
                            </button>
                            <a href="{{ route('cart.orderNow', ['id' => $relatedProduct->id]) }}"
                                class="btn btn-sm btn-primary w-50 ml-1">
                                Order Now
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        $(document).ready(function() {
            var csrfToken = $('meta[name="csrf-token"]').attr('content');

            $(document).on('click', '.add-to-cart', function(e) {
                e.preventDefault();
                var productId = $(this).data('id');

                $.ajax({
                    url: "/cart/add/" + productId,
                    method: "POST",
                    data: {
                        _token: csrfToken,
                    },
                    success: function(response) {
                        alert(response.message);
                        if (response.totalQuantity !== undefined) {
                            $('#cart-badge').text(response.totalQuantity).hide().fadeIn(200);
                        }
                    },
                    error: function(xhr) {
                        console.log(xhr.responseText);
                        alert('Something went wrong.');
                    }
                });
            });

            $(document).on('click', '.buy-now', function(e) {
                e.preventDefault();
                var productId = $(this).data('id');

                $.ajax({
                    url: "/buy-now/" + productId,
                    method: "POST",
                    data: {
                        _token: csrfToken,
                    },
                    success: function(response) {
                        if (response.success !== false) {
                            window.location.href = "/checkout";
                        } else {
                            alert(response.message);
                        }
                    },
                    error: function(xhr) {
                        console.log(xhr.responseText);
                        alert('Something went wrong.');
                    }
                });
            });
        });
    </script>

@endsection
