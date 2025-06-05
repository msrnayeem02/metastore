<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderPayment;
use App\Models\OrderItem;
use App\Models\DeliveryCharge;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'phone' => 'required|string',
            'address' => 'required|string',
            'delivery_zone' => 'required|exists:delivery_charges,id',
            'payment_method' => 'required',
            'subtotal' => 'required|numeric',
            'total' => 'required|numeric',
        ]);

        try {
            $cart = session()->get('buy_now_cart', session('cart', []));

            if (empty($cart)) {
                return back()->with('error', 'Cart is empty');
            }

            $zone = DeliveryCharge::findOrFail($request->delivery_zone);
            $tran_id = 'ORD-' . strtoupper(uniqid());
            $payment_method = $request->payment_method;

            // Create order
            $order = Order::create([
                'invoice' => 'INV-' . Str::random(10),
                'customer_name' => $request->name,
                'customer_contact' => $request->phone,
                'customer_address' => $request->address,
                'products' => json_encode($cart),
                'ordered_quantity' => array_sum(array_column($cart, 'quantity')),
                'order_status' => $payment_method === 'cod' ? 'processing' : 'pending',
                'payment_method' => $payment_method,
                'payment_status' => $payment_method === 'cod' ? 'pending' : 'unpaid',
                'total_price' => $request->total,
                'delivery_zone_id' => $zone->id,
                'zone_name' => $zone->zone_name,
                'delivery_charge' => $zone->delivery_charge,
                'subtotal' => $request->subtotal,
                'discount_amount' => 0,
            ]);

            // Create order items
            foreach ($cart as $id => $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $id,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'total_price' => $item['price'] * $item['quantity']
                ]);
            }

            // Create payment record
            $payment_data = [
                'order_invoice' => $order->invoice,
                'product_count' => count($cart),
                'payment_type' => $payment_method === 'cod' ? 'Cash On Delivery' : $payment_method
            ];

            OrderPayment::create([
                'order_id' => $order->id,
                'amount' => $order->total_price,
                'payment_method' => $payment_method,
                'payment_status' => 'pending',
                'transaction_id' => $tran_id,
                'currency' => 'BDT',
                'payment_data' => json_encode($payment_data)
            ]);

            if ($payment_method === 'cod') {

                session()->forget(['cart', 'buy_now_cart']);

                return redirect()->route('shop.order.success', [
                    'order' => $order->id
                ])->with('success', "Order placed successfully! Your invoice number is: {$order->invoice}");
            }


            // Define URLs for payment callbacks
            $successUrl = route('shop.payment.callback');

            $cancelUrl = route('shop.payment.cancel');

            config([
                'sslcommerz.success_url' => $successUrl,
                'sslcommerz.cancel_url' => $cancelUrl,
                'sslcommerz.failed_url' => $cancelUrl
            ]);

            // Process online payments
            if ($payment_method === 'bkash') {
                $token = PaymentService::bkashGetToken();
                if (!$token) {
                    return back()->with('error', 'Could not authenticate with bKash. Please try again.');
                }

                $additionalFields = [
                    'intent' => 'sale',
                    'orderID' => $order->id,
                    'payerReference' => $order->customer_contact
                ];

                $payment = PaymentService::bkashCreatePayment(
                    $order->total_price,
                    $tran_id,
                    $token,
                    $successUrl,
                    $cancelUrl,
                    $additionalFields
                );

                if (isset($payment['paymentID'], $payment['bkashURL'])) {
                    return redirect()->away($payment['bkashURL'])->with([
                        'invoice' => $order->invoice,
                        'order_id' => $order->id
                    ]);
                }

                \Log::error('bKash Payment Error', $payment);
                return back()->with('error', 'Could not initiate bKash payment: ' . ($payment['statusMessage'] ?? 'Unknown error'));
            }

            if ($payment_method === 'sslcommerz') {
                $user = (object)[
                    'name' => $order->customer_name,
                    'email' => 'customer@example.com',
                    'phone' => $order->customer_contact
                ];

                $additionalFields = [
                    'product_name' => 'Order #' . $order->invoice,
                    'product_category' => 'Order',
                    'value_a' => $order->id,
                    'value_b' => $tran_id,
                    'shipping_method' => 'NO',
                    'product_profile' => 'physical-goods',
                    'cus_add1' => $order->customer_address
                ];

                $payment = PaymentService::sslCommerzCreatePayment(
                    $order->total_price,
                    $tran_id,
                    $user,
                    $successUrl,
                    $cancelUrl,
                    $additionalFields
                );

                if (is_string($payment) || (is_array($payment) && isset($payment['GatewayPageURL']))) {
                    session(['temp_order_invoice' => $order->invoice]);
                    return redirect()->away(is_string($payment) ? $payment : $payment['GatewayPageURL']);
                }

                \Log::error('SSLCommerz Payment Error', ['response' => $payment]);
                return back()->with('error', 'Could not initiate SSLCommerz payment.');
            }

            return back()->with('error', 'Invalid payment method selected.');
        } catch (\Exception $e) {
            \Log::error('Order Creation Error:', ['error' => $e->getMessage()]);
            return back()->with('error', 'Failed to create order.');
        }
    }

    public function success(Request $request,  $order_id)
    {
        $order = Order::findOrFail($order_id);
        return view('frontend.order.success', compact('order'));
    }


    /**
     * Handle payment callback from payment gateways
     */
    public function paymentCallback(Request $request)
    {

        // Process the payment response
        $result = PaymentService::processPaymentResponse($request);

        // Find payment record based on gateway
        $payment = \App\Models\OrderPayment::where(function ($query) use ($result, $request) {
            $query->where('transaction_id', $result['tran_id']);
            if ($request->has('paymentID')) {
                $query->orWhere('payment_data->paymentID', $request->paymentID);
            }
        })->first();

        if (!$payment) {
            dd("no payment found");
        }

        $order = Order::find($payment->order_id);
        if (!$order) {
            return redirect()->route('orders.index')
                ->with('error', 'Order not found. Please contact support.');
        }

        if ($result['status'] === 'success') {
            $payment->update([
                'payment_status' => 'completed',
                'payment_date' => now(),
                'payment_data' => json_encode(array_merge(
                    json_decode($payment->payment_data, true) ?: [],
                    ['gateway_response' => $result['payment_data']]
                ))
            ]);

            $order->update(['payment_status' => 'completed']);

            return redirect()->route('orders.invoice', $order->id)
                ->with('success', 'Payment successful! Your order has been confirmed.');
        }

        // If payment failed
        $payment->update([
            'payment_status' => 'failed',
            'payment_data' => json_encode(array_merge(
                json_decode($payment->payment_data, true) ?: [],
                ['gateway_response' => $result['payment_data']]
            ))
        ]);
    }

    /**
     * Handle payment cancellation
     */
    public function paymentCancel(Request $request)
    {
        // Get transaction ID based on gateway
        $tran_id = $request->has('tran_id') ?
            $request->tran_id : // SSLCommerz
            $request->merchantInvoiceNumber; // bKash

        // If no transaction ID, try to get paymentID from bKash
        if (!$tran_id && $request->has('paymentID')) {
            $tran_id = $request->paymentID;
        }

        if (!$tran_id) {
            dd('Payment was cancelled. No transaction reference found.');
        }

        $payment = \App\Models\OrderPayment::where(function ($query) use ($tran_id, $request) {
            $query->where('transaction_id', $tran_id)
                ->orWhere('payment_data->paymentID', $request->paymentID ?? '');
        })->first();

        if ($payment) {
            // Update payment status and data
            $payment->update([
                'payment_status' => 'cancelled',
                'payment_data' => json_encode(array_merge(
                    json_decode($payment->payment_data, true) ?? [],
                    ['cancel_data' => $request->all()]
                ))
            ]);

            // Always redirect to checkout page with appropriate message
            $message = $request->has('failureMessage') ?
                'Payment failed: ' . $request->failureMessage :
                'Payment was cancelled';

            return $message;
        }

        // Fallback to index if payment record not found
        return "failed";
    }
}
