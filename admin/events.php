<?php
include '../config.php';
include 'auth_check.php';

$search = '';
$date_filter = '';
$status_filter = '';

if (isset($_GET['search'])) {
    $search = $_GET['search'];
}

if (isset($_GET['date'])) {
    $date_filter = $_GET['date'];
}

if (isset($_GET['status'])) {
    $status_filter = $_GET['status'];
}

$sql = "SELECT e.*, 
        (SELECT SUM(sold) FROM ticket_types WHERE event_id = e.id) as tickets_sold,
        (SELECT SUM(quantity) FROM ticket_types WHERE event_id = e.id) as total_tickets
        FROM events e WHERE 1=1";

if ($search != '') {
    $sql .= " AND title LIKE '%$search%'";
}

if ($status_filter != '') {
    $sql .= " AND status = '$status_filter'";
}

$sql .= " ORDER BY start_date DESC";

$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Management</title>
    <link rel="stylesheet" href="css/events.css">
</head>

<body>
    <div class="sidebar">
        <div class="logo">
            <div class="logo-icon"></div>
            <span class="logo-text">Ticketly</span>
        </div>

        <a href="index.php" class="nav-item">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <rect x="3" y="3" width="7" height="7" />
                <rect x="14" y="3" width="7" height="7" />
                <rect x="14" y="14" width="7" height="7" />
                <rect x="3" y="14" width="7" height="7" />
            </svg>
            Dashboard
        </a>

        <a href="events.php" class="nav-item active">
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
            <h1 class="page-title">Event Management</h1>
            <a href="create_event.php" class="create-btn">
                <svg class="create-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10" />
                    <line x1="12" y1="8" x2="12" y2="16" />
                    <line x1="8" y1="12" x2="16" y2="12" />
                </svg>
                Create New
            </a>
        </div>

        <div class="filters">
            <div class="search-box">
                <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <circle cx="11" cy="11" r="8" />
                    <path d="m21 21-4.35-4.35" />
                </svg>
                <input type="text" class="search-input" placeholder="Search by event name..." id="search-input"
                    value="<?php echo $search; ?>">
            </div>

            <select class="filter-select" id="date-filter">
                <option value="">Date Range</option>
                <option value="today">Today</option>
                <option value="week">This Week</option>
                <option value="month">This Month</option>
            </select>

            <select class="filter-select" id="status-filter" onchange="filterStatus()">
                <option value="">Status</option>
                <option value="Upcoming" <?php if ($status_filter == 'Upcoming')
                    echo 'selected'; ?>>Upcoming</option>
                <option value="Past" <?php if ($status_filter == 'Past')
                    echo 'selected'; ?>>Past</option>
                <option value="Draft" <?php if ($status_filter == 'Draft')
                    echo 'selected'; ?>>Draft</option>
                <option value="Cancelled" <?php if ($status_filter == 'Cancelled')
                    echo 'selected'; ?>>Cancelled</option>
            </select>
        </div>

        <div class="events-table">
            <table>
                <thead>
                    <tr>
                        <th>EVENT NAME</th>
                        <th>DATE</th>
                        <th>LOCATION</th>
                        <th>TICKETS SOLD</th>
                        <th>STATUS</th>
                        <th>ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $count = 0;
                    while ($row = mysqli_fetch_assoc($result)) {
                        $count++;
                        $sold = $row['tickets_sold'] ? $row['tickets_sold'] : 0;
                        $total = $row['total_tickets'] ? $row['total_tickets'] : 1;
                        $percentage = ($sold / $total) * 100;

                        $status_class = 'status-' . strtolower($row['status']);

                        $date = date('Y-m-d', strtotime($row['start_date']));
                        ?>
                        <tr>
                            <td class="event-name"><?php echo $row['title']; ?></td>
                            <td><?php echo $date; ?></td>
                            <td><?php echo $row['location']; ?></td>
                            <td>
                                <?php echo $sold . ' / ' . $total; ?>
                                <div class="progress-bar">
                                    <div class="progress-fill"
                                        style="width: <?php echo $percentage; ?>%; background: <?php echo $percentage < 50 ? '#3B82F6' : ($percentage < 80 ? '#3B82F6' : '#3B82F6'); ?>;">
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="status-badge <?php echo $status_class; ?>">
                                    <?php echo $row['status']; ?>
                                </span>
                            </td>
                            <td>
                                <a href="edit_event.php?id=<?php echo $row['id']; ?>" class="action-btn" title="Edit">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                                    </svg>
                                </a>
                                <a href="delete_event.php?id=<?php echo $row['id']; ?>" class="action-btn" title="Delete"
                                    onclick="return confirm('Are you sure you want to delete this event? This action cannot be undone.');"
                                    style="color: #EF4444;">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <polyline points="3 6 5 6 21 6" />
                                        <path
                                            d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>

            <div class="results-info">
                Showing 1 to <?php echo $count; ?> of <?php echo $count; ?> results
            </div>
        </div>
    </div>

    <script>
        document.getElementById('search-input').addEventListener('keyup', function (e) {
            if (e.key === 'Enter') {
                const search = this.value;
                window.location.href = 'events.php?search=' + search;
            }
        });

        function filterStatus() {
            const status = document.getElementById('status-filter').value;
            window.location.href = 'events.php?status=' + status;
        }
    </script>
</body>

</html>