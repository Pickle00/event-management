<?php
session_start();
include 'config.php';

$featured_sql = "SELECT * FROM events WHERE status = 'Upcoming' ORDER BY start_date ASC LIMIT 4";
$featured_result = mysqli_query($conn, $featured_sql);

$upcoming_sql = "SELECT * FROM events WHERE status = 'Upcoming' ORDER BY start_date ASC LIMIT 4, 4";
$upcoming_result = mysqli_query($conn, $upcoming_sql);

$search = '';
if (isset($_GET['search'])) {
    $search = $_GET['search'];
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
    <title>TicketMaster - Discover Your Next Unforgettable Experience</title>
</head>

<body>
    <?php include 'includes/nav_bar.php'; ?>

    <div class="hero">
        <h1 class="hero-title">Discover Your Next Unforgettable<br>Experience</h1>
        <p class="hero-subtitle">Search millions of live events from concerts to sports and everything in between.</p>

        <form action="search.php" method="GET" class="search-container">
            <div class="search-input-wrapper">
                <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <circle cx="11" cy="11" r="8" />
                    <path d="m21 21-4.35-4.35" />
                </svg>
                <input type="text" name="q" class="search-input" placeholder="Search for events, artists, or venues"
                    value="<?php echo $search; ?>">
            </div>
            <button type="submit" class="search-btn">Search</button>
        </form>
    </div>

    <div class="content">
        <h2 class="section-title">Featured Events</h2>

        <div class="events-grid">
            <?php while ($event = mysqli_fetch_assoc($featured_result)):
                $ticket_sql = "SELECT MIN(price) as min_price FROM ticket_types WHERE event_id = " . $event['id'];
                $ticket_result = mysqli_query($conn, $ticket_sql);
                $ticket = mysqli_fetch_assoc($ticket_result);
                $min_price = $ticket['min_price'] ? $ticket['min_price'] : 0;

                $date = date('D, M j, g:i A', strtotime($event['start_date']));
                ?>
                <a href="event_detail.php?id=<?php echo $event['id']; ?>" style="text-decoration: none;">
                    <div class="event-card">
                        <div class="event-image" style="
                        <?php if (!empty($event['image'])): ?>
                            background-image: url('admin/<?php echo htmlspecialchars($event['image']); ?>');
                            background-size: cover;
                            background-position: center;
                        <?php else: ?>
                            background: linear-gradient(135deg, <?php echo '#' . substr(md5($event['id']), 0, 6); ?> 0%, <?php echo '#' . substr(md5($event['id'] . 'salt'), 0, 6); ?> 100%);
                        <?php endif; ?>
                            ">
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
                            <button class="btn-buy btn-buy-light">Buy Tickets</button>
                        </div>
                    </div>
                </a>
            <?php endwhile; ?>
        </div>

        <h2 class="section-title">Upcoming Events</h2>

        <div class="events-grid">
            <?php while ($event = mysqli_fetch_assoc($upcoming_result)):
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
                            <?php if ($min_price > 0): ?>
                            <?php endif; ?>
                            <button class="btn-buy">Buy Tickets</button>
                        </div>
                    </div>
                </a>
            <?php endwhile; ?>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>

</html>