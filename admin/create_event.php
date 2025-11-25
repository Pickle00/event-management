<?php
include '../config.php';
include 'auth_check.php';
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['status'])) {
        $status = $_POST['status'];
    } else {
        $status = 'Draft';
    }

    $title = $_POST['title'];
    $description = $_POST['description'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $location = $_POST['location'];
    $map_iframe = $_POST['map_iframe'];
    $image = '';

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "uploads/";

        // Create uploads directory if not exists
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        // File info
        $file_tmp = $_FILES['image']['tmp_name'];
        $file_name = basename($_FILES['image']['name']);
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        // Validate file extension
        if (in_array($file_ext, $allowed_ext)) {
            // Generate unique file name
            $new_filename = uniqid('event_', true) . '.' . $file_ext;
            $target_file = $target_dir . $new_filename;

            // Move file
            if (move_uploaded_file($file_tmp, $target_file)) {
                $image = $target_file;
            } else {
                $error_message = "Error uploading image. Please try again.";
            }
        } else {
            $error_message = "Invalid image format. Only JPG, PNG, GIF, or WEBP allowed.";
        }
    }



    $sql = "INSERT INTO events (title, description, start_date, end_date, location, image, map_iframe, status) 
            VALUES ('$title', '$description', '$start_date', '$end_date', '$location', '$image', '$map_iframe', '$status')";

    if (mysqli_query($conn, $sql)) {
        $event_id = mysqli_insert_id($conn);

        if (isset($_POST['ticket_names'])) {
            $ticket_names = $_POST['ticket_names'];
            $ticket_prices = $_POST['ticket_prices'];
            $ticket_quantities = $_POST['ticket_quantities'];

            for ($i = 0; $i < count($ticket_names); $i++) {
                $ticket_name = $ticket_names[$i];
                $ticket_price = $ticket_prices[$i];
                $ticket_quantity = $ticket_quantities[$i];

                $ticket_sql = "INSERT INTO ticket_types (event_id, ticket_name, price, quantity) 
                              VALUES ('$event_id', '$ticket_name', '$ticket_price', '$ticket_quantity')";
                mysqli_query($conn, $ticket_sql);
            }
        }

        $success_message = "Event created successfully!";
        header("Location: events.php");
        exit();
    } else {
        $error_message = "Error: " . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Event</title>
    <link rel="stylesheet" href="css/create_event.css">
</head>

<body>
    <div class="sidebar">
        <div class="logo">
            <div class="logo-icon"></div>
            <span class="logo-text">Ticketeer</span>
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

        <a href="events.php" class="nav-item">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                <line x1="16" y1="2" x2="16" y2="6" />
                <line x1="8" y1="2" x2="8" y2="6" />
                <line x1="3" y1="10" x2="21" y2="10" />
            </svg>
            Events
        </a>

        <a href="create_event.php" class="nav-item active">
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
        <div class="page-header">
            <h1 class="page-title">Create New Event</h1>
        </div>

        <?php if ($success_message): ?>
            <div style="background: #D1FAE5; color: #065F46; padding: 12px; border-radius: 6px; margin-bottom: 20px;">
                <?php echo $success_message; ?>
            </div>
        <?php endif; ?>

        <?php if ($error_message): ?>
            <div style="background: #FEE2E2; color: #991B1B; padding: 12px; border-radius: 6px; margin-bottom: 20px;">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="form-container">
            <div class="section-title">Event Details</div>

            <div class="form-group">
                <label>Event Title</label>
                <input type="text" name="title" placeholder="Annual Tech Summit 2024" required>
            </div>

            <div class="form-group">
                <label>Event Description</label>
                <textarea name="description" placeholder="Describe your event in detail..."></textarea>
            </div>

            <div class="form-group">
                <label>Status</label>
                <select name="status" required>
                    <option value="Upcoming">Upcoming</option>
                    <option value="Draft">Draft</option>
                    <option value="Past">Past</option>
                    <option value="Cancelled">Cancelled</option>
                </select>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Start Date & Time</label>
                    <input type="datetime-local" name="start_date" required>
                </div>

                <div class="form-group">
                    <label>End Date & Time</label>
                    <input type="datetime-local" name="end_date" required>
                </div>
            </div>

            <div class="form-group">
                <label>Location</label>
                <input type="text" name="location" placeholder="123 Main St, Anytown, USA">
            </div>

            <div class="form-group">
                <label>Map Embed Code (Google Maps iframe)</label>
                <textarea name="map_iframe"
                    placeholder='<iframe src="https://www.google.com/maps/embed?pb=..." width="100%" height="350" style="border:0;" allowfullscreen="" loading="lazy"></iframe>'
                    style="min-height: 80px; font-family: monospace; font-size: 13px;"></textarea>
                <div style="font-size: 12px; color: #6B7280; margin-top: 5px;">
                    Get embed code from Google Maps: Search location → Share → Embed a map → Copy HTML
                </div>
            </div>

            <div class="section-title" style="margin-top: 30px;">Event Image</div>

            <div class="form-group">
                <label for="file-upload" class="upload-area">
                    <svg class="upload-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                        <polyline points="17 8 12 3 7 8" />
                        <line x1="12" y1="3" x2="12" y2="15" />
                    </svg>
                    <div class="upload-text">Click to upload or drag and drop</div>
                    <div class="upload-info">SVG, PNG, JPG or GIF (MAX. 800x400px)</div>
                </label>
                <input type="file" id="file-upload" name="image" accept="image/*">
            </div>

            <div class="ticket-section">
                <div class="ticket-header">
                    <div class="section-title" style="margin: 0;">Ticket Types</div>
                </div>

                <div id="ticket-container">
                    <div class="ticket-row">
                        <div class="form-group">
                            <label>Ticket Name</label>
                            <input type="text" name="ticket_names[]" placeholder="General Admission"
                                value="General Admission">
                        </div>

                        <div class="form-group">
                            <label>Price</label>
                            <input type="number" name="ticket_prices[]" placeholder="50.00" step="0.01" value="50.00">
                        </div>

                        <div class="form-group">
                            <label>Quantity</label>
                            <input type="number" name="ticket_quantities[]" placeholder="500" value="500">
                        </div>

                        <button type="button" class="remove-btn" onclick="this.parentElement.remove()">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <polyline points="3 6 5 6 21 6" />
                                <path
                                    d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
                            </svg>
                        </button>
                    </div>
                </div>

                <button type="button" class="add-ticket-btn" onclick="addTicketRow()">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        style="margin-right: 5px;">
                        <circle cx="12" cy="12" r="10" />
                        <line x1="12" y1="8" x2="12" y2="16" />
                        <line x1="8" y1="12" x2="16" y2="12" />
                    </svg>
                    Add Ticket Type
                </button>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-publish">Create Event</button>
            </div>
        </form>
    </div>

    <script>
        document.getElementById('file-upload').addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (evt) {
                    const preview = document.createElement('img');
                    preview.src = evt.target.result;
                    preview.style.maxWidth = '100%';
                    preview.style.marginTop = '10px';
                    const uploadArea = document.querySelector('.upload-area');
                    uploadArea.innerHTML = '';
                    uploadArea.appendChild(preview);
                };
                reader.readAsDataURL(file);
            }
        });
    </script>

</body>

</html>