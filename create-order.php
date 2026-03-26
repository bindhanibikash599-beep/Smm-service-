<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// AAPKI REAL KEYS (SCREENSHOT SE)
$client_id = "1218942c16bdfc239b187ffdca62498121";
$client_secret = "cfsk_ma_prod_405e161deddf4f6a58272500d63d1642_6c52ce54";

// Frontend se amount lena
$json = file_get_contents('php://input');
$data = json_decode($json, true);

if(!isset($data['amount']) || $data['amount'] < 200) {
    echo json_encode(["error" => "Minimum amount ₹200 required"]);
    exit;
}

$amount = $data['amount'];
$customer_id = $data['customer_id'] ?? 'user_' . time();

// Cashfree Production API URL
$url = "https://api.cashfree.com/pg/orders";

$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode([
        'order_amount' => (float)$amount,
        'order_currency' => "INR",
        'customer_details' => [
            'customer_id' => $customer_id,
            'customer_phone' => "9999999999", 
            'customer_email' => "customer@gmail.com"
        ],
        'order_meta' => [
            // Apni website ka URL yahan dalein
            'return_url' => "https://".$_SERVER['HTTP_HOST']."/index.html?order_id={order_id}"
        ]
    ]),
    CURLOPT_HTTPHEADER => [
        "x-client-id: $client_id",
        "x-client-secret: $client_secret",
        "x-api-version: 2023-08-01",
        "content-type: application/json"
    ],
]);

$response = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);

if ($err) {
    echo json_encode(["error" => "cURL Error: " . $err]);
} else {
    echo $response;
}
?>
