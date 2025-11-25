<?php
session_start();
include 'config.php';

$event_id = isset($_GET['id']) ? $_GET['id'] : 1;

$event_sql = "SELECT * FROM events WHERE id = $event_id";
$event_result = mysqli_query($conn, $event_sql);
$event = mysqli_fetch_assoc($event_result);

$tickets_sql = "SELECT * FROM ticket_types WHERE event_id = $event_id";
$tickets_result = mysqli_query($conn, $tickets_sql);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="css/nav_bar.css">
    <title><?php echo $event['title']; ?> - Ticketly</title>
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

        

        .breadcrumb {
            padding: 20px 40px;
            display: flex;
            gap: 8px;
            font-size: 14px;
            color: #6B7280;
        }

        .breadcrumb a {
            color: #6B7280;
            text-decoration: none;
        }

        .breadcrumb a:hover {
            color: #4F46E5;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 40px 60px;
            display: grid;
            grid-template-columns: 1fr 380px;
            gap: 40px;
        }

        .event-media {
            width: 100%;
            height: 400px;
            border-radius: 16px;
            overflow: hidden;
            position: relative;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .play-btn {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s;
        }

        .play-btn:hover {
            transform: scale(1.1);
        }

        .event-header {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
            align-items: start;
        }

        .event-title {
            font-size: 42px;
            font-weight: 800;
            color: #1a1a1a;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
        }

        .status-badge {
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-upcoming {
            background: #D1FAE5;
            color: #065F46;
        }

        .status-ongoing {
            background: #FEF3C7;
            color: #92400E;
        }

        .status-completed {
            background: #E5E7EB;
            color: #374151;
        }

        .status-cancelled {
            background: #FEE2E2;
            color: #991B1B;
        }

        .event-datetime {
            font-size: 16px;
            color: #6B7280;
            margin-bottom: 8px;
        }

        .event-location-link {
            color: #4F46E5;
            text-decoration: none;
            font-weight: 500;
        }

        .event-meta {
            color: #9CA3AF;
            font-size: 14px;
            margin-top: 12px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
        }

        .btn-calendar {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border: 1px solid #D1D5DB;
            border-radius: 8px;
            background: white;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.2s;
        }

        .btn-calendar:hover {
            border-color: #4F46E5;
        }

        .btn-share {
            padding: 10px;
            border: 1px solid #D1D5DB;
            border-radius: 8px;
            background: white;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-share:hover {
            border-color: #4F46E5;
        }

        .tabs {
            display: flex;
            gap: 30px;
            border-bottom: 2px solid #E5E7EB;
            margin-top: 30px;
            margin-bottom: 30px;
        }

        .tab {
            padding: 12px 0;
            color: #6B7280;
            cursor: pointer;
            border-bottom: 3px solid transparent;
            margin-bottom: -2px;
            font-weight: 500;
            transition: all 0.2s;
        }

        .tab.active {
            color: #4F46E5;
            border-bottom-color: #4F46E5;
        }

        .section {
            margin-bottom: 40px;
        }

        .section-title {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 15px;
            color: #1a1a1a;
        }

        .section-content {
            font-size: 16px;
            line-height: 1.8;
            color: #4B5563;
        }

        .agenda-list {
            list-style: none;
            padding-left: 0;
        }

        .agenda-item {
            padding: 12px 0;
            font-size: 16px;
            color: #374151;
        }

        .tags {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .tag {
            padding: 6px 16px;
            background: #EEF2FF;
            color: #4F46E5;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
        }

        .location-section {
            margin-top: 40px;
        }

        .map {
            width: 100%;
            height: 350px;
            background: #E5E7EB;
            border-radius: 12px;
            margin-top: 15px;
            overflow: hidden;
        }

        .map iframe {
            width: 100%;
            height: 100%;
            border: 0;
            border-radius: 12px;
        }

        .no-map {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
            color: #9CA3AF;
            font-size: 14px;
        }

        .ticket-sidebar {
            position: sticky;
            top: 90px;
            height: fit-content;
        }

        .ticket-card {
            background: white;
            border-radius: 16px;
            padding: 30px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .ticket-card-title {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 25px;
            color: #1a1a1a;
        }

        .ticket-item {
            padding: 20px 0;
            border-bottom: 1px solid #E5E7EB;
        }

        .ticket-item:last-child {
            border-bottom: none;
        }

        .ticket-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 12px;
        }

        .ticket-name {
            font-weight: 600;
            font-size: 16px;
            color: #1a1a1a;
        }

        .ticket-price {
            font-weight: 700;
            font-size: 16px;
            color: #1a1a1a;
        }

        .ticket-status {
            font-size: 13px;
            color: #EF4444;
            margin-top: 2px;
        }

        .ticket-controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .quantity-controls {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .qty-btn {
            width: 32px;
            height: 32px;
            border: 1px solid #D1D5DB;
            border-radius: 50%;
            background: white;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            transition: all 0.2s;
        }

        .qty-btn:hover {
            border-color: #4F46E5;
            color: #4F46E5;
        }

        .qty-btn:disabled {
            opacity: 0.3;
            cursor: not-allowed;
        }

        .qty-display {
            font-weight: 600;
            font-size: 16px;
            width: 30px;
            text-align: center;
        }

        .price-summary {
            margin-top: 25px;
            padding-top: 20px;
            border-top: 2px solid #E5E7EB;
        }

        .price-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            font-size: 15px;
        }

        .price-label {
            color: #6B7280;
        }

        .price-value {
            font-weight: 600;
            color: #1a1a1a;
        }

        .price-total {
            display: flex;
            justify-content: space-between;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #E5E7EB;
            font-size: 18px;
            font-weight: 700;
        }

        .btn-get-tickets {
            width: 100%;
            background: #4F46E5;
            color: white;
            padding: 16px;
            border-radius: 10px;
            border: none;
            font-weight: 700;
            font-size: 16px;
            cursor: pointer;
            margin-top: 20px;
            transition: all 0.2s;
        }

        .btn-get-tickets:hover {
            background: #4338CA;
            transform: translateY(-1px);
        }

        .btn-get-tickets:disabled {
            background: #9CA3AF;
            cursor: not-allowed;
            transform: none;
        }
    </style>
</head>

<body>
    <?php include 'includes/nav_bar.php'; ?>

    <div class="breadcrumb">
        <a href="index.php">Home</a>
        <span>/</span>
        <a href="#">Events</a>
        <span>/</span>
        <span><?php echo htmlspecialchars($event['title']); ?></span>
    </div>

    <div class="container">
        <div class="event-main">
            <div class="event-media">
                <?php if (!empty($event['image'])): ?>
                    <img src="admin/<?php echo htmlspecialchars($event['image']); ?>" 
                         alt="<?php echo htmlspecialchars($event['title']); ?>"
                         style="width: 100%; height: 100%; object-fit: cover;">
                <?php else: ?>
                <?php endif; ?>
            </div>

            <div class="event-header">
                <div>
                    <h1 class="event-title">
                        <?php echo htmlspecialchars($event['title']); ?>
                        <?php 
                        $status = strtolower($event['status']);
                        $status_class = 'status-' . $status;
                        $status_text = ucfirst($status);
                        ?>
                        <span class="status-badge <?php echo $status_class; ?>"><?php echo $status_text; ?></span>
                    </h1>
                    <div class="event-datetime">
                        <?php
                        $start = date('D, M j, Y, g:i A', strtotime($event['start_date']));
                        $end = date('g:i A T', strtotime($event['end_date']));
                        echo $start . ' - ' . $end;
                        ?>
                        at <a href="#location" class="event-location-link"><?php echo htmlspecialchars($event['location']); ?></a>
                    </div>
                    <div class="event-meta">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                            <line x1="16" y1="2" x2="16" y2="6"/>
                            <line x1="8" y1="2" x2="8" y2="6"/>
                            <line x1="3" y1="10" x2="21" y2="10"/>
                        </svg>
                        Posted on <?php echo date('F j, Y', strtotime($event['created_at'])); ?>
                    </div>
                </div>

            </div>

            <div class="tabs">
                <div class="tab active">Details</div>
            </div>

            <div class="section">
                <h2 class="section-title">About this Event</h2>
                <div class="section-content">
                    <?php if (!empty($event['description'])): ?>
                        <p><?php echo nl2br(htmlspecialchars($event['description'])); ?></p>
                    <?php else: ?>
                        <p>Join us for the most anticipated event of the year, the <?php echo htmlspecialchars($event['title']); ?>! 
                        Experience an unforgettable time with amazing activities and entertainment. Set in the iconic 
                        <?php echo htmlspecialchars($event['location']); ?>, this event promises an experience you won't forget.</p>
                        <br>
                        <p>Don't miss out on this incredible opportunity to be part of something special. 
                        Food, entertainment, and exclusive experiences will be available on-site.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="section">
                <h2 class="section-title">Event Schedule</h2>
                <ul class="agenda-list">
                    <li class="agenda-item">
                        <?php echo date('g:i A', strtotime($event['start_date'])); ?> - Event Doors Open
                    </li>
                    <li class="agenda-item">
                        <?php echo date('g:i A', strtotime($event['start_date']) + 1800); ?> - Opening Activities
                    </li>
                    <li class="agenda-item">
                        <?php echo date('g:i A', strtotime($event['start_date']) + 3600); ?> - Main Program Begins
                    </li>
                    <li class="agenda-item">
                        <?php echo date('g:i A', strtotime($event['end_date'])); ?> - Event Concludes
                    </li>
                </ul>

                <div class="tags" style="margin-top: 20px;">
                    <span class="tag">#Event</span>
                    <span class="tag">#Live</span>
                    <span class="tag">#<?php echo htmlspecialchars($event['location']); ?></span>
                    <span class="tag">#<?php echo date('Y', strtotime($event['start_date'])); ?></span>
                </div>
            </div>

            <div class="section location-section" id="location">
                <h2 class="section-title">Location</h2>
                <p class="section-content"><?php echo htmlspecialchars($event['location']); ?></p>
                <div class="map">
                    <?php if (!empty($event['map_iframe'])): ?>
                        <?php echo $event['map_iframe']; ?>
                    <?php else: ?>
                        <div class="no-map">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                style="opacity: 0.3;">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
                                <circle cx="12" cy="10" r="3" />
                            </svg>
                            <span style="margin-left: 10px;">No map available</span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="ticket-sidebar">
            <div class="ticket-card">
                <h3 class="ticket-card-title">Tickets</h3>

                <?php 
                $has_tickets = false;
                mysqli_data_seek($tickets_result, 0);
                while ($ticket = mysqli_fetch_assoc($tickets_result)):
                    $has_tickets = true;
                    $available = $ticket['quantity'] - $ticket['sold'];
                    $is_sold_out = $available <= 0;
                ?>
                    <div class="ticket-item">
                        <div class="ticket-header">
                            <div>
                                <div class="ticket-name"><?php echo htmlspecialchars($ticket['ticket_name']); ?></div>
                                <?php if ($is_sold_out): ?>
                                    <div class="ticket-status">Sold Out</div>
                                <?php else: ?>
                                    <div class="ticket-status" style="color: #10B981;">
                                        <?php echo $available; ?> available
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="ticket-price">Rs <?php echo number_format($ticket['price'], 2); ?></div>
                        </div>

                        <div class="ticket-controls">
                            <div class="quantity-controls">
                                <button class="qty-btn" 
                                        onclick="updateQuantity(<?php echo $ticket['id']; ?>, -1)" 
                                        <?php if ($is_sold_out) echo 'disabled'; ?>>-</button>
                                <span class="qty-display" id="qty-<?php echo $ticket['id']; ?>">0</span>
                                <button class="qty-btn" 
                                        onclick="updateQuantity(<?php echo $ticket['id']; ?>, 1)" 
                                        <?php if ($is_sold_out) echo 'disabled'; ?>>+</button>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>

                <?php if (!$has_tickets): ?>
                    <p style="text-align: center; color: #6B7280; padding: 20px 0;">
                        No tickets available at this time
                    </p>
                <?php endif; ?>

                <div class="price-summary">
                    <div class="price-row">
                        <span class="price-label">Subtotal</span>
                        <span class="price-value" id="subtotal">Rs 0</span>
                    </div>
                    <div class="price-row">
                        <span class="price-label">Service Fees (Rs 10)</span>
                        <span class="price-value" id="fees">Rs 0.00</span>
                    </div>
                    <div class="price-total">
                        <span>Total</span>
                        <span id="total">Rs 0</span>
                    </div>
                </div>

                <?php 
                $event_status = strtolower($event['status']);
                $is_event_cancelled = ($event_status === 'cancelled');
                $is_event_completed = ($event_status === 'completed');
                ?>

                <button class="btn-get-tickets" 
                        onclick="checkout()" 
                        <?php if ($is_event_cancelled || $is_event_completed || !$has_tickets) echo 'disabled'; ?>>
                    <?php 
                    if ($is_event_cancelled) {
                        echo 'Event Cancelled';
                    } elseif ($is_event_completed) {
                        echo 'Event Ended';
                    } elseif (!$has_tickets) {
                        echo 'No Tickets Available';
                    } else {
                        echo 'Get Tickets';
                    }
                    ?>
                </button>
            </div>
        </div>
    </div>


    <script>
        const tickets = <?php
        mysqli_data_seek($tickets_result, 0);
        $tickets_array = [];
        while ($t = mysqli_fetch_assoc($tickets_result)) {
            $tickets_array[] = $t;
        }
        echo json_encode($tickets_array);
        ?>;

        const quantities = {};
        tickets.forEach(ticket => {
            quantities[ticket.id] = 0;
        });

        function updateQuantity(ticketId, change) {
            const currentQty = quantities[ticketId];
            const newQty = Math.max(0, currentQty + change);
            quantities[ticketId] = newQty;

            document.getElementById('qty-' + ticketId).textContent = newQty;
            updatePrices();
        }

        function updatePrices() {
            let subtotal = 0;

            tickets.forEach(ticket => {
                const qty = quantities[ticket.id];
                subtotal += qty * parseFloat(ticket.price);
            });

            const fees = 10;
            const total = subtotal + fees;

            document.getElementById('subtotal').textContent = 'Rs ' + subtotal.toFixed(2);
            document.getElementById('fees').textContent = 'Rs ' + fees.toFixed(2);
            document.getElementById('total').textContent = 'Rs ' + total.toFixed(2);
        }

        function checkout() {
            let selectedTickets = [];
            for (let id in quantities) {
                if (quantities[id] > 0) {
                    selectedTickets.push(id + ':' + quantities[id]);
                }
            }

            if (selectedTickets.length > 0) {
                <?php if (isset($_SESSION['user_id'])): ?>
                    const ticketsParam = selectedTickets.join(',');
                    window.location.href = 'checkout.php?event_id=<?php echo $event_id; ?>&tickets=' + ticketsParam;
                <?php else: ?>
                    if (confirm('You need to login to purchase tickets. Redirect to login page?')) {
                        window.location.href = 'login.php';
                    }
                <?php endif; ?>
            } else {
                alert('Please select at least one ticket');
            }
        }

        function toggleUserMenu() {
            const menu = document.getElementById('userMenu');
            if (menu.style.display === 'none') {
                menu.style.display = 'block';
            } else {
                menu.style.display = 'none';
            }
        }

        // Close menu when clicking outside
        document.addEventListener('click', function (event) {
            const menu = document.getElementById('userMenu');
            const avatar = document.querySelector('.user-avatar');
            if (menu && !avatar.contains(event.target)) {
                menu.style.display = 'none';
            }
        });
    </script>
</body>
<?php include 'includes/footer.php'; ?>
</html>