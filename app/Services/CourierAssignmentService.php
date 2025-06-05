<?php

namespace App\Services;

use App\Models\Order;
use App\Services\CourierCredentialService;
use Illuminate\Support\Facades\Http;

class CourierAssignmentService
{
    /**
     * Assign the delivery partner to the order.
     * 
     * @param Order $orderInfo
     * @param string $slug
     * @return array
     */
    public function assignDeliveryPartner(Order $orderInfo, string $slug)
    {
        // Step 1: Get the courier credentials based on the slug
        $credentials = CourierCredentialService::decrypt($slug);

        if (is_string($credentials)) {
            // If credentials return an error string
            return ['error' => $credentials];
        }

        // Step 2: Define the common order data
        $orderData = [
            'invoice' => $orderInfo->invoice,
            'recipient_name' => $orderInfo->customer_name,
            'recipient_phone' => $orderInfo->customer_contact,
            'recipient_address' => $orderInfo->customer_address,
            'cod_amount' => $orderInfo->total_price,
            'note' => 'Handle with care'
        ];

        // Step 3: Process the courier based on the slug (e.g., Steadfast or RedX)
        switch ($slug) {
            case 'steadfast':
                return $this->assignSteadfast($orderInfo, $orderData, $credentials);
            case 'redx':
                return $this->assignRedX($orderInfo, $orderData, $credentials);
            case 'pathao':
                return $this->assignPathao($orderInfo, $orderData, $credentials);
            default:
                return ['error' => 'Invalid delivery partner slug.'];
        }
    }

    protected function assignPathao(Order $order, array $orderData, array $credentials)
    {
        $baseUrl = 'https://api-hermes.pathao.com';
        // Map zone names to Pathao city/zone IDs
        $cityZoneMap = [
            'Dhaka' => ['city' => 1, 'zone' => 1],
            'Chattogram' => ['city' => 2, 'zone' => 2],
            'Sylhet' => ['city' => 3, 'zone' => 3],
            // Add more mappings as needed
        ];

        // Default to Dhaka if zone not found
        $zoneInfo = $cityZoneMap[$order->zone_name] ?? ['city' => 1, 'zone' => 1];

        // Get access token
        $tokenResponse = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post($baseUrl . '/aladdin/api/v1/issue-token', [
            'client_id' => $credentials['client_id'],
            'client_secret' => $credentials['client_secret'],
            'username' => $credentials['mail'],
            'password' => $credentials['password'],
            'grant_type' => 'password',
        ]);

        if (!$tokenResponse->ok()) {
            return ['error' => 'Pathao Auth Failed: ' . $tokenResponse->body()];
        }

        $accessToken = $tokenResponse['access_token'];
        // Prepare parcel data
        $payload = [
            'store_id' => $credentials['store_id'],
            'merchant_order_id' => $order->invoice ?? ('INV-' . $order->id),
            'recipient_name' => $order->customer_name,
            'recipient_phone' => $order->customer_contact,
            'recipient_address' => strlen($order->customer_address) >= 10 ? $order->customer_address : 'N/A, Dhaka',
            'recipient_city' => $zoneInfo['city'],
            'recipient_zone' => $zoneInfo['zone'],
            'delivery_type' => 48, // 48 = Regular, 12 = Same day
            'item_type' => 2, // 1 = Parcel
            'item_weight' => 1,
            'item_quantity' => $order->ordered_quantity ?? 1,
            'amount_to_collect' => (int) $order->total_price,
            'item_description' => 'Ordered products',
            'special_instruction' => 'Handle with care',
        ];

        $response = Http::withToken($accessToken)
            ->acceptJson()
            ->post($baseUrl . '/aladdin/api/v1/orders', $payload);

        if (!$response->ok()) {
            return ['error' => 'Pathao order creation failed: ' . $response->body()];
        }

        if ($response->successful() && isset($response['data']['consignment_id'])) {
            $data = $response->json();

            // Update the order
            $order->update([
                'courier_partner' => 'pathao',
                'trackingid' => $data['data']['consignment_id'],
                'courier_response' => $data,
            ]);

            return ['success' => 'Order sent to Pathao successfully.'];
        }

        return ['error' => 'Pathao order creation failed: ' . $response->body()];
    }


    /**
     * Handle Steadfast API integration.
     */
    private function assignSteadfast(Order $orderInfo, array $orderData, array $credentials)
    {

        //dd($credentials);
        // Steadfast API request
        $response = Http::withHeaders([
            'Api-Key'      => $credentials['api_key'],
            'Secret-Key'   => $credentials['api_secret'],
            'Content-Type' => 'application/json',
        ])->post('https://portal.steadfast.com.bd/api/v1/create_order', $orderData);

        $responseData = $response->json();

        if ($response->successful() && isset($responseData['consignment'])) {
            $consignment = $responseData['consignment'];

            // Update order info with the response data
            $orderInfo->update([
                'courier_partner'   => 'steadfast',
                'order_status'      => 'shipped',
                'trackingid'        => $consignment['tracking_code'],
                'courier_response'  => json_encode($responseData),
            ]);

            return ['success' => 'Steadfast order assigned successfully!'];
        } else {
            $orderInfo->update([
                'courier_partner'   => 'steadfast',
                'order_status'      => 'failed',
                'trackingid'        => null,
                'courier_response'  => json_encode($responseData),
            ]);
        }

        return ['error' => 'Steadfast API failed.', 'response' => $responseData];
    }



    /**
     * Handle RedX API integration.
     */
    private function assignRedX(Order $orderInfo, array $orderData, array $credentials)
    {
        // Construct parcel details
        $products = json_decode($orderInfo->products, true);
        $parcelDetails = [];

        foreach ($products as $product) {
            $parcelDetails[] = [
                'name'     => 'productName',  // You can replace this with dynamic data
                'category' => 'General',
                'value'    => (float)$product['price'],
            ];
        }

        // RedX API request
        $payload = [
            "customer_name"          => $orderInfo->customer_name,
            "customer_phone"         => $orderInfo->customer_contact,
            "delivery_area"          => $orderInfo->zone_name ?? "Dhaka",
            "delivery_area_id"       => $orderInfo->delivery_zone_id ?? 12,
            "customer_address"       => $orderInfo->customer_address,
            "merchant_invoice_id"    => $orderInfo->invoice,
            "cash_collection_amount" => $orderInfo->total_price,
            "parcel_weight"          => 500,
            "instruction"            => "Handle with care",
            "value"                  => 100,
            "is_closed_box"          => false,
            "pickup_store_id"        => 1,
            "parcel_details_json"    => $parcelDetails,
        ];

        $response = Http::withHeaders([
            'API-ACCESS-TOKEN' => 'Bearer ' . $credentials['jwt_token'],
            'Content-Type' => 'application/json',
        ])->post('https://sandbox.redx.com.bd/v1.0.0-beta/parcel', $payload);

        $responseData = $response->json();

        if (isset($responseData['tracking_id'])) {
            $orderInfo->update([
                'courier_partner'   => 'redx',
                'courier_response'  => json_encode($responseData),
                'trackingid'        => $responseData['tracking_id'],
                'order_status'      => 'shipped',
            ]);

            return ['success' => 'RedX order assigned successfully!'];
        } else {
            $orderInfo->update([
                'courier_partner'   => 'redx',
                'courier_response'  => json_encode($responseData),
                'trackingid'        => null,
                'order_status'      => 'failed',
            ]);
        }

        return ['error' => 'RedX API failed.', 'response' => $responseData];
    }
}
