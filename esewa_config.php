<?php
// eSewa Payment Gateway Configuration (v2)

// eSewa Merchant Details (Sandbox/Test Environment)
define('ESEWA_MERCHANT_CODE', 'EPAYTEST'); 
define('ESEWA_SECRET_KEY', '8gBm/:&EnhH.1/q'); 

// eSewa URLs
define('ESEWA_PAYMENT_URL', 'https://rc-epay.esewa.com.np/api/epay/main/v2/form'); // v2 Sandbox URL
// For production, use: https://epay.esewa.com.np/api/epay/main/v2/form

// Callback URLs (Update these to match your domain)
$base_url = 'http://localhost/events_management';
define('ESEWA_SUCCESS_URL', $base_url . '/esewa_success.php');
define('ESEWA_FAILURE_URL', $base_url . '/esewa_failure.php');

/**
 * Generate eSewa v2 Signature
 * 
 * @param string $message Message to sign (total_amount,transaction_uuid,product_code)
 * @return string Base64 encoded signature
 */
function generateEsewaSignature($message) {
    $s = hash_hmac('sha256', $message, ESEWA_SECRET_KEY, true);
    return base64_encode($s);
}

/**
 * Generate eSewa payment form data
 * 
 * @param float $amount Total amount
 * @param string $order_id Unique order ID (transaction_uuid)
 * @param string $product_name Product/Event name
 * @return array Payment form data
 */
function generateEsewaPaymentData($amount, $order_id, $product_name) {
    $total_amount = number_format($amount, 2, '.', '');
    $transaction_uuid = $order_id;
    $product_code = ESEWA_MERCHANT_CODE;
    
    // Message to sign: total_amount,transaction_uuid,product_code
    $message = "total_amount=$total_amount,transaction_uuid=$transaction_uuid,product_code=$product_code";
    $signature = generateEsewaSignature($message);
    
    return [
        'amount' => $total_amount, // For simplicity in this demo, assuming no tax/service charge separation in input
        'tax_amount' => '0',
        'total_amount' => $total_amount,
        'transaction_uuid' => $transaction_uuid,
        'product_code' => $product_code,
        'product_service_charge' => '0',
        'product_delivery_charge' => '0',
        'success_url' => ESEWA_SUCCESS_URL,
        'failure_url' => ESEWA_FAILURE_URL,
        'signed_field_names' => 'total_amount,transaction_uuid,product_code',
        'signature' => $signature
    ];
}

/**
 * Verify eSewa payment (v2)
 * 
 * Note: v2 verification is different. It usually involves checking the signature in the response or calling a status API.
 * For this implementation, we will assume the success page receives the encoded response which needs decoding and verification.
 * However, the user request focused on the form submission. We will leave this placeholder or update if needed.
 */
function verifyEsewaPayment($data) {
    // Implement v2 verification logic here if needed
    // Usually involves decoding the 'data' parameter from the success URL
    return true; 
}
?>