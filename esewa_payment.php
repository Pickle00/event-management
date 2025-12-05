<?php
session_start();
include 'config.php';
include 'esewa_config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get order ID from query parameter
$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

if (!$order_id) {
    header("Location: index.php");
    exit();
}

// Get order details
$order_sql = "SELECT o.*, e.title as event_title 
              FROM orders o 
              JOIN events e ON o.event_id = e.id 
              WHERE o.id = $order_id AND o.user_id = {$_SESSION['user_id']} AND o.status = 'pending'";
$order_result = mysqli_query($conn, $order_sql);
$order = mysqli_fetch_assoc($order_result);

if (!$order) {
    header("Location: index.php");
    exit();
}

// Generate eSewa payment data
$payment_data = generateEsewaPaymentData(
    $order['total'],
    'ORDER-' . $order_id . '-' . time(),
    $order['event_title']
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Processing Payment - eSewa</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        .payment-container {
            background: white;
            border-radius: 16px;
            padding: 60px 50px;
            text-align: center;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 500px;
        }

        .esewa-logo {
            width: 120px;
            height: 40px;
            background: #60bb46;
            border-radius: 8px;
            margin: 0 auto 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 20px;
        }

        .spinner {
            width: 60px;
            height: 60px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #60bb46;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 25px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        h1 {
            font-size: 24px;
            color: #1a1a1a;
            margin-bottom: 15px;
        }

        p {
            color: #6B7280;
            font-size: 16px;
            margin-bottom: 10px;
        }

        .amount {
            font-size: 32px;
            font-weight: 700;
            color: #60bb46;
            margin: 20px 0;
        }

        .info {
            background: #F9FAFB;
            padding: 20px;
            border-radius: 12px;
            margin-top: 30px;
            text-align: left;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 14px;
        }

        .info-label {
            color: #6B7280;
        }

        .info-value {
            font-weight: 600;
            color: #1a1a1a;
        }
    </style>
</head>
<body>
    <div class="payment-container">
        <div class="esewa-logo">eSewa</div>
        <div class="spinner"></div>
        <h1>Redirecting to eSewa</h1>
        <p>Please wait while we redirect you to eSewa payment gateway...</p>
        
        <div class="amount">NPR <?php echo number_format($order['total'], 2); ?></div>
        
        <div class="info">
            <div class="info-row">
                <span class="info-label">Event</span>
                <span class="info-value"><?php echo htmlspecialchars($order['event_title']); ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Order ID</span>
                <span class="info-value">#<?php echo str_pad($order_id, 6, '0', STR_PAD_LEFT); ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Payment Method</span>
                <span class="info-value">eSewa</span>
            </div>
        </div>

        <!-- eSewa Payment Form (Auto-submit) -->
        <form id="esewaForm" action="<?php echo ESEWA_PAYMENT_URL; ?>" method="POST">
            <input type="hidden" name="amount" value="<?php echo $payment_data['amount']; ?>">
            <input type="hidden" name="tax_amount" value="<?php echo $payment_data['tax_amount']; ?>">
            <input type="hidden" name="total_amount" value="<?php echo $payment_data['total_amount']; ?>">
            <input type="hidden" name="transaction_uuid" value="<?php echo $payment_data['transaction_uuid']; ?>">
            <input type="hidden" name="product_code" value="<?php echo $payment_data['product_code']; ?>">
            <input type="hidden" name="product_service_charge" value="<?php echo $payment_data['product_service_charge']; ?>">
            <input type="hidden" name="product_delivery_charge" value="<?php echo $payment_data['product_delivery_charge']; ?>">
            <input type="hidden" name="success_url" value="<?php echo $payment_data['success_url']; ?>">
            <input type="hidden" name="failure_url" value="<?php echo $payment_data['failure_url']; ?>">
            <input type="hidden" name="signed_field_names" value="<?php echo $payment_data['signed_field_names']; ?>">
            <input type="hidden" name="signature" value="<?php echo $payment_data['signature']; ?>">
        </form>

        <script>
            // Auto-submit form after 2 seconds
            setTimeout(function() {
                document.getElementById('esewaForm').submit();
            }, 2000);
        </script>
    </div>
</body>
</html>
