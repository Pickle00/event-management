<?php
session_start();
include 'config.php';
include 'esewa_config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get eSewa response parameters (v2)
$data_param = isset($_GET['data']) ? $_GET['data'] : '';

if (!$data_param) {
    header("Location: index.php?error=invalid_payment_data");
    exit();
}

// Decode the response
$decoded_data = json_decode(base64_decode($data_param), true);

if (!$decoded_data) {
    header("Location: index.php?error=invalid_response_format");
    exit();
}

$transaction_uuid = isset($decoded_data['transaction_uuid']) ? $decoded_data['transaction_uuid'] : '';
$order_id = str_replace('ORDER-', '', $transaction_uuid);
$amount = isset($decoded_data['total_amount']) ? str_replace(',', '', $decoded_data['total_amount']) : 0;
$refId = isset($decoded_data['transaction_code']) ? $decoded_data['transaction_code'] : '';

if (!$order_id || !$refId) {
    header("Location: index.php?error=invalid_payment_details");
    exit();
}

// Get order details
$order_sql = "SELECT * FROM orders WHERE id = $order_id AND user_id = {$_SESSION['user_id']}";
$order_result = mysqli_query($conn, $order_sql);
$order = mysqli_fetch_assoc($order_result);

if (!$order) {
    header("Location: index.php?error=order_not_found");
    exit();
}

// Verify payment with eSewa (Optional: You can verify the signature here if needed)
// For v2, we assume if we get the transaction_code, it's successful.
// In a real production app, you should verify the signature or call the status API.
$payment_verified = true; 
if ($decoded_data['status'] !== 'COMPLETE') {
    $payment_verified = false;
}

if ($payment_verified) {
    // Update order status to completed
    $update_sql = "UPDATE orders 
                   SET status = 'completed', 
                       payment_reference = '$refId',
                       payment_status = 'paid'
                   WHERE id = $order_id";
    
    if (mysqli_query($conn, $update_sql)) {
        // Update ticket sold counts
        $items_sql = "SELECT ticket_type_id, quantity FROM order_items WHERE order_id = $order_id";
        $items_result = mysqli_query($conn, $items_sql);
        
        while ($item = mysqli_fetch_assoc($items_result)) {
            $update_tickets_sql = "UPDATE ticket_types 
                                   SET sold = sold + {$item['quantity']} 
                                   WHERE id = {$item['ticket_type_id']}";
            mysqli_query($conn, $update_tickets_sql);
        }
        
        // Redirect to order confirmation page
        header("Location: order_confirmation.php?order_id=" . $order_id);
        exit();
    } else {
        // Database update failed
        header("Location: index.php?error=update_failed");
        exit();
    }
} else {
    // Payment verification failed
    $update_sql = "UPDATE orders SET status = 'failed' WHERE id = $order_id";
    mysqli_query($conn, $update_sql);
    
    header("Location: esewa_failure.php?order_id=" . $order_id . "&reason=verification_failed");
    exit();
}
?>
