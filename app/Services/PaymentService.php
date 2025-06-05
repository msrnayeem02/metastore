<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Library\SslCommerz\SslCommerzNotification;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    /**
     * Get bKash authentication token
     * 
     * @return string|null The authentication token or null on failure
     */
    public static function bkashGetToken()
    {
        // Get credentials from service
        $credentials = PaymentCredentialService::getCredentials('bkash');

        if (!is_array($credentials)) {
            Log::info('bKash credentials not found');
            return null;
        }

        // Determine the API URL based on sandbox setting
        $apiUrl = isset($credentials['sandbox']) && $credentials['sandbox'] == '1'
            ? 'https://tokenized.sandbox.bka.sh/v1.2.0-beta'
            : 'https://tokenized.pay.bka.sh/v1.2.0-beta';

        $response = Http::withHeaders([
            'username' => $credentials['username'],
            'password' => $credentials['password'],
            'content-type' => 'application/json',
        ])->post($apiUrl . '/tokenized/checkout/token/grant', [
            'app_key' => $credentials['app_key'],
            'app_secret' => $credentials['app_secret'],
        ]);
        $data = $response->json();
        return $data['id_token'] ?? null;
    }

    /**
     * Create a bKash payment
     * 
     * @param float $amount Payment amount
     * @param string $transactionId Unique transaction ID
     * @param string $token Authentication token from bkashGetToken()
     * @param string|null $successUrl Success callback URL
     * @param string|null $cancelUrl Cancel callback URL
     * @param array $additionalFields Additional payment data fields
     * @return array Response from bKash API
     */
    public static function bkashCreatePayment($amount, $transactionId, $token, $successUrl = null, $cancelUrl = null, array $additionalFields = [])
    {

        // Get credentials for API key
        $credentials = PaymentCredentialService::getCredentials('bkash');

        if (!is_array($credentials)) {
            Log::info('bKash credentials not found');
            return ['error' => 'Payment gateway configuration error'];
        }

        // Determine the API URL based on sandbox setting
        $apiUrl = isset($credentials['sandbox']) && $credentials['sandbox'] == '1'
            ? 'https://tokenized.sandbox.bka.sh/v1.2.0-beta'
            : 'https://tokenized.pay.bka.sh/v1.2.0-beta';

        // Default payment data
        $paymentData = [
            'mode' => '0011',
            'payerReference' => $transactionId,
            'callbackURL' => $successUrl,
            'successCallbackURL' => $successUrl,
            'failureCallbackURL' => $cancelUrl,
            'cancelledCallbackURL' => $cancelUrl,
            'amount' => (string)$amount,
            'currency' => 'BDT',
            'intent' => 'sale',
            'merchantInvoiceNumber' => $transactionId,
        ];

        // Merge any additional fields from the controller
        $paymentData = array_merge($paymentData, $additionalFields);

        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Authorization' => $token,
            'X-APP-Key' => $credentials['app_key'],
            'Content-Type' => 'application/json',
        ])->post($apiUrl . '/tokenized/checkout/create', $paymentData);

        return $response->json();
    }

    /**
     * Execute a bKash payment after user confirmation
     * 
     * @param string $paymentID Payment ID from bKash
     * @param string $token Authentication token
     * @return array Response from bKash API
     */
    public static function bkashExecutePayment($paymentID, $token)
    {
        // Get credentials for API key
        $credentials = PaymentCredentialService::getCredentials('bkash');

        if (!is_array($credentials)) {
            Log::info('bKash credentials not found');
            return ['error' => 'Payment gateway configuration error'];
        }

        // Determine the API URL based on sandbox setting
        $apiUrl = isset($credentials['sandbox']) && $credentials['sandbox'] == '1'
            ? 'https://tokenized.sandbox.bka.sh/v1.2.0-beta'
            : 'https://tokenized.pay.bka.sh/v1.2.0-beta';

        $response = Http::withHeaders([
            'authorization' => $token,
            'x-app-key' => $credentials['app_key'],
            'content-type' => 'application/json',
        ])->post($apiUrl . '/tokenized/checkout/execute', [
            'paymentID' => $paymentID,
        ]);

        return $response->json();
    }

    /**
     * Create a SSLCommerz payment
     * 
     * @param float $amount Payment amount
     * @param string $transactionId Unique transaction ID
     * @param object $user User making the payment
     * @param string|null $successUrl Success callback URL
     * @param string|null $cancelUrl Cancel callback URL
     * @param array $additionalFields Additional payment data fields
     * @return mixed Response from SSLCommerz API
     */
    public static function sslCommerzCreatePayment($amount, $transactionId, $user, $successUrl = null, $cancelUrl = null, array $additionalFields = [])
    {
        $credentials = PaymentCredentialService::getCredentials('sslcommerz');

        if (!is_array($credentials)) {
            Log::info('SSLCommerz credentials not found');
            return ['error' => 'Payment gateway configuration error'];
        }

        // Default payment data
        $postData = [
            'store_id' => $credentials['store_id'],
            'store_passwd' => $credentials['store_password'],
            'total_amount' => $amount,
            'currency' => 'BDT',
            'tran_id' => $transactionId,
            'cus_name' => $user->name ?? 'Customer',
            'cus_email' => $user->email ?? 'customer@example.com',
            'cus_add1' => 'N/A',
            'cus_phone' => $user->phone ?? '01700000000',
            'success_url' => config('sslcommerz.success_url'),
            'fail_url' => config('sslcommerz.cancel_url'),
            'cancel_url' => config('sslcommerz.cancel_url'),
            'product_profile' => 'non-physical-goods',
            'product_name' => 'Subscription',
            'product_category' => 'Service',
            'shipping_method' => 'NO',
        ];

        // Merge any additional fields from the controller
        $postData = array_merge($postData, $additionalFields);

        // Initialize SSLCommerz
        $sslc = new \App\Library\SslCommerz\SslCommerzNotification();

        // Set the correct API domain based on sandbox mode
        if (isset($credentials['sandbox']) && $credentials['sandbox']) {
            $sslc->sslc_domain = "https://sandbox.sslcommerz.com";
        } else {
            $sslc->sslc_domain = "https://securepay.sslcommerz.com";
        }

        // Use 'hosted' mode instead of 'checkout', 'json'
        $payment_options = $sslc->makePayment($postData, 'hosted');

        return $payment_options;
    }


    /**
     * Process payment response from callback
     * 
     * @param object $request HTTP request containing payment gateway response
     * @return array Standardized payment response with transaction ID and status
     */
    public static function processPaymentResponse($request)
    {
        $data = [
            'tran_id' => null,
            'status' => 'failed',
            'gateway' => null,
            'amount' => 0,
            'payment_data' => []
        ];

        if ($request->has('tran_id')) { // SSLCommerz response
            $data['tran_id'] = $request->input('tran_id');
            $data['status'] = $request->input('status') == 'VALID' ? 'success' : 'failed';
            $data['gateway'] = 'sslcommerz';
            $data['amount'] = $request->input('amount');
            $data['payment_data'] = $request->all();
        } elseif ($request->has('paymentID')) { // bKash response
            $paymentID = $request->input('paymentID');
            $status = $request->input('status');

            if ($status === 'success') {
                $token = self::bkashGetToken();
                if ($token) {
                    $execute = self::bkashExecutePayment($paymentID, $token);
                    if (isset($execute['transactionStatus']) && $execute['transactionStatus'] == 'Completed') {
                        $data['status'] = 'success';
                        $data['amount'] = $execute['amount'];
                    }
                    $data['payment_data'] = $execute;
                }
            }

            $data['tran_id'] = $request->input('merchantInvoiceNumber');
            $data['gateway'] = 'bkash';
        }

        return $data;
    }
}
