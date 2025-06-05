<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Checkout;
use App\Models\DeliveryCharge;
use App\Models\PaymentGateway;

class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        // Get delivery zones
        $zone = DeliveryCharge::where('status', true)->get();

        // Get active payment gateways
        $activeGateways = PaymentGateway::where('status', true)->get();

        // Get cart data
        if (session()->has('buy_now_cart')) {
            $cart = session('buy_now_cart', []);
        } else {
            $cart = session('cart', []);
        }

        if (empty($cart)) {
            return redirect()->route('shop')
                ->with('error', 'Your cart is empty');
        }

        return view('frontend.checkout.checkout', compact('cart', 'zone', 'activeGateways'));
    }


    public function store(Request $request)
    {

        // Validate the request
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'cart' => 'required|string', // Cart data must be passed as JSON
            'subtotal' => 'required|numeric',
            'total' => 'required|numeric',
        ]);

        try {
            // Decode cart data
            $cartData = json_decode($request->cart, true);

            if (!$cartData) {
                return back()->with('error', 'Invalid cart data format.');
            }


            return redirect()->route('home')
                ->with('success', 'Order placed successfully!');
        } catch (\Exception $e) {
            // Log error
            \Log::error('Checkout Save Error:', ['error' => $e->getMessage()]);
            return back()->with('error', 'Failed to save order.');
        }
    }
}
