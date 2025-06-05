<?php

return [
    'apiCredentials' => [
        'store_id' => 'hrsof6825c279695bc',
        'store_password' => 'hrsof6825c279695bc@ssl',
    ],
    'apiUrl' => [
        'make_payment' => '/gwprocess/v4/api.php',
        'transaction_status' => '/validator/api/merchantTransIDvalidationAPI.php',
        'order_validate' => '/validator/api/validationserverAPI.php',
        'refund_payment' => '/validator/api/merchantTransIDvalidationAPI.php',
        'refund_status' => '/validator/api/merchantTransIDvalidationAPI.php',
    ],
    'apiDomain' => 'https://sandbox.sslcommerz.com',
    'connect_from_localhost' => '',
    'success_url' => '/success',
    'failed_url' => '/fail',
    'cancel_url' => '/cancel',
    'ipn_url' => '/ipn',
    'testmode' => '',
];
