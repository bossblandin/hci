<?php
// Database connection details
$servername = "localhost"; // Database server (e.g., localhost)
$username = "root";        // Database username (e.g., root)
$password = "";            // Database password (leave blank for XAMPP default)
$dbname = "logincemetery_db"; // Your database name

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Optional: To use UTF-8 encoding for proper character handling
mysqli_set_charset($conn, "utf8");
?>
