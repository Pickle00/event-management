<?php
session_start();
include 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$order_id = isset($_GET['order_id']) ? $_GET['order_id'] : 0;

// Get order details
$order_sql = "SELECT o.*, e.title as event_title, e.start_date, e.location, u.name as user_name, u.email as user_email 
              FROM orders o 
              JOIN events e ON o.event_id = e.id 
              JOIN users u ON o.user_id = u.id 
              WHERE o.id = $order_id AND o.user_id = {$_SESSION['user_id']}";
$order_result = mysqli_query($conn, $order_sql);
$order = mysqli_fetch_assoc($order_result);

if (!$order) {
    header("Location: index.php");
    exit();
}

// Get order items
$items_sql = "SELECT oi.*, tt.ticket_name 
              FROM order_items oi 
              JOIN ticket_types tt ON oi.ticket_type_id = tt.id 
              WHERE oi.order_id = $order_id";
$items_result = mysqli_query($conn, $items_sql);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
    <link rel="stylesheet" href="css/order_confirmation.css">
</head>

<body>
    <nav class="navbar">
        <a href="index.php" class="logo">
            <div class="logo-icon"></div>
            Ticketly
        </a>
    </nav>

    <div class="container">
        <div class="success-card">
            <div class="success-icon">
                <svg class="checkmark" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="3">
                    <polyline points="20 6 9 17 4 12" />
                </svg>
            </div>

            <h1 class="success-title">Payment Successful!</h1>
            <p class="success-message">Your tickets have been booked. A confirmation email has been sent to
                <?php echo htmlspecialchars($order['user_email']); ?></p>

            <div class="order-details">
                <div class="order-header">
                    <div>
                        <div class="order-label">Order ID</div>
                        <div class="order-value">#<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?></div>
                    </div>
                    <div>
                        <div class="order-label">Order Date</div>
                        <div class="order-value"><?php echo date('M j, Y', strtotime($order['created_at'])); ?></div>
                    </div>
                    <div>
                        <div class="order-label">Payment Method</div>
                        <div class="order-value"><?php echo ucfirst($order['payment_method']); ?></div>
                    </div>
                </div>

                <div class="event-info">
                    <div class="event-title"><?php echo htmlspecialchars($order['event_title']); ?></div>
                    <div class="event-detail">
                        <svg class="event-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                            <line x1="16" y1="2" x2="16" y2="6" />
                            <line x1="8" y1="2" x2="8" y2="6" />
                            <line x1="3" y1="10" x2="21" y2="10" />
                        </svg>
                        <?php echo date('l, F j, Y • g:i A', strtotime($order['start_date'])); ?>
                    </div>
                    <div class="event-detail">
                        <svg class="event-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
                            <circle cx="12" cy="10" r="3" />
                        </svg>
                        <?php echo htmlspecialchars($order['location']); ?>
                    </div>
                </div>

                <div class="tickets-section">
                    <div class="section-title">Tickets</div>
                    <?php while ($item = mysqli_fetch_assoc($items_result)): ?>
                        <div class="ticket-row">
                            <span><?php echo htmlspecialchars($item['ticket_name']); ?> ×
                                <?php echo $item['quantity']; ?></span>
                            <span>Rs <?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                        </div>
                    <?php endwhile; ?>
                </div>

                <div class="price-summary">
                    <div class="price-row">
                        <span style="color: #6B7280;">Subtotal</span>
                        <span style="font-weight: 600;">Rs <?php echo number_format($order['subtotal'], 2); ?></span>
                    </div>
                    <div class="price-row">
                        <span style="color: #6B7280;">Service Fee</span>
                        <span style="font-weight: 600;">Rs <?php echo number_format($order['service_fee'], 2); ?></span>
                    </div>
                    <div class="price-total">
                        <span>Total Paid</span>
                        <span>Rs <?php echo number_format($order['total'], 2); ?></span>
                    </div>
                </div>
            </div>

            <div class="action-buttons">
                <a href="index.php" class="btn btn-secondary">Back to Home</a>
                <button onclick="window.print()" class="btn btn-primary">Download Ticket</button>
            </div>
        </div>
    </div>
</body>

</html>