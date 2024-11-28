<?php
session_start();
include 'hciheader.php';

if (!isset($_SESSION['username'])) {
    header("Location: loginregister.php");
    exit();
}

$role = $_SESSION['role'];

// Database connection
$servername = "localhost"; // Update if necessary
$username = "root";        // Update with your DB username
$password = "";            // Update with your DB password
$dbname = "logincemetery_db"; // Update if necessary

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to fetch services posted by admin
$sql = "SELECT id, service_name, description, price_range FROM services";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Muntinlupa Public Cemetery</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600;700;800;900&family=Poppins:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }
        body {
            background-color: #000;
            color: #f5f5f5;
            line-height: 1.5;
        }
        section {
            padding: 50px 8%;
            margin-top: 20px;
            margin-bottom: 20px;
        }
        .header {
            text-align: center;
        }
        .header h1 {
            font-size: 3em;
            color: #e0e0e0;
            margin-bottom: 20px;
            font-family: 'Anton', serif;
        }
        .tagline {
          margin-top: 20px;
            font-size: 2em;
            color: #c0c0c0;
            font-style: italic;
        }
        .scrollable-box {
            background-color: #111;
            border: 1px solid #444;
            border-radius: 8px;
            padding: 20px;
            height: 600px;
            overflow-y: auto; /* Scrollable content */
            margin: 20px 0;
        }
        .service-card {
            background-color: #222;
            border: 1px solid #444;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 25px;
            display: flex; /* Enables flexbox */
            justify-content: space-between; /* Spaces content between */
            align-items: center; /* Aligns items vertically */
        }
        .service-details {
            max-width: 70%; /* Ensure the text stays on the left side */
        }
        .service-details h3 {
          font-size: 2em;
          font-weight: bold;
        }
        .service-details p {
          font-size: 1.2em;
          font-style: italic;
        }
        .appointment-button {
            text-align: right;
        }
        .appointment-button button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 10px;
            cursor: pointer;
            font-size: 1.5em;
        }
        .appointment-button button:hover {
            background-color: #0056b3;
        }
        .view-appointment-btn {
            background-color: black;
            color: white;
            border: 1px solid white;
            padding: 14px 28px;
            text-align: center;
            font-size: 30px;
            font-weight: bold;
            border-radius: 10px;
            text-decoration: none;
            width: 250px;
            margin-top: 50px; /* Ensure some spacing from the content */
        }

        .view-appointment-btn:hover {
            background-color: #0077b3;
        }
    </style>
</head>
<body>
<section>
    <div class="header">
        <h1>OFFERED SERVICES</h1>
        <a href="hciuserappointments.php" class="view-appointment-btn">View Appointments</a>
        <p class="tagline">Request for appointments</p>
    </div>
    <div class="scrollable-box">
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<div class="service-card">';
                echo '<div class="service-details">';
                echo '<h3>' . htmlspecialchars($row['service_name']) . '</h3>';
                echo '<p>' . htmlspecialchars($row['description']) . '</p>';
                echo '<p><strong>Price Range:</strong> ' . htmlspecialchars($row['price_range']) . '</p>';
                echo '</div>';
                echo '<div class="appointment-button">';
                echo '<form method="POST" action="make_appointment.php">';
                echo '<input type="hidden" name="service_id" value="' . htmlspecialchars($row['id']) . '">';
                echo '<button type="submit">Make an Appointment</button>';
                echo '</form>';
                echo '</div>';
                echo '</div>';
            }
        } else {
            echo '<p>No services are currently available.</p>';
        }
        ?>
    </div>
</section>
<?php
$conn->close();
include 'hcifooter.php';
?>
</body>
</html>
