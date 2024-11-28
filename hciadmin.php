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

// Include database connection
include 'db_connection.php'; // Ensure to include your database connection file

// Query to get total number of accounts
$total_accounts_query = "SELECT COUNT(*) AS total_accounts FROM users";
$total_accounts_result = mysqli_query($conn, $total_accounts_query);
$total_accounts_data = mysqli_fetch_assoc($total_accounts_result);
$total_accounts = $total_accounts_data['total_accounts'];

// Query to get total number of records
$total_records_query = "SELECT COUNT(*) AS total_records FROM records";
$total_records_result = mysqli_query($conn, $total_records_query);
$total_records_data = mysqli_fetch_assoc($total_records_result);
$total_records = $total_records_data['total_records'];

// Query to get total number of unread messages
$total_unread_messages_query = "SELECT COUNT(*) AS total_unread_messages FROM messages WHERE receiver = 'admin' AND status = 'unread'";
$total_unread_messages_result = mysqli_query($conn, $total_unread_messages_query);
$total_unread_messages_data = mysqli_fetch_assoc($total_unread_messages_result);
$total_unread_messages = $total_unread_messages_data['total_unread_messages'];


// Query to get total number of pending appointments
$total_pending_appointments_query = "SELECT COUNT(*) AS total_pending_appointments FROM appointments WHERE status = 'pending'";
$total_pending_appointments_result = mysqli_query($conn, $total_pending_appointments_query);
$total_pending_appointments_data = mysqli_fetch_assoc($total_pending_appointments_result);
$total_pending_appointments = $total_pending_appointments_data['total_pending_appointments'];

// Query to get total number of ongoing appointments
$total_ongoing_appointments_query = "SELECT COUNT(*) AS total_ongoing_appointments FROM appointments WHERE status = 'ongoing'";
$total_ongoing_appointments_result = mysqli_query($conn, $total_ongoing_appointments_query);
$total_ongoing_appointments_data = mysqli_fetch_assoc($total_ongoing_appointments_result);
$total_ongoing_appointments = $total_ongoing_appointments_data['total_ongoing_appointments'];

// Query to get total number of completed appointments
$total_completed_appointments_query = "SELECT COUNT(*) AS total_completed_appointments FROM appointments WHERE status = 'completed'";
$total_completed_appointments_result = mysqli_query($conn, $total_completed_appointments_query);
$total_completed_appointments_data = mysqli_fetch_assoc($total_completed_appointments_result);
$total_completed_appointments = $total_completed_appointments_data['total_completed_appointments'];
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
            padding: 40px;
            background-color: #1e1e1e;
            color: white;
            overflow-y: auto;
            height: 100%;
            text-align: center;
        }

        .main-content h2,p {
            font-size: 32px;
            margin-bottom: 20px;
        }


        .report-container {
      display: flex;
      justify-content: space-between;
      flex-wrap: wrap;
      margin-top: 30px;
      gap: 20px; /* Adds space between items */
  }

  .report-item {
      background-color: #2c2c2c; /* Darker background for better contrast */
      padding: 30px;
      border-radius: 12px;
      text-align: center;
      width: 28%;  /* Adjusted width for 3 items */
      margin: 10px 0;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2); /* Subtle shadow for depth */
      transition: background-color 0.3s ease, transform 0.3s ease, box-shadow 0.3s ease; /* Smooth transitions for hover effects */
      border: 1px solid white; /* Border for separation */
      position: relative; /* For any future enhancements like badge indicators */
  }

  .report-item img {
      width: 100px;
      height: 100px;  /* Slightly larger icons */
      margin-bottom: 20px;
      transition: transform 0.3s ease, filter 0.3s ease; /* Smooth icon transitions */
      filter: grayscale(60%); /* Soft grayscale effect for icons */
  }

  .report-item p {
      font-size: 22px;
      font-weight: 600; /* Bold text for report details */
      color: #ccc; /* Light color for the text */
      margin-top: 10px;
  }

  .report-item:hover {
      background-color: #333;  /* Slightly lighter background on hover */
      transform: translateY(-8px);  /* Lift effect with smooth transition */
      box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3); /* Larger shadow on hover for depth */
  }

  .report-item img:hover {
      transform: scale(1.1); /* Icon enlarges on hover */
      filter: grayscale(0%); /* Full color on hover */
  }

  /* Make the content responsive */
  @media screen and (max-width: 768px) {
      .report-item {
          width: 45%;
      }
  }

  @media screen and (max-width: 480px) {
      .report-item {
          width: 100%;
      }
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
            <a href="hciadminmessenger.php">Messages</a>
            <form method="post">
                <button type="submit" name="logout" class="logout-btn">Logout</button>
            </form>
        </div>

        <div class="main-content">
            <h2>Admin Report</h2>
            <p>Here is an overview of system activity and reports:</p>

            <div class="report-container">
                <div class="report-item">
                    <img src="user.png" alt="Accounts">
                    <p><strong>Total Accounts:</strong> <?php echo $total_accounts; ?></p>
                  <p>Total users of our website</p>
                </div>

                <div class="report-item">
                    <img src="folder.png" alt="Records">
                    <p><strong>Total Records:</strong> <?php echo $total_records; ?></p>
                    <p>Total data records inputted in our website</p>
                </div>

                <div class="report-item">
                    <img src="new-email.png" alt="Unread Messages">
                    <p><strong>Total Unread Messages:</strong> <?php echo $total_unread_messages; ?></p>
                    <p>Total unread messages in our websites! better check them out</p>
                </div>
            </div>

            <div class="report-container">
                <div class="report-item">
                    <img src="clock.png" alt="Pending Appointments">
                    <p><strong>Total Pending Appointments:</strong> <?php echo $total_pending_appointments; ?></p>

                </div>

                <div class="report-item">
                    <img src="clock.png" alt="Ongoing Appointments">
                    <p><strong>Total Ongoing Appointments:</strong> <?php echo $total_ongoing_appointments; ?></p>
                </div>

                <div class="report-item">
                    <img src="appoin.png" alt="Completed Appointments">
                    <p><strong>Total Completed Appointments:</strong> <?php echo $total_completed_appointments; ?></p>
                </div>
            </div>
        </div>
    </div>

    <?php include 'hcifooter.php'; ?>
</body>
</html>
