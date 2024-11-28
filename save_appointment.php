<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: loginregister.php");
    exit();
}

// Fetch input data
$service_id = $_POST['service_id'];
$appointment_date = $_POST['appointment_date'];
$appointment_time = $_POST['appointment_time'];
$name_of_deceased = $_POST['name_of_deceased'] ?? null;
$date_of_burial = $_POST['date_of_burial'] ?? null;
$purpose = $_POST['purpose'];
$username = $_SESSION['username'];

// Database connection
$servername = "localhost"; // Update if necessary
$username_db = "root";      // Update with your DB username
$password_db = "";          // Update with your DB password
$dbname = "logincemetery_db"; // Update if necessary

$conn = new mysqli($servername, $username_db, $password_db, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Insert appointment
$sql = "INSERT INTO appointments (service_id, username, appointment_date, appointment_time, name_of_deceased, date_of_burial, purpose)
        VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("issssss", $service_id, $username, $appointment_date, $appointment_time, $name_of_deceased, $date_of_burial, $purpose);

if ($stmt->execute()) {
    echo "Appointment successfully made!";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
