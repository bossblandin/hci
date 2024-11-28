<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: loginregister.php");
    exit();
}

if (isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    header("Location: loginregister.php");
    exit();
}

// Include the database connection
include 'db_connection.php';

// Fetch logged-in user
$username = $_SESSION['username'];

// Fetch pending appointments
$query_pending = "
    SELECT a.id, a.service_id, a.appointment_date, a.appointment_time, a.name_of_deceased, a.date_of_burial, a.purpose, a.status, s.service_name
    FROM appointments a
    JOIN services s ON a.service_id = s.id
    WHERE a.username = ? AND a.status = 'pending'
";
$stmt = mysqli_prepare($conn, $query_pending);

if (!$stmt) {
    die("Query preparation failed: " . mysqli_error($conn));
}

mysqli_stmt_bind_param($stmt, 's', $username);
mysqli_stmt_execute($stmt);
$result_pending = mysqli_stmt_get_result($stmt);

// Fetch accepted/rejected appointments
$query_accepted_rejected = "
    SELECT a.id, a.service_id, a.appointment_date, a.appointment_time, a.name_of_deceased, a.date_of_burial, a.purpose, a.status, s.service_name
    FROM appointments a
    JOIN services s ON a.service_id = s.id
    WHERE a.username = ? AND a.status IN ('confirmed', 'rejected')
";
$stmt_accepted_rejected = mysqli_prepare($conn, $query_accepted_rejected);

if (!$stmt_accepted_rejected) {
    die("Query preparation failed: " . mysqli_error($conn));
}

mysqli_stmt_bind_param($stmt_accepted_rejected, 's', $username);
mysqli_stmt_execute($stmt_accepted_rejected);
$result_accepted_rejected = mysqli_stmt_get_result($stmt_accepted_rejected);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Appointments</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            display: flex;
            flex-direction: column;
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #f5f5f5;
            height: 100vh;
        }

        .container {
            display: flex;
            flex: 1;
            height: 900px;
        }

        .sidebar {
            width: 350px; /* Increased width */
            background-color: black;
            color: white;
            padding: 30px; /* Increased padding */
            height: 100%;
            border: 1px solid white;
        }

        .sidebar h2 {
            text-align: center;
            margin-bottom: 30px; /* Increased margin */
            font-size: 28px; /* Increased font size */
        }

        .sidebar a, .logout-btn {
            display: block;
            text-decoration: none;
            color: white;
            padding: 12px; /* Increased padding */
            margin: 12px 0; /* Increased margin */
            border-radius: 5px;
            cursor: pointer;
            font-size: 18px; /* Increased font size */
            border: 1px solid white;
            text-align: center;
            margin-bottom: 60px;
        }

        .sidebar a:hover, .logout-btn:hover {
            background-color: white;
            color: black;
        }

        .logout-btn{
            background-color: black;
            color: white;
            font-size: 20px; /* Increased font size for button */
            cursor: pointer;
            font-weight: bold;
            margin-top: 400px;
        }

        form input, form button {
            display: block;
            background-color: black;
            color: white;
            width: 100%;
            padding: 18px; /* Increased padding for larger input fields */
            margin: 18px 0; /* Increased margin */
            border: 1px solid black;
            border-radius: 5px;
            font-size: 18px; /* Increased font size for inputs */
        }

        form button {
            font-size: 25px; /* Increased font size for button */
            cursor: pointer;
            font-weight: bold;
        }

        form button:hover {
            background-color: white;
            color: black;
        }

        .main-content {
            flex: 1; /* To avoid overlap with sidebar */
            padding: 30px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            align-items: center; /* Center the content horizontally */
            justify-content: center; /* Center the content vertically */
        }

        /* Heading styling */
        h2 {
            font-size: 36px;
            margin-bottom: 30px;
            color: #f0f0f0;
            font-weight: 600;
            text-align: center;
        }

        /* Appointment card container */
        .appointment-card {
            background-color: #1f1f1f;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s ease;
            width: 100%; /* Allow the card to fill the available space */
            max-width: 1200px; /* Limit the width of the card */
        }

        .appointment-card:hover {
            transform: translateY(-5px);
        }

        /* Appointment card headings */
        .appointment-card h3 {
            font-size: 1.8em;
            margin-bottom: 15px;
            color: #0099cc;
        }

        /* Appointment details */
        .appointment-card p {
            font-size: 1.2em;
            margin: 10px 0;
            line-height: 1.6;
            color: #f0f0f0;
        }

        /* Appointment details labels */
        .appointment-card p span {
            font-weight: bold;
            color: #0099cc;
        }

        /* Status styling */
        .status {
            font-size: 1.2em;
            font-weight: bold;
            color: #ff9800;
            margin-top: 15px;
        }

        /* "No appointments" text */
        p {
            font-size: 1.4em;
            text-align: center;
            margin-top: 20px;
            margin-bottom: 100px;
            color: #ccc;
        }

        /* Make New Appointment Button */
        .make-appointment-btn {
            background-color: black;
            color: white;
            border: 1px solid white;
            padding: 14px 28px;
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            border-radius: 5px;
            text-decoration: none;
            width: 250px;
            margin-top: 30px; /* Ensure some spacing from the content */
        }

        .make-appointment-btn:hover {
            background-color: #0077b3;
        }
    </style>
</head>
<body>
    <?php include 'hciheader.php'; ?>

    <div class="container">
        <div class="sidebar">
            <h2>My Dashboard</h2>
            <a href="hciaccount.php">Personal Information</a>
            <a href="hciuserappointments.php">Appointments</a>
            <a href="hcimessenger.php">Chat with Admin</a>
            <form method="post">
                <button type="submit" name="logout" class="logout-btn">Logout</button>
            </form>
        </div>

        <div class="main-content">
            <h2>Pending Appointments</h2>
            <?php if (mysqli_num_rows($result_pending) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result_pending)): ?>
                    <div class="appointment-card">
                        <h3><?php echo htmlspecialchars($row['service_name']); ?></h3>
                        <p><span>Appointment Date:</span> <?php echo htmlspecialchars($row['appointment_date']); ?></p>
                        <p><span>Appointment Time:</span> <?php echo htmlspecialchars($row['appointment_time']); ?></p>
                        <p><span>Purpose:</span> <?php echo htmlspecialchars($row['purpose']); ?></p>
                        <p><span>Status:</span> <?php echo htmlspecialchars($row['status']); ?></p>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No pending appointments found.</p>
            <?php endif; ?>

            <h2>Accepted/Rejected Appointments</h2>
            <?php if (mysqli_num_rows($result_accepted_rejected) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result_accepted_rejected)): ?>
                    <div class="appointment-card">
                        <h3><?php echo htmlspecialchars($row['service_name']); ?></h3>
                        <p><span>Appointment Date:</span> <?php echo htmlspecialchars($row['appointment_date']); ?></p>
                        <p><span>Appointment Time:</span> <?php echo htmlspecialchars($row['appointment_time']); ?></p>
                        <p><span>Status:</span> <?php echo htmlspecialchars($row['status']); ?></p>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No accepted or rejected appointments found.</p>
            <?php endif; ?>

            <!-- Button centered in the container -->
            <a href="make_appointment.php" class="make-appointment-btn">Make New Appointment</a>
        </div>
    </div>

    <?php include 'hcifooter.php'; ?>
</body>
</html>
