<?php
session_start();

// Remove specific session items
unset($_SESSION['admin_id']);
unset($_SESSION['admin_name']);

// Or clear everything:
// session_unset();

// Destroy session fully
session_destroy();

// Redirect
header("Location: login.php");
exit();
?>
