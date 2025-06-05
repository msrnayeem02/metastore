@extends('frontend.master')

@section('content')
    <!-- Hero Slider Start -->
    @php
        $heroBanners = json_decode(\App\Models\Setting::getValue('hero_banners'), true);
    @endphp
    @if ($heroBanners)
        <div id="heroCarousel" class="carousel slide" data-ride="carousel">
            <div class="carousel-inner">
                @foreach ($heroBanners as $index => $banner)
                    <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                        <img style="height: 500px" src="{{ asset($banner) }}" class="d-block w-100"
                            alt="Banner {{ $index + 1 }}">
                    </div>
                @endforeach
            </div>
            <a class="carousel-control-prev" href="#heroCarousel" role="button" data-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </a>
            <a class="carousel-control-next" href="#heroCarousel" role="button" data-slide="next">
                <span class="carousel-control-next-icon"></span>
            </a>
        </div>
    @endif
    <!-- Hero Slider End -->

    <!--Style for Hover Effect -->
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

    <!-- Products Start -->
    <div class="container pt-5 pb-3">
        <h2 class="section-title position-relative text-uppercase mx-xl-5 mb-4">
            <span class="bg-secondary pr-3">All Products</span>
        </h2>
        <div class="row px-xl-5">
            @foreach ($products as $product)
                <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                    <div class="bg-white shadow-sm rounded p-3 h-100 d-flex flex-column justify-content-between">
                        <div class="text-center">
                            <a href="{{ route('product.details', ['id' => $product->id]) }}"
                                class="product-img-hover">
                                <img src="{{ asset($product->image) }}" alt="{{ $product->title }}"
                                    class="img-fluid rounded mb-2" style="height: 220px; width: 100%; object-fit: cover;">
                            </a>

                            <h3 class="mt-2 text-start">{{ $product->title }}</h3>

                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <div class="d-flex align-items-center">
                                    <h5 class="text-primary mb-0">৳ {{ $product->price }}</h5>
                                    @if ($product->regular_price && $product->regular_price > $product->price)
                                        <h6 class="text-muted mb-0 ms-2"><del>৳ {{ $product->regular_price }}</del></h6>
                                    @endif
                                </div>
                                <p class="mb-0"
                                    style="font-size: 14px; color: {{ $product->stock_status == 'In stock' ? 'green' : 'red' }}">
                                    {{ $product->stock_status ?? 'In stock' }}
                                </p>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-3">
                            <button class="btn btn-sm btn-outline-primary w-50 mr-1 add-to-cart"
                                data-id="{{ $product->id }}">
                                Add To Cart
                            </button>
                            <a href="{{ route('cart.orderNow', ['id' => $product->id]) }}"
                                class="btn btn-sm btn-primary w-50 ml-1">
                                Order Now
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    <!-- Products End -->

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.add-to-cart').on('click', function(e) {
                e.preventDefault();
                var productId = $(this).data('id');

                $.ajax({
                    url: "{{ route('cart.add', ['id' => ':id']) }}".replace(':id', productId),
                    method: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                    },
                    success: function(response) {
                        alert(response.message);
                        if (response.totalQuantity !== undefined) {
                            $('#cart-badge').text(response.totalQuantity).hide().fadeIn(200);
                        }
                    },
                    error: function(error) {
                        console.log(error);
                    }
                });
            });
        });
    </script>
@endsection
