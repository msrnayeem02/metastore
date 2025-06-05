@extends('frontend.master')

@section('content')
    <!-- Breadcrumb Start -->
    <div class="container-fluid">
        <div class="row px-xl-5">
            <div class="col-12">
                <nav class="breadcrumb bg-light mb-30">
                    <a class="breadcrumb-item text-dark" href="{{ route('home') }}">Home</a>
                    <a class="breadcrumb-item text-dark" href="{{ route('shop') }}">Shop</a>
                    <a class="breadcrumb-item text-dark" href="{{ route('cart') }}">Shopping Cart</a>
                </nav>
            </div>
        </div>
    </div>
    <!-- Breadcrumb End -->

    <!-- Cart Start -->
    <div class="container-fluid">
        <div class="row px-xl-5">
            <div class="col-lg-8 table-responsive mb-5">
                <div class="container">
                    <h2>Shopping Cart</h2>
                    <table class="table table-light table-borderless table-hover text-center mb-0">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Subtotal</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $total = 0; @endphp
                            @foreach (session('cart', []) as $id => $item)
                                <tr data-id="{{ $id }}">
                                    <td>{{ $item['title'] }}</td>
                                    <td>${{ number_format($item['price'], 2) }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-primary btn-decrease">-</button>
                                        <input type="text" class="form-control d-inline text-center quantity-input"
                                            value="{{ $item['quantity'] }}" style="width: 50px;" readonly>
                                        <button class="btn btn-sm btn-primary btn-increase">+</button>
                                    </td>
                                    <td class="subtotal">${{ number_format($item['price'] * $item['quantity'], 2) }}</td>
                                    <td>
                                        <a href="{{ route('cart.remove', ['id' => $id]) }}"
                                            class="btn btn-sm btn-danger btn-remove">Remove</a>
                                    </td>
                                </tr>
                                @php $total += $item['price'] * $item['quantity']; @endphp
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-lg-4">
                <form class="mb-30" action="">
                    <div class="input-group">
                        <input type="text" class="form-control border-0 p-4" placeholder="Coupon Code">
                        <div class="input-group-append">
                            <button class="btn btn-primary">Apply Coupon</button>
                        </div>
                    </div>
                </form>
                <h5 class="section-title position-relative text-uppercase mb-3">
                    <span class="bg-secondary pr-3">Cart Summary</span>
                </h5>
                <div class="bg-light p-30 mb-5">
                    <div class="border-bottom pb-2">
                        <div class="d-flex justify-content-between mb-3">
                            <h6>Product Quantity</h6>
                            <h6 class="cart-total-quantity">{{ array_sum(array_column(session('cart', []), 'quantity')) }}</h6>
                        </div>
                    </div>
                    <div class="pt-2">
                        <div class="d-flex justify-content-between mt-2">
                            <h5>Total Product Price</h5>
                            <h5 class="cart-total-price">
                                ${{ number_format(array_sum(array_map(fn($item) => $item['price'] * $item['quantity'], session('cart', []))), 2) }}
                            </h5>
                        </div>
                        <a href="{{ route('checkout') }}"
                            class="btn btn-block btn-primary font-weight-bold my-3 py-3">
                            Proceed To Checkout
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Cart End -->

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Remove from cart handler
            $('.btn-remove').on('click', function(e) {
                e.preventDefault();
                const productId = $(this).closest('tr').data('id');
                window.location.href = '/cart/remove/' + productId;
            });

            $(".btn-increase, .btn-decrease").click(function() {
                const row = $(this).closest("tr");
                const productId = row.data("id");
                const quantityInput = row.find(".quantity-input");
                let currentQuantity = parseInt(quantityInput.val());

                if ($(this).hasClass("btn-increase")) {
                    currentQuantity++;
                } else if ($(this).hasClass("btn-decrease") && currentQuantity > 1) {
                    currentQuantity--;
                }

                quantityInput.val(currentQuantity);

                $.ajax({
                    url: '/cart/update/' + productId,
                    type: 'GET',
                    data: {
                        quantity: currentQuantity
                    },
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    success: function(response) {
                        if (response.success) {
                            row.find(".subtotal").text("$" + response.subtotal);
                            $(".cart-total-quantity").text(response.totalQuantity);
                            $(".cart-total-price").text("$" + response.totalPrice);
                        } else {
                            alert(response.message || 'Error updating cart');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                        alert('Error updating cart');
                    }
                });
            });
        });
    </script>
@endsection
