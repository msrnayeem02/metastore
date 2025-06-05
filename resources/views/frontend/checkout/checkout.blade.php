@extends('frontend.master')

@section('content')
    <!-- Breadcrumb Start -->
    <div class="container-fluid">
        <div class="row px-xl-5">
            <div class="col-12">
                <nav class="breadcrumb bg-light mb-30">
                    <a class="breadcrumb-item text-dark" href="{{ route('home') }}">Home</a>
                    <a class="breadcrumb-item text-dark" href="{{ route('shop') }}">Shop</a>
                    <a class="breadcrumb-item text-dark" href="{{ route('checkout') }}">Checkout</a>
                </nav>
            </div>
        </div>
    </div>
    <!-- Breadcrumb End -->

    <!-- Checkout Start -->
    <div class="container-fluid">
        @session('error')
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endsession
        <form action="{{ route('shop.order.create') }}" method="POST">
            @csrf
            <div class="row px-xl-5">
                <div class="col-lg-8">
                    <h5 class="section-title text-uppercase mb-3">
                        <span class="bg-secondary pr-3">Billing Address</span>
                    </h5>
                    <div class="bg-light p-30 mb-5">
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>Full Name</label>
                                <input class="form-control" type="text" name="name" placeholder="Enter your name" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Phone Number</label>
                                <input class="form-control" type="text" name="phone" placeholder="Enter your phone number" required>
                            </div>
                            <div class="col-md-12 form-group">
                                <label>Delivery Zone</label>
                                <select name="delivery_zone" id="delivery_zone" class="form-control" required>
                                    <option value="">Select Delivery Zone</option>
                                    @foreach ($zone as $deliveryZone)
                                        <option value="{{ $deliveryZone->id }}" data-charge="{{ $deliveryZone->delivery_charge }}">
                                            {{ $deliveryZone->zone_name }} - ${{ number_format($deliveryZone->delivery_charge, 2) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('delivery_zone')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="col-md-12 form-group">
                                <label>Address</label>
                                <textarea name="address" class="form-control" rows="3" required></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <h5 class="section-title text-uppercase mb-3">
                        <span class="bg-secondary pr-3">Order Summary</span>
                    </h5>
                    <div class="bg-light p-30 mb-5">
                        <h6>Products</h6>
                        <div class="border-bottom mb-3">
                            @php $total = 0; @endphp
                            @foreach ($cart as $item)
                                <div class="d-flex justify-content-between">
                                    <p>{{ $item['title'] }} (x{{ $item['quantity'] }})</p>
                                    <p>${{ number_format($item['price'] * $item['quantity'], 2) }}</p>
                                </div>
                                @php $total += $item['price'] * $item['quantity']; @endphp
                            @endforeach
                        </div>

                        <div class="d-flex justify-content-between mb-3">
                            <h6>Subtotal</h6>
                            <h6>${{ number_format($total, 2) }}</h6>
                        </div>
                        <div class="d-flex justify-content-between">
                            <h6>Shipping</h6>
                            <h6 id="shipping-charge">$0.00</h6>
                        </div>
                        <div class="d-flex justify-content-between mt-3">
                            <h5>Total</h5>
                            <h5 id="total-amount">${{ number_format($total, 2) }}</h5>
                        </div>

                        <input type="hidden" name="cart" value="{{ json_encode($cart) }}">
                        <input type="hidden" name="subtotal" value="{{ $total }}">
                        <input type="hidden" name="shipping_charge" value="0">
                        <input type="hidden" name="total" value="{{ $total }}">

                        <div class="bg-light p-30 mb-5">
                            <h5 class="section-title text-uppercase mb-3">
                                <span class="bg-secondary pr-3">Payment Method</span>
                            </h5>
                            <div class="form-group">
                                <div class="payment-methods">
                                    <div class="mb-3">
                                        <div class="form-check custom-option custom-option-basic">
                                            <input class="form-check-input" type="radio" name="payment_method" id="cashOnDelivery" value="cod" checked>
                                            <label class="form-check-label d-flex align-items-center" for="cashOnDelivery">
                                                <i class="bx bx-money me-2 fs-3 text-success"></i>
                                                <span>
                                                    <span class="custom-option-header">Cash On Delivery</span>
                                                    <small>Pay when you receive the order</small>
                                                </span>
                                            </label>
                                        </div>
                                    </div>

                                    @foreach ($activeGateways as $gateway)
                                        @if ($gateway->gateway == 'bkash')
                                            <div class="mb-3">
                                                <div class="form-check custom-option custom-option-basic">
                                                    <input class="form-check-input" type="radio" name="payment_method" id="bkashPayment" value="bkash">
                                                    <label class="form-check-label d-flex align-items-center" for="bkashPayment">
                                                        <img src="https://static.cdnlogo.com/logos/b/47/bkash.svg" alt="bKash Logo" width="32" class="me-2">
                                                        <span>
                                                            <span class="custom-option-header">bKash Payment</span>
                                                            <small>Pay via bKash mobile banking</small>
                                                        </span>
                                                    </label>
                                                </div>
                                            </div>
                                        @endif

                                        @if ($gateway->gateway == 'sslcommerz')
                                            <div class="mb-3">
                                                <div class="form-check custom-option custom-option-basic">
                                                    <input class="form-check-input" type="radio" name="payment_method" id="sslPayment" value="sslcommerz">
                                                    <label class="form-check-label d-flex align-items-center" for="sslPayment">
                                                        <img src="https://developer.sslcommerz.com/assets/img/logo.png" alt="SSLCommerz" width="32" class="me-2">
                                                        <span>
                                                            <span class="custom-option-header">SSLCommerz</span>
                                                            <small>Credit/Debit Card, Mobile Banking, etc.</small>
                                                        </span>
                                                    </label>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>

                            <button type="submit" class="btn btn-block btn-primary font-weight-bold py-3">
                                Proceed to Payment
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <!-- Checkout End -->
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            const $deliveryZone = $('#delivery_zone');
            const $shippingCharge = $('#shipping-charge');
            const $totalAmount = $('#total-amount');
            const subtotal = {{ $total }};

            $deliveryZone.on('change', function() {
                const selectedOption = $(this).find('option:selected');
                const deliveryCharge = parseFloat(selectedOption.data('charge')) || 0;

                $shippingCharge.text('$' + deliveryCharge.toFixed(2));
                const total = subtotal + deliveryCharge;
                $totalAmount.text('$' + total.toFixed(2));

                $('input[name="shipping_charge"]').val(deliveryCharge);
                $('input[name="total"]').val(total);
            });
        });
    </script>
@endpush
