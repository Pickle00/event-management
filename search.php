<?php
session_start();
include 'config.php';

$search_query = isset($_GET['q']) ? trim($_GET['q']) : '';
$results = [];

// 1. Fetch all events
$sql = "SELECT * FROM events ORDER BY title ASC";
$result = mysqli_query($conn, $sql);
$events = [];
while ($row = mysqli_fetch_assoc($result)) {
    $events[] = $row;
}

// 2. Sort events by title (already sorted by SQL, but doing it in PHP to be safe/explicit for the algo)
usort($events, function ($a, $b) {
    return strcasecmp($a['title'], $b['title']);
});

// 3. Binary Search Implementation (Find first occurrence)
function binarySearchFirst($arr, $query) {
    $low = 0;
    $high = count($arr) - 1;
    $result_index = -1;
    $query_len = strlen($query);

    while ($low <= $high) {
        $mid = floor(($low + $high) / 2);
        $title = $arr[$mid]['title'];
        
        // Compare only the prefix of the title with the query
        $cmp = strncasecmp($title, $query, $query_len);

        if ($cmp == 0) {
            // Match found, but we want the first one, so continue searching left
            $result_index = $mid;
            $high = $mid - 1;
        } elseif ($cmp < 0) {
            // Title is "smaller" than query, search right
            $low = $mid + 1;
        } else {
            // Title is "larger" than query, search left
            $high = $mid - 1;
        }
    }
    return $result_index;
}

if ($search_query !== '') {
    $first_index = binarySearchFirst($events, $search_query);

    if ($first_index != -1) {
        // Collect all matching events starting from the first match
        $query_len = strlen($search_query);
        for ($i = $first_index; $i < count($events); $i++) {
            if (strncasecmp($events[$i]['title'], $search_query, $query_len) === 0) {
                $results[] = $events[$i];
            } else {
                // Since array is sorted, once we stop matching, we can stop
                break;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="css/nav_bar.css">
    <link rel="stylesheet" href="css/index.css">
    <title>Search Results - TicketMaster</title>
    <style>
        .search-results-header {
            padding: 40px 20px;
            background: #f8f9fa;
            text-align: center;
        }
        .search-query-display {
            font-size: 24px;
            font-weight: 700;
            color: #1a1a1a;
        }
        .no-results {
            text-align: center;
            padding: 60px 20px;
            color: #6B7280;
            font-size: 18px;
        }
    </style>
</head>

<body>
    <?php include 'includes/nav_bar.php'; ?>

    <div class="search-results-header">
        <h1 class="search-query-display">
            <?php if ($search_query): ?>
                Results for "<?php echo htmlspecialchars($search_query); ?>"
            <?php else: ?>
                All Events
            <?php endif; ?>
        </h1>
        <p><?php echo count($results); ?> events found</p>
    </div>

    <div class="content">
        <?php if (empty($results) && $search_query): ?>
            <div class="no-results">
                <p>No events found matching your search.</p>
                <a href="index.php" style="color: #4F46E5; text-decoration: none; margin-top: 10px; display: inline-block;">Browse all events</a>
            </div>
        <?php else: ?>
            <div class="events-grid">
                <?php foreach ($results as $event): 
                    $ticket_sql = "SELECT MIN(price) as min_price FROM ticket_types WHERE event_id = " . $event['id'];
                    $ticket_result = mysqli_query($conn, $ticket_sql);
                    $ticket = mysqli_fetch_assoc($ticket_result);
                    $min_price = $ticket['min_price'] ? $ticket['min_price'] : 0;
                    $date = date('D, M j, g:i A', strtotime($event['start_date']));
                ?>
                    <a href="event_detail.php?id=<?php echo $event['id']; ?>" style="text-decoration: none;">
                        <div class="event-card">
                            <div class="event-image"
                                style="  <?php if (!empty($event['image'])): ?>
                                background-image: url('admin/<?php echo htmlspecialchars($event['image']); ?>');
                                background-size: cover;
                                background-position: center;
                            <?php else: ?>
                                background: linear-gradient(135deg, <?php echo '#' . substr(md5($event['id']), 0, 6); ?> 0%, <?php echo '#' . substr(md5($event['id'] . 'salt'), 0, 6); ?> 100%);
                            <?php endif; ?>">
                            </div>
                            <div class="event-content">
                                <h3 class="event-title"><?php echo $event['title']; ?></h3>
                                <div class="event-info">
                                    <div class="event-detail">
                                        <svg class="event-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                                            <line x1="16" y1="2" x2="16" y2="6" />
                                            <line x1="8" y1="2" x2="8" y2="6" />
                                            <line x1="3" y1="10" x2="21" y2="10" />
                                        </svg>
                                        <?php echo $date; ?>
                                    </div>
                                    <div class="event-detail">
                                        <svg class="event-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
                                            <circle cx="12" cy="10" r="3" />
                                        </svg>
                                        <?php echo $event['location']; ?>
                                    </div>
                                </div>
                                <button class="btn-buy">Buy Tickets</button>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
