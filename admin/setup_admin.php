<?php
include '../config.php';

// Create admins table
$sql = "CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
)";

if (mysqli_query($conn, $sql)) {
    echo "Table 'admins' created successfully.<br>";
} else {
    echo "Error creating table: " . mysqli_error($conn) . "<br>";
}

// Seed default admin user
$username = 'admin';
$password = 'admin123'; // Plain text as requested

// Check if admin already exists
$check_sql = "SELECT * FROM admins WHERE username = '$username'";
$check_result = mysqli_query($conn, $check_sql);

if (mysqli_num_rows($check_result) == 0) {
    $insert_sql = "INSERT INTO admins (username, password) VALUES ('$username', '$password')";
    if (mysqli_query($conn, $insert_sql)) {
        echo "Default admin user created successfully.<br>";
        echo "Username: $username<br>";
        echo "Password: $password<br>";
    } else {
        echo "Error creating admin user: " . mysqli_error($conn) . "<br>";
    }
} else {
    echo "Admin user '$username' already exists.<br>";
}

mysqli_close($conn);
?>
