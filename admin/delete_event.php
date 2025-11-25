<?php
include '../config.php';

if (isset($_GET['id'])) {
    $event_id = $_GET['id'];

    // Delete ticket types first
    $delete_tickets_sql = "DELETE FROM ticket_types WHERE event_id = '$event_id'";
    mysqli_query($conn, $delete_tickets_sql);

    // Delete event
    $delete_event_sql = "DELETE FROM events WHERE id = '$event_id'";
    
    if (mysqli_query($conn, $delete_event_sql)) {
        header("Location: events.php?msg=Event deleted successfully");
    } else {
        header("Location: events.php?error=Error deleting event");
    }
} else {
    header("Location: events.php");
}
exit();
?>
