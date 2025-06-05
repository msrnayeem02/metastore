<!DOCTYPE html>
<html lang="en">

<head>
    @php
        $marketingIntegration = \App\Models\MarketingIntegration::first();
    @endphp

    <!-- Google Tag Manager -->
    @if (isset($marketingIntegration) && $marketingIntegration->google_tag_manager_id)
        <script>
            (function(w, d, s, l, i) {
                w[l] = w[l] || [];
                w[l].push({
                    'gtm.start': new Date().getTime(),
                    event: 'gtm.js'
                });
                var f = d.getElementsByTagName(s)[0],
                    j = d.createElement(s),
                    dl = l != 'dataLayer' ? '&l=' + l : '';
                j.async = true;
                j.src =
                    'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
                f.parentNode.insertBefore(j, f);
            })(window, document, 'script', 'dataLayer', '{{ $marketingIntegration->google_tag_manager_id }}');
        </script>
    @endif
    <!-- End Google Tag Manager -->

    <!-- Meta Pixel Code -->
    @if (isset($marketingIntegration) && $marketingIntegration->meta_pixel_id)
        <script>
            ! function(f, b, e, v, n, t, s) {
                if (f.fbq) return;
                n = f.fbq = function() {
                    n.callMethod ?
                        n.callMethod.apply(n, arguments) : n.queue.push(arguments)
                };
                if (!f._fbq) f._fbq = n;
                n.push = n;
                n.loaded = !0;
                n.version = '2.0';
                n.queue = [];
                t = b.createElement(e);
                t.async = !0;
                t.src = v;
                s = b.getElementsByTagName(e)[0];
                s.parentNode.insertBefore(t, s)
            }(window, document, 'script',
                'https://connect.facebook.net/en_US/fbevents.js');
            fbq('init', '{{ $marketingIntegration->meta_pixel_id }}');
            fbq('track', 'PageView');
        </script>
    @endif
    <!-- End Meta Pixel Code -->
    <meta charset="utf-8">
    @php
        $siteTitle = \App\Models\Tenant::where('custom_domain', request()->getHost())->value('shop_name');
    @endphp

    <title>{{ ucwords($siteTitle) ?? 'Best Ecommerce' }}</title>

    <title>MultiShop - Ecommerce</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="Free HTML Templates" name="keywords">
    <meta content="Free HTML Templates" name="description">

    <!-- Favicon -->
    <link href="img/favicon.ico" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="{{ asset('/') }}frontend-assets/lib/animate/animate.min.css" rel="stylesheet">
    <link href="{{ asset('/') }}frontend-assets/lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="{{ asset('/') }}frontend-assets/css/style.css" rel="stylesheet">
</head>

<body>
    <!-- Google Tag Manager (noscript) -->
    @if (isset($marketingIntegration) && $marketingIntegration->google_tag_manager_id)
        <noscript>
            <iframe src="https://www.googletagmanager.com/ns.html?id={{ $marketingIntegration->google_tag_manager_id }}"
                height="0" width="0" style="display:none;visibility:hidden"></iframe>
        </noscript>
    @endif
    <!-- End Google Tag Manager (noscript) -->

    <!-- Meta Pixel (noscript) -->
    @if (isset($marketingIntegration) && $marketingIntegration->meta_pixel_id)
        <noscript>
            <img height="1" width="1" style="display:none"
                src="https://www.facebook.com/tr?id={{ $marketingIntegration->meta_pixel_id }}&ev=PageView&noscript=1" />
        </noscript>
    @endif
    <!-- End Meta Pixel (noscript) -->

    @include('frontend.includes.header')


    @yield('content')


    <!-- Footer Start -->
    @include('frontend.includes.footer')
    <!-- Footer End -->

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.3.0/jquery.form.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqBootstrapValidation/1.3.7/jqBootstrapValidation.min.js"></script>
    <script src="/frontend-assets/lib/easing/easing.min.js"></script>
    <script src="/frontend-assets/lib/owlcarousel/owl.carousel.min.js"></script>

    <!-- Template Javascript -->
    <script src="/frontend-assets/js/main.js"></script>
    <script src="/frontend-assets/mail/contact.js"></script>

    @stack('scripts')
</body>

</html>
