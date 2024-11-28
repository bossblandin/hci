<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: loginregister.php");  // Redirect if not admin
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

// Fetch pending appointments
$query_pending = "
    SELECT a.id, a.service_id, a.appointment_date, a.appointment_time, a.name_of_deceased, a.date_of_burial, a.purpose, a.username, s.service_name
    FROM appointments a
    JOIN services s ON a.service_id = s.id
    WHERE a.status = 'pending'
";
$result_pending = mysqli_query($conn, $query_pending);

// Fetch on-going appointments (only confirmed)
$query_ongoing = "
    SELECT a.id, a.service_id, a.appointment_date, a.appointment_time, a.name_of_deceased, a.date_of_burial, a.purpose, a.username, a.status, s.service_name
    FROM appointments a
    JOIN services s ON a.service_id = s.id
    WHERE a.status = 'confirmed'
    ORDER BY a.appointment_date ASC
";
$result_ongoing = mysqli_query($conn, $query_ongoing);

// Fetch appointment history (completed only)
$query_history = "
    SELECT a.id, a.service_id, a.appointment_date, a.appointment_time, a.name_of_deceased, a.date_of_burial, a.purpose, a.username, a.status, s.service_name
    FROM appointments a
    JOIN services s ON a.service_id = s.id
    WHERE a.status = 'completed'
    ORDER BY a.appointment_date DESC
";
$result_history = mysqli_query($conn, $query_history);

// Confirm or reject appointment
if (isset($_POST['confirm_id'])) {
    $appointment_id = $_POST['confirm_id'];
    $updateQuery = "UPDATE appointments SET status = 'confirmed' WHERE id = ?";
    $stmt = mysqli_prepare($conn, $updateQuery);
    mysqli_stmt_bind_param($stmt, 'i', $appointment_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    header("Location: hciadminappointments.php");
    exit();
}

if (isset($_POST['reject_id'])) {
    $appointment_id = $_POST['reject_id'];
    $updateQuery = "UPDATE appointments SET status = 'rejected' WHERE id = ?";
    $stmt = mysqli_prepare($conn, $updateQuery);
    mysqli_stmt_bind_param($stmt, 'i', $appointment_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    header("Location: hciadminappointments.php");
    exit();
}

// Mark appointment as completed
if (isset($_POST['done_id'])) {
    $appointment_id = $_POST['done_id'];

    // Debugging log
    error_log("Done button clicked for ID: " . $appointment_id);

    $updateQuery = "UPDATE appointments SET status = 'completed' WHERE id = ?";
    $stmt = mysqli_prepare($conn, $updateQuery);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'i', $appointment_id);
        mysqli_stmt_execute($stmt);
        if (mysqli_stmt_affected_rows($stmt) > 0) {
            error_log("Status updated to 'completed' for ID: " . $appointment_id);
        } else {
            error_log("Update failed for ID: " . $appointment_id);
        }
        mysqli_stmt_close($stmt);
    } else {
        error_log("Statement preparation failed: " . mysqli_error($conn));
    }

    header("Location: hciadminappointments.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Muntinlupa Public Cemetery Admin</title>
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
   height: 100%;
}

.sidebar {
   width: 350px;
   background-color: black;
   color: white;
   padding: 15px;
   height: 100%;
   border-right: 1px solid white;
   display: flex;
   flex-direction: column;
   justify-content: flex-start;
}

.sidebar h2 {
   text-align: center;
   margin: 30px;
   font-size: 40px;
}

.sidebar a, .logout-btn {
   display: block;
   text-decoration: none;
   color: white;
   padding: 12px;
   margin: 4px 0;
   border-radius: 5px;
   cursor: pointer;
   font-size: 20px;
   border: 1px solid white;
   text-align: center;
}

.sidebar form {
   margin-top: auto;
   margin-bottom: 10px;
}

.sidebar a {
   margin-bottom: 30px;
}

.sidebar a:hover, .logout-btn:hover {
   background-color: white;
   color: black;
}

.main-content {
   flex: 1;
   padding: 30px;
   background-color: black;
   overflow-y: auto;
}

.main-content h2 {
   font-size: 2.5em;
   margin-bottom: 20px;
   color: #ccc;
   text-align: center;
   font-weight: bold;
}

.logout-btn {
   background-color: black;
   color: white;
   font-size: 25px;
   cursor: pointer;
   font-weight: bold;
}

form button {
   display: block;
   background-color: black;
   color: white;
   width: 100%;
   padding: 18px;
   margin: 18px 0;
   border: 1px solid black;
   border-radius: 5px;
   border: 1px solid white;
}

table {
   width: 100%;
   border-collapse: collapse;
   margin-top: 20px;
}

th, td {
   padding: 12px;
   text-align: center;
   border: 1px solid #ddd;
   font-size: 1.1em;
}

th {
   background-color: #007bff;
   color: white;
}

td {
   background-color: black;
}

.actions {
   font-size: 16px;
   border: none;
   color: white;
   cursor: pointer;
   padding: 10px 20px;
   margin: 5px;
   border-radius: 5px;
}

.confirm-btn {
   background-color: green;
}

.reject-btn {
   background-color: red;
}

.actions:hover {
   opacity: 0.8;
}

.confirm-btn:hover {
   background-color: darkgreen;
}

.reject-btn:hover {
   background-color: darkred;
}

.box {
   border: 2px solid #007bff;
   padding: 20px;
   margin-bottom: 20px;
   background-color: #333;
   border-radius: 8px;
}

.no-appointments {
   text-align: center;
   font-size: 1.5em;
   color: #ccc;
   margin-top: 20px;
}

.chat-btn {
background-color: #4caf50;
color: white;
font-size: 18px;
padding: 10px 20px;
border: none;
border-radius: 5px;
cursor: pointer;
font-weight: bold;
margin-top: 15px;
}

.chat-btn:hover {
background-color: #45a049;
}
    </style>
</head>
<body>
  <?php include 'hciadminheader.php'; ?>

  <div class="container">
    <div class="sidebar">
        <h2>Admin Menu</h2>
        <a href="hciadmin.php">Admin Report</a>
        <a href="hciannouncement.php">Announcement</a>
        <a href="hciadminservices.php">Services</a>
        <a href="hciadminappointments.php">Appointments</a>
        <a href="hciadminrecords.php">Records</a>
        <a href="hciadminmessenger.php">Message</a>
        <form method="post">
            <button type="submit" name="logout" class="logout-btn">Logout</button>
        </form>
    </div>

    <div class="main-content">
        <!-- Pending Appointments -->
        <div class="box">
            <h2>Pending Appointments</h2>
            <?php if (mysqli_num_rows($result_pending) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Service Name</th>
                            <th>Appointment Date</th>
                            <th>Appointment Time</th>
                            <th>Name of Deceased</th>
                            <th>Date of Burial</th>
                            <th>Purpose</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($result_pending)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['username']); ?></td>
                            <td><?php echo htmlspecialchars($row['service_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['appointment_date']); ?></td>
                            <td><?php echo htmlspecialchars($row['appointment_time']); ?></td>
                            <td><?php echo htmlspecialchars($row['name_of_deceased']); ?></td>
                            <td><?php echo htmlspecialchars($row['date_of_burial']); ?></td>
                            <td><?php echo htmlspecialchars($row['purpose']); ?></td>
                            <td>
                                <form method="post" style="display:inline;">
                                    <button type="submit" name="confirm_id" value="<?php echo $row['id']; ?>" class="actions confirm-btn">Confirm</button>
                                </form>
                                <form method="post" style="display:inline;">
                                    <button type="submit" name="reject_id" value="<?php echo $row['id']; ?>" class="actions reject-btn">Reject</button>
                                </form>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="no-appointments">No Pending Appointments</div>
            <?php endif; ?>
        </div>

        <!-- On-going Appointments -->
        <div class="box">
            <h2>On-going Appointments</h2>
            <?php if (mysqli_num_rows($result_ongoing) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Service Name</th>
                            <th>Appointment Date</th>
                            <th>Appointment Time</th>
                            <th>Name of Deceased</th>
                            <th>Date of Burial</th>
                            <th>Purpose</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($result_ongoing)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['username']); ?></td>
                            <td><?php echo htmlspecialchars($row['service_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['appointment_date']); ?></td>
                            <td><?php echo htmlspecialchars($row['appointment_time']); ?></td>
                            <td><?php echo htmlspecialchars($row['name_of_deceased']); ?></td>
                            <td><?php echo htmlspecialchars($row['date_of_burial']); ?></td>
                            <td><?php echo htmlspecialchars($row['purpose']); ?></td>
                            <td>
                                <form method="post" style="display:inline;">
                                    <button type="submit" name="done_id" value="<?php echo $row['id']; ?>" class="actions confirm-btn">Done</button>
                                </form>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="no-appointments">No On-going Appointments</div>
            <?php endif; ?>
        </div>

        <!-- Appointment History -->
        <div class="box">
            <h2>Appointment History</h2>
            <table>
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Service Name</th>
                        <th>Appointment Date</th>
                        <th>Appointment Time</th>
                        <th>Name of Deceased</th>
                        <th>Date of Burial</th>
                        <th>Purpose</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result_history)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                        <td><?php echo htmlspecialchars($row['service_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['appointment_date']); ?></td>
                        <td><?php echo htmlspecialchars($row['appointment_time']); ?></td>
                        <td><?php echo htmlspecialchars($row['name_of_deceased']); ?></td>
                        <td><?php echo htmlspecialchars($row['date_of_burial']); ?></td>
                        <td><?php echo htmlspecialchars($row['purpose']); ?></td>
                        <td><?php echo htmlspecialchars($row['status']); ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
  </div>
    <?php include 'hcifooter.php'; ?> 
</body>
</html>
