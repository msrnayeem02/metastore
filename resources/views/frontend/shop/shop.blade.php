{{-- shop.blade.php --}}
@extends('frontend.master')

@section('content')

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
                </nav>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row px-xl-5">
            <div class="col-lg-3 col-md-4">
                <h5 class="section-title position-relative text-uppercase mb-3">
                    <span class="bg-secondary pr-3">Filter by Category</span>
                </h5>
                <div class="bg-light p-4 mb-30">
                    <form action="{{ route('shop') }}" method="GET" id="filterForm">
                        <select id="category" name="category" class="form-control">
                            <option value="">All Categories</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ request('category') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>

                        <h5 class="section-title position-relative text-uppercase mt-3">
                            <span class="bg-secondary pr-3">Filter by Subcategory</span>
                        </h5>
                        <select id="subcategory" name="subcategory" class="form-control">
                            <option value="">All Subcategories</option>
                            @if (request('category'))
                                @foreach ($categories->where('id', request('category'))->first()->subCategories as $subcategory)
                                    <option value="{{ $subcategory->id }}"
                                        {{ request('subcategory') == $subcategory->id ? 'selected' : '' }}>
                                        {{ $subcategory->name }}
                                    </option>
                                @endforeach
                            @endif
                        </select>

                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary w-100 mb-2">Apply Filters</button>
                            <button type="button" class="btn btn-primary w-100" id="clearFilters">Clear Filters</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-lg-9 col-md-8">
                <div class="row pb-3">
                    @foreach ($products as $product)
                        <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                            <div class="bg-white shadow-sm rounded p-3 h-100 d-flex flex-column justify-content-between">
                                <div class="text-center">
                                    <a href="{{ route('product.details', ['id' => $product->id]) }}"
                                        class="product-img-hover">
                                        <img src="{{ asset($product->image) }}" alt="{{ $product->title }}"
                                            class="img-fluid rounded mb-2"
                                            style="height: 220px; width: 100%; object-fit: cover;">
                                    </a>
                                    <h3 class="mt-2 text-start"><a href="{{ route('product.details', ['id' => $product->id]) }}">{{ $product->title }}</a></h3>

                                    <div class="d-flex justify-content-between align-items-center mt-2">
                                        <div class="d-flex align-items-center">
                                            <h5 class="text-primary mb-0">৳ {{ $product->price }}</h5>
                                            @if ($product->regular_price && $product->regular_price > $product->price)
                                                <h6 class="text-muted mb-0 ms-2">
                                                    <del>৳ {{ $product->regular_price }}</del>
                                                </h6>
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
                                    <button class="btn btn-sm btn-primary w-50 ml-1 order-now"
                                        data-id="{{ $product->id }}">
                                        Order Now
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <div class="col-12">
                        <nav>
                            <ul class="pagination justify-content-center">
                                {{ $products->links() }}
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            const csrfToken = '{{ csrf_token() }}';

            $('#category').on('change', function() {
                const categoryId = $(this).val();
                const $subcategory = $('#subcategory');

                const url = '/categories/' + categoryId;

                $subcategory.html('<option value="">All Subcategories</option>');

                if (categoryId) {
                    $subcategory.prop('disabled', true);
                    $.ajax({
                        url: url,
                        type: 'GET',
                        dataType: 'json',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        success: function(data) {
                            if (Array.isArray(data) && data.length > 0) {
                                data.forEach(function(subcategory) {
                                    $subcategory.append(`<option value="${subcategory.id}">${subcategory.name}</option>`);
                                });
                            }
                        },
                        error: function(xhr) {
                            console.error(xhr);
                        },
                        complete: function() {
                            $subcategory.prop('disabled', false);
                        }
                    });
                }
            });

            $('.add-to-cart').on('click', function(e) {
                e.preventDefault();
                const productId = $(this).data('id');

                $.ajax({
                    url: `/cart/add/${productId}`,
                    type: 'GET',
                    dataType: 'json',
                    headers: {
                        'Accept': 'application/json'
                    },
                    success: function(data) {
                        if (data.totalQuantity !== undefined) {
                            $('#cart-badge').text(data.totalQuantity).hide().fadeIn(300);
                        }
                        alert(data.message || 'Product added to cart');
                    },
                    error: function() {
                        alert('Error adding item to cart');
                    }
                });
            });

            $('.order-now').on('click', function(e) {
                e.preventDefault();
                const productId = $(this).data('id');

                $.ajax({
                    url: `/cart/add/${productId}`,
                    type: 'GET',
                    dataType: 'json',
                    headers: {
                        'Accept': 'application/json'
                    },
                    success: function(data) {
                        if (data.success !== false) {
                            window.location.href = '/cart';
                        } else {
                            alert(data.message || 'Error adding item to cart');
                        }
                    },
                    error: function() {
                        alert('Something went wrong');
                    }
                });
            });

            $('#clearFilters').on('click', function() {
                $('#category, #subcategory').val('');
                window.location.href = '/shop';
            });
        });
    </script>
@endpush
