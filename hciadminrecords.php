<?php
// Start the session and check if the user is an admin
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: loginregister.php");  // Redirect if not admin
    exit();
}

if (isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    header("Location: loginregister.php");  // Redirect to login page after logout
    exit();
}

// Include the database connection file
include 'db_connection.php';

$success_message = "";

if (isset($_POST['add_record'])) {

    $last_name = $_POST['last_name'];
    $first_name = $_POST['first_name'];
    $middle_initial = $_POST['middle_initial'];
    $suffix = $_POST['suffix'];
    $date_of_burial = $_POST['date_of_burial'];
    $group = $_POST['group'];
    $group_number = $_POST['group_number'];
    $group_letter = $_POST['group_letter'];


    $sql = "INSERT INTO records (last_name, first_name, middle_initial, suffix, date_of_burial, group_name, group_number, group_letter)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    // Prepare statement
    if ($stmt = $conn->prepare($sql)) {
        // Bind parameters
        $stmt->bind_param("ssssssis", $last_name, $first_name, $middle_initial, $suffix, $date_of_burial, $group, $group_number, $group_letter);

        // Execute query
        if ($stmt->execute()) {
            $success_message = "Record added successfully!"; // Set success message
        } else {
            $success_message = "Error: " . $stmt->error; // Set error message
        }

        // Close statement
        $stmt->close();
    } else {
        $success_message = "Error: " . $conn->error; // Set error message
    }
}

// Fetch distinct groups
$group_query = "SELECT DISTINCT group_name FROM records ORDER BY group_name ASC";
$group_result = $conn->query($group_query);

// Handle group selection and fetch records for the selected group
$record_result = null; // Default value
if (isset($_GET['group'])) {
    $selected_group = $_GET['group'];

    // Fetch records for the selected group
    $record_query = "SELECT * FROM records WHERE group_name = ? ORDER BY id DESC";
    $stmt = $conn->prepare($record_query);
    $stmt->bind_param("s", $selected_group);
    $stmt->execute();
    $record_result = $stmt->get_result();
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
        display: flex;
        justify-content: center;
        align-items: flex-start;
        background-color: black;
        padding: 20px;
        flex-direction: column;
        gap: 20px;
        overflow: auto;
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

    .form-container {
        width: 100%;
        height: auto;
        background-color: #333;
        border: 1px solid #ccc;
        border-radius: 10px;
        padding: 20px;
        color: white;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .form-header {
        text-align: center;
        margin-bottom: 10px;
    }

    .form-content {
        display: flex;
        justify-content: space-between;
        gap: 20px;
        flex: 1;
    }

    .form-column {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .form-column label {
        font-size: 14px;
    }

    .form-column input {
        width: 100%;
        padding: 10px;
        border-radius: 5px;
        border: 1px solid #ccc;
        font-size: 14px;
    }

    .form-footer {
        text-align: center;
        margin-top: 10px;
    }

    .form-footer button {
        background-color: #444;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 20px;
        border: 1px solid white;
    }

    .form-footer button:hover {
      background-color: white;
      color: black;
    }

    .success-message {
        color: green;
        font-size: 16px;
        text-align: center;
        margin: 10px 0;
    }

    .group-list {
  margin-top: 20px;
  background-color: #333;
  padding: 20px;
  border-radius: 10px;
  color: white;
  display: flex; /* Use flexbox for horizontal layout */
  flex-wrap: wrap; /* Allow wrapping of items */
  justify-content: flex-start; /* Align items to the start */
}

.group-list h2 {
  text-align: center;
  margin-bottom: 10px;
  width: 100%;
}

.group-list ul {
  list-style-type: none;
  padding: 0;
  display: flex;
  gap: 10px; /* Spacing between items */
}

.group-list li {
  margin: 0;
}

.group-list a {
  color: white;
  text-decoration: none;
  padding: 10px;
  background-color: #444;
  border-radius: 5px;
  text-align: center;
  display: inline-block; /* Make it inline for horizontal layout */
}

.group-list a:hover {
  background-color: #555;
}

    .record-list {
        background-color: #333;
        padding: 20px;
        border-radius: 10px;
        color: white;
        display: block;
        position: relative;
    }

    .record-list h2 {
        text-align: center;
        margin-bottom: 20px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
    }

    table th, table td {
        padding: 10px;
        text-align: center;
        border: 1px solid white;
    }

    table th {
        background-color: #444;
        color: white;
    }

    table tbody tr:nth-child(odd) {
        background-color: #555;
    }

    table tbody tr:nth-child(even) {
        background-color: #666;
    }

    .close-btn {
        position: absolute;
        top: 10px;
        right: 10px;
        background-color: black;
        color: white;
        border: 1px solid white;
        padding: 10px;
        cursor: pointer;
        font-size: 10px;
        border-radius: 50%;
    }
    .close-btn:hover{
      background-color: white;
      color: black;
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

        <!-- Main Content -->
        <div class="main-content">
            <div class="form-container">
                <div class="form-header">
                    <h2>Add Cemetery Record</h2>
                </div>

                <?php if (!empty($success_message)): ?>
                    <div class="success-message"><?= $success_message; ?></div>
                <?php endif; ?>

                <form method = "post">
                <div class="form-content">
                    <div class="form-column">
                        <label for="last_name">Last Name:</label>
                        <input type="text" id="last_name" name="last_name" required>

                        <label for="first_name">First Name:</label>
                        <input type="text" id="first_name" name="first_name" required>

                        <label for="middle_initial">Middle Initial:</label>
                        <input type="text" id="middle_initial" name="middle_initial">

                        <label for="suffix">Suffix:</label>
                        <input type="text" id="suffix" name="suffix">
                    </div>

                    <div class="form-column">
                        <label for="date_of_burial">Date of Burial:</label>
                        <input type="date" id="date_of_burial" name="date_of_burial" required>

                        <label for="group">Group:</label>
                        <input type="text" id="group" name="group" required>

                        <label for="group_number">Group Number:</label>
                        <input type="text" id="group_number" name="group_number" required>

                        <label for="group_letter">Group Letter:</label>
                        <input type="text" id="group_letter" name="group_letter" required>
                    </div>
                </div>

                <div class="form-footer">
                    <button type="submit" name="add_record">Add Record</button>
                </div>
              </form>
            </div>

            <!-- Group List -->
            <div class="group-list">
                <h2>Group List</h2>
                <ul>
                    <?php while ($row = $group_result->fetch_assoc()): ?>
                        <li><a href="?group=<?= htmlspecialchars($row['group_name']); ?>"><?= htmlspecialchars($row['group_name']); ?></a></li>
                    <?php endwhile; ?>
                </ul>
            </div>

            <!-- Record List -->
            <?php if ($record_result): ?>
                <div class="record-list">
                    <h2>Records in Group: <?= htmlspecialchars($selected_group); ?></h2>
                    <button class="close-btn" onclick="document.querySelector('.record-list').style.display='none'">X</button>
                    <table>
                        <thead>
                            <tr>
                                <th>Last Name</th>
                                <th>First Name</th>
                                <th>Middle Initial</th>
                                <th>Suffix</th>
                                <th>Date of Burial</th>
                                <th>Group</th>
                                <th>Group Number</th>
                                <th>Group Letter</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $record_result->fetch_assoc()): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['last_name']); ?></td>
                                    <td><?= htmlspecialchars($row['first_name']); ?></td>
                                    <td><?= htmlspecialchars($row['middle_initial']); ?></td>
                                    <td><?= htmlspecialchars($row['suffix']); ?></td>
                                    <td><?= htmlspecialchars($row['date_of_burial']); ?></td>
                                    <td><?= htmlspecialchars($row['group_name']); ?></td>
                                    <td><?= htmlspecialchars($row['group_number']); ?></td>
                                    <td><?= htmlspecialchars($row['group_letter']); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // JavaScript to handle toggling of the record list visibility
        document.querySelector('.close-btn').addEventListener('click', function() {
            document.querySelector('.record-list').style.display = 'none';
        });
        // Auto-remove success message after 3 seconds
window.addEventListener('DOMContentLoaded', (event) => {
    const successMessage = document.querySelector('.success-message');
    if (successMessage) {
        setTimeout(() => {
            successMessage.style.display = 'none'; // Hide the success message
        }, 3000); // 3000ms = 3 seconds
    }
});

    </script>

    <?php include 'hcifooter.php'; ?>


</body>
</html>
