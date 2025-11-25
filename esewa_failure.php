<?php
session_start();
include 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
$reason = isset($_GET['reason']) ? $_GET['reason'] : 'unknown';

// Update order status to failed if order_id is provided
if ($order_id) {
    $update_sql = "UPDATE orders SET status = 'failed' WHERE id = $order_id";
    mysqli_query($conn, $update_sql);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Failed</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: #f5f5f5;
        }

        .navbar {
            background: white;
            padding: 15px 40px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .logo {
            display: flex;
            align-items: center;
            font-size: 20px;
            font-weight: 700;
            color: #1a1a1a;
            text-decoration: none;
        }

        .logo-icon {
            width: 32px;
            height: 32px;
            background: #4F46E5;
            border-radius: 6px;
            margin-right: 10px;
        }

        .container {
            max-width: 600px;
            margin: 80px auto;
            padding: 0 40px;
        }

        .failure-card {
            background: white;
            border-radius: 16px;
            padding: 60px 50px;
            text-align: center;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        .failure-icon {
            width: 80px;
            height: 80px;
            background: #FEE2E2;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
        }

        .x-mark {
            color: #EF4444;
        }

        .failure-title {
            font-size: 32px;
            font-weight: 800;
            color: #1a1a1a;
            margin-bottom: 15px;
        }

        .failure-message {
            font-size: 16px;
            color: #6B7280;
            margin-bottom: 40px;
            line-height: 1.6;
        }

        .error-details {
            background: #FEF2F2;
            border-left: 4px solid #EF4444;
            padding: 20px;
            border-radius: 8px;
            text-align: left;
            margin-bottom: 30px;
        }

        .error-label {
            font-size: 14px;
            font-weight: 600;
            color: #991B1B;
            margin-bottom: 5px;
        }

        .error-value {
            font-size: 14px;
            color: #6B7280;
        }

        .action-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
        }

        .btn {
            padding: 14px 30px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 15px;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s;
            display: inline-block;
        }

        .btn-primary {
            background: #4F46E5;
            color: white;
            border: none;
        }

        .btn-primary:hover {
            background: #4338CA;
        }

        .btn-secondary {
            background: white;
            color: #374151;
            border: 1px solid #D1D5DB;
        }

        .btn-secondary:hover {
            background: #F9FAFB;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <a href="index.php" class="logo">
            <div class="logo-icon"></div>
            Ticketly
        </a>
    </nav>

    <div class="container">
        <div class="failure-card">
            <div class="failure-icon">
                <svg class="x-mark" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </div>

            <h1 class="failure-title">Payment Failed</h1>
            <p class="failure-message">
                Unfortunately, your payment could not be processed. This could be due to insufficient funds, 
                cancellation, or a technical issue with the payment gateway.
            </p>

            <?php if ($order_id): ?>
            <div class="error-details">
                <div class="error-label">Order ID</div>
                <div class="error-value">#<?php echo str_pad($order_id, 6, '0', STR_PAD_LEFT); ?></div>
                
                <div class="error-label" style="margin-top: 15px;">Reason</div>
                <div class="error-value">
                    <?php 
                    switch($reason) {
                        case 'verification_failed':
                            echo 'Payment verification failed';
                            break;
                        case 'cancelled':
                            echo 'Payment was cancelled';
                            break;
                        default:
                            echo 'Unknown error occurred';
                    }
                    ?>
                </div>
            </div>
            <?php endif; ?>

            <div class="action-buttons">
                <a href="index.php" class="btn btn-secondary">Back to Home</a>
                <?php if ($order_id): ?>
                <a href="checkout.php?retry=<?php echo $order_id; ?>" class="btn btn-primary">Try Again</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
