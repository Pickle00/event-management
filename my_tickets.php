<?php
session_start();
include 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get all orders for the logged-in user
$orders_sql = "SELECT o.*, e.title as event_title, e.start_date, e.location, e.image
               FROM orders o
               JOIN events e ON o.event_id = e.id
               WHERE o.user_id = $user_id AND o.status = 'completed'
               ORDER BY o.created_at DESC";
$orders_result = mysqli_query($conn, $orders_sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/nav_bar.css">
    <link rel="stylesheet" href="css/my_tickets.css">
    <title>My Tickets - Ticketly</title>
</head>
<body>
    <?php include 'includes/nav_bar.php'; ?>

    <div class="container">
        <div class="page-header">
            <h1 class="page-title">My Tickets</h1>
            <p class="page-subtitle">View and manage all your purchased event tickets</p>
        </div>

        <div class="tickets-grid">
            <?php if (mysqli_num_rows($orders_result) > 0): ?>
                <?php while ($order = mysqli_fetch_assoc($orders_result)): 
                    // Get order items
                    $items_sql = "SELECT oi.*, tt.ticket_name 
                                  FROM order_items oi 
                                  JOIN ticket_types tt ON oi.ticket_type_id = tt.id 
                                  WHERE oi.order_id = {$order['id']}";
                    $items_result = mysqli_query($conn, $items_sql);
                    
                    $total_tickets = 0;
                    while ($item = mysqli_fetch_assoc($items_result)) {
                        $total_tickets += $item['quantity'];
                    }
                    mysqli_data_seek($items_result, 0); // Reset pointer
                ?>
                    <div class="ticket-card">
                        <div class="ticket-image" style="
                            <?php if (!empty($order['image'])): ?>
                                background-image: url('admin/<?php echo htmlspecialchars($order['image']); ?>');
                                background-size: cover;
                                background-position: center;
                            <?php else: ?>
                                background: linear-gradient(135deg, <?php echo '#' . substr(md5($order['id']), 0, 6); ?> 0%, <?php echo '#' . substr(md5($order['id'] . 'salt'), 0, 6); ?> 100%);
                            <?php endif; ?>
                        "></div>

                        <div class="ticket-content">
                            <div class="ticket-header">
                                <h2 class="event-title"><?php echo htmlspecialchars($order['event_title']); ?></h2>
                                <div class="event-details">
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
                            </div>

                            <div class="ticket-info">
                                <div class="info-item">
                                    <span class="info-label">Order ID</span>
                                    <span class="info-value">#<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Tickets</span>
                                    <span class="info-value"><?php echo $total_tickets; ?> Ticket<?php echo $total_tickets > 1 ? 's' : ''; ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Total Paid</span>
                                    <span class="info-value">Rs <?php echo number_format($order['total'], 2); ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Payment</span>
                                    <span class="info-value"><?php echo ucfirst($order['payment_method']); ?></span>
                                </div>
                            </div>
                        </div>

                        <div class="ticket-actions">
                            <div class="qr-code">
                                QR Code
                                <br>
                                #<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?>
                            </div>
                            <a href="order_confirmation.php?order_id=<?php echo $order['id']; ?>" class="btn btn-primary">View Details</a>
                            <button onclick="window.print()" class="btn btn-secondary">Print Ticket</button>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="empty-state">
                    <div class="empty-icon">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#9CA3AF" stroke-width="2">
                            <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                            <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                        </svg>
                    </div>
                    <h2 class="empty-title">No Tickets Yet</h2>
                    <p class="empty-message">You haven't purchased any tickets yet. Browse events and get your tickets now!</p>
                    <a href="index.php" class="btn btn-primary" style="max-width: 200px; margin: 0 auto;">Browse Events</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
