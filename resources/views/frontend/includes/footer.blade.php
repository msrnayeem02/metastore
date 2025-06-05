<!-- First Footer Start -->
<div class="container-fluid header-2 text-secondary pt-4">
    <div class="row mx-xl-5 py-4 footer-container">

        <!-- Column 1: Company Logo and Details -->
        <div class="col-md-3 mb-4 text-center text-md-left"></div>

        <div class="col-md-3 mb-4 text-center text-md-left">
            <a href="#">
                <img src="{{ asset(\App\Models\Setting::getValue('logo')) }}" alt="logo" width="60">
            </a>
            <p class="mt-3 text-secondary">
                {!! \App\Models\Setting::getValue('company_imformation') !!}
            </p>
        </div>

        <!-- Column 2: Information Links -->
        <div class="col-md-2 mb-4 text-center text-md-left">
            <h5 class="text-light mb-3">Information</h5>
            <ul class="list-unstyled">
                <li>
                    <a href="{{ route('privacy-policy') }}"
                        class="text-secondary">
                        Privacy Policy
                    </a>
                </li>
                <li>
                    <a href="{{ route('terms_conditions') }}"
                        class="text-secondary">
                        Terms & Conditions
                    </a>
                </li>
            </ul>
        </div>

        <!-- Column 3: Social Icons -->
        <div class="col-md-2 mb-4 text-center text-md-left">
            <h5 class="text-light mb-3">Follow Us</h5>
            <div>
                <a href="{{ \App\Models\Setting::getValue('customer_facebook') }}" class="text-secondary mx-2"
                    target="_blank">
                    <i class="fab fa-facebook-f"></i>
                </a>
                <a href="{{ \App\Models\Setting::getValue('customer_youtube') }}" class="text-secondary mx-2"><i
                        class="fab fa-twitter"></i></a>
                <a href="{{ \App\Models\Setting::getValue('customer_instagram') }}" class="text-secondary mx-2"><i
                        class="fab fa-instagram"></i></a>
                <a href="{{ \App\Models\Setting::getValue('customer_whatsapp') }}" class="text-secondary mx-2"><i
                        class="fab fa-linkedin"></i></a>
            </div>
        </div>
        <div class="col-md-2"></div>

    </div>
</div>
<!-- First Footer End -->

<!-- Second Footer Start -->
<div class="container-fluid header-1 text-secondary">
    <div class="row border-top mx-xl-5 py-4" style="border-color: rgba(256, 256, 256, .1) !important;">
        <div class="col-md-12 px-xl-0 text-center text-md-center">
            <p class="mb-md-0 text-secondary">
                Copyright © {{ date('Y') }} Metasoft BD. All Rights Reserved.
            </p>
        </div>
    </div>
</div>
<!-- Second Footer End -->

<!-- Back to Top -->
<a href="#" class="btn btn-primary back-to-top"><i class="fa fa-angle-double-up"></i></a>

<!-- JavaScript Libraries -->
<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('frontend-assets/lib/easing/easing.min.js') }}"></script>
<script src="{{ asset('frontend-assets/lib/owlcarousel/owl.carousel.min.js') }}"></script>

<!-- Contact Javascript File -->
<script src="{{ asset('frontend-assets/mail/jqBootstrapValidation.min.js') }}"></script>
<script src="{{ asset('frontend-assets/mail/contact.js') }}"></script>

<!-- Template Javascript -->
<script src="{{ asset('frontend-assets/js/main.js') }}"></script>
