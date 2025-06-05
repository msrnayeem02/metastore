<!-- Header Start -->
<div class="container-fluid header-1 px-4">
    <div class="row align-items-center py-1">
        <div class="col-md-3 text-center text-md-left"></div>

        <div class="col-md-6 text-center py-1">
            <div class="moving-text-container text-light font-weight-bold">
                <span class="moving-text">{{ \App\Models\Setting::getValue('header_text') }}</span>
            </div>
        </div>

        <div class="col-md-3 text-center text-md-right py-1 d-flex align-items-center justify-content-md-end">
            <i class="fas fa-phone-alt text-light mr-2 phone-icon"></i>
            <h6 class="m-0 text-light customer-service-number">
                {{ \App\Models\Setting::getValue('customer_service_number') }}</h6>
        </div>
    </div>
</div>

<!-- Navbar Start -->
<nav class="navbar navbar-expand-lg navbar-dark header-2 sticky-top">
    <div class="container-fluid ">

        <a href="#">
            <img src="{{ asset(\App\Models\Setting::getValue('logo')) }}" alt="logo" width="60">
        </a>

        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarContent">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse justify-content-center" id="navbarContent">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a href="{{ route('home') }}" class="nav-link">Home</a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('shop') }}" class="nav-link">Shop</a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('cart') }}" class="nav-link">Cart</a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('checkout') }}" class="nav-link">Checkout</a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('contact') }}" class="nav-link">Contact</a>
                </li>
            </ul>
        </div>

        <div class="d-flex align-items-center">
            <div class="d-none d-lg-block mx-3">
                <form action="{{ route('shop') }}">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Search products...">
                        <div class="input-group-append">
                            <button class="btn btn-success all-search-btn" type="submit">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <a href="{{ route('cart') }}" class="icon-link mx-3 position-relative">
                <i class="fa fa-shopping-cart icon-style"></i>
                <span id="cart-badge" class="badge-custom">
                    {{ session('cart') ? array_sum(array_column(session('cart'), 'quantity')) : 0 }}
                </span>
            </a>
        </div>
    </div>
</nav>
<!-- Navbar End -->

{{-- 
<script>
    $(document).ready(function() {
        $('.add-to-cart-btn').click(function(e) {
            e.preventDefault();
            let productId = $(this).data('id');

            $.ajax({
                url: "{{ route('cart.add', ['id' => ':id']) }}".replace(':id', productId),
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                dataType: "json",
                success: function(response) {
                    if (response.cart) {
                        let totalQuantity = Object.values(response.cart).reduce((sum,
                            item) => sum + item.quantity, 0);
                        $('#cart-badge').text(totalQuantity).hide().fadeIn(300);
                    }
                    toastr.success(response.message);
                },
                error: function(err) {
                    console.log(err);
                    toastr.error('Error adding item to cart');
                }
            });
        });
    });
</script>
--}}
