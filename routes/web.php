<?php

use App\Http\Controllers\PrivacyPolicyController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\ShopController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController as ShopOrderController;

// Shop routes with tenant database selection
Route::middleware(['tenant'])->group(function () {
    Route::get('/', [LandingPageController::class, 'index'])->name('home');
    Route::get('/shop', [ShopController::class, 'index'])->name('shop');

    // Product details page
    Route::get('/product/{id}', [ShopController::class, 'details'])->name('product.details');

    Route::get('/cart', [CartController::class, 'index'])->name('cart');
    Route::get('/cart/add/{id}', [CartController::class, 'addToCart'])->name('cart.add');
    Route::get('/cart/update/{id}', [CartController::class, 'updateCart'])->name('cart.update');
    Route::get('/cart/order-now/{id}', [CartController::class, 'orderNow'])->name('cart.orderNow');
    Route::get('/cart/remove/{id}', [CartController::class, 'removeFromCart'])->name('cart.remove');
    Route::get('/buy-now/{id}', [CartController::class, 'buyNow'])->name('cart.buyNow');

    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
    Route::post('/checkout/store', [CheckoutController::class, 'store'])->name('checkout.store');

    Route::get('/contact', [ContactController::class, 'index'])->name('contact');
    Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

    Route::get('/categories/{category}', [ShopController::class, 'getSubcategories'])->name('get-subcategories');

    // Order & Payment Routes
    Route::post('/order/create', [ShopOrderController::class, 'create'])->name('shop.order.create');
    Route::get('/order/success/{order}', [ShopOrderController::class, 'success'])->name('shop.order.success');

    Route::match(['get', 'post'], '/shop/payment/callback', [ShopOrderController::class, 'paymentCallback'])->name('shop.payment.callback');
    Route::match(['get', 'post'], '/shop/payment/cancel', [ShopOrderController::class, 'paymentCancel'])->name('shop.payment.cancel');

    Route::get('/privacy-policy', [PrivacyPolicyController::class, 'index'])->name('privacy-policy');
    Route::get('/terms_conditions', [PrivacyPolicyController::class, 'termsConditions'])->name('terms_conditions');
});