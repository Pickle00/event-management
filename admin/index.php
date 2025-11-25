<?php
include '../config.php';
include 'auth_check.php';

// Fetch Total Events
$events_sql = "SELECT COUNT(*) as total FROM events";
$events_result = mysqli_query($conn, $events_sql);
$total_events = mysqli_fetch_assoc($events_result)['total'];

// Fetch Total Tickets Sold
$tickets_sql = "SELECT SUM(sold) as total FROM ticket_types";
$tickets_result = mysqli_query($conn, $tickets_sql);
$total_tickets_sold = mysqli_fetch_assoc($tickets_result)['total'] ?? 0;

// Fetch Total Revenue
$revenue_sql = "SELECT SUM(total) as total FROM orders WHERE status = 'completed'";
$revenue_result = mysqli_query($conn, $revenue_sql);
$total_revenue = mysqli_fetch_assoc($revenue_result)['total'] ?? 0;

// Fetch Recent Orders
$orders_sql = "SELECT o.*, u.name as user_name, e.title as event_title 
               FROM orders o 
               JOIN users u ON o.user_id = u.id 
               JOIN events e ON o.event_id = e.id 
               ORDER BY o.created_at DESC LIMIT 5";
$orders_result = mysqli_query($conn, $orders_sql);

// Fetch Upcoming Events
$upcoming_sql = "SELECT * FROM events WHERE start_date >= NOW() ORDER BY id DESC LIMIT 5";
$upcoming_result = mysqli_query($conn, $upcoming_sql);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="css/events.css">
    <link rel="stylesheet" href="css/dashboard.css">
</head>

<body>
    <div class="sidebar">
        <div class="logo">
            <div class="logo-icon"></div>
            <span class="logo-text">Ticketly</span>
        </div>

        <a href="index.php" class="nav-item active">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <rect x="3" y="3" width="7" height="7" />
                <rect x="14" y="3" width="7" height="7" />
                <rect x="14" y="14" width="7" height="7" />
                <rect x="3" y="14" width="7" height="7" />
            </svg>
            Dashboard
        </a>

        <a href="events.php" class="nav-item">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                <line x1="16" y1="2" x2="16" y2="6" />
                <line x1="8" y1="2" x2="8" y2="6" />
                <line x1="3" y1="10" x2="21" y2="10" />
            </svg>
            Events
        </a>

        <a href="create_event.php" class="nav-item">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                <circle cx="9" cy="7" r="4" />
                <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                <path d="M16 3.13a4 4 0 0 1 0 7.75" />
            </svg>
            Create Event
        </a>

        <a href="logout.php" class="logout-link">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                <polyline points="16 17 21 12 16 7" />
                <line x1="21" y1="12" x2="9" y2="12" />
            </svg>
            Logout
        </a>

        <div class="user-section">
            <div class="user-avatar"></div>
            <div class="user-info">
                <div class="user-name">Admin User</div>
                <div class="user-role">Administrator</div>
            </div>
        </div>
    </div>

    <div class="main-content">
        <div class="header">
            <h1 class="page-title">Dashboard</h1>
        </div>

        <div class="dashboard-grid">
            <div class="stat-card">
                <div class="stat-icon bg-blue">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                        <line x1="16" y1="2" x2="16" y2="6" />
                        <line x1="8" y1="2" x2="8" y2="6" />
                        <line x1="3" y1="10" x2="21" y2="10" />
                    </svg>
                </div>
                <div class="stat-content">
                    <div class="stat-value"><?php echo $total_events; ?></div>
                    <div class="stat-label">Total Events</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon bg-green">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z" />
                        <line x1="7" y1="7" x2="7.01" y2="7" />
                    </svg>
                </div>
                <div class="stat-content">
                    <div class="stat-value"><?php echo number_format($total_tickets_sold); ?></div>
                    <div class="stat-label">Tickets Sold</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon bg-purple">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <line x1="12" y1="1" x2="12" y2="23" />
                        <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
                    </svg>
                </div>
                <div class="stat-content">
                    <div class="stat-value">Rs <?php echo number_format($total_revenue, 2); ?></div>
                    <div class="stat-label">Total Revenue</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon bg-orange">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                        <circle cx="9" cy="7" r="4" />
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                        <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                    </svg>
                </div>
                <div class="stat-content">
                    <div class="stat-value">--</div>
                    <div class="stat-label">Active Users</div>
                </div>
            </div>
        </div>

        <div class="dashboard-sections">
            <div class="section-card">
                <div class="section-header">
                    <h2 class="section-title">Recent Orders</h2>
                    <a href="#" class="view-all">View All</a>
                </div>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Event</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($order = mysqli_fetch_assoc($orders_result)): ?>
                                <tr>
                                    <td>#<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?></td>
                                    <td><?php echo htmlspecialchars($order['user_name']); ?></td>
                                    <td><?php echo htmlspecialchars($order['event_title']); ?></td>
                                    <td>Rs <?php echo number_format($order['total'], 2); ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo strtolower($order['status']); ?>">
                                            <?php echo ucfirst($order['status']); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="section-card">
                <div class="section-header">
                    <h2 class="section-title">Upcoming Events</h2>
                    <a href="events.php" class="view-all">View All</a>
                </div>
                <div class="event-list">
                    <?php while ($event = mysqli_fetch_assoc($upcoming_result)):
                        $date = strtotime($event['start_date']);
                        ?>
                        <div class="event-item">
                            <div class="event-date-box">
                                <span class="date-month"><?php echo date('M', $date); ?></span>
                                <span class="date-day"><?php echo date('d', $date); ?></span>
                            </div>
                            <div class="event-details">
                                <a href="edit_event.php?id=<?php echo $event['id']; ?>" class="event-name">
                                    <?php echo htmlspecialchars($event['title']); ?>
                                </a>
                                <span class="event-meta">
                                    <?php echo htmlspecialchars($event['location']); ?>
                                </span>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </div>
</body>

</html>