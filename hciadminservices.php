<?php
session_start();

// Check if the user is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: loginregister.php"); // Redirect if not admin
    exit();
}

// Logout functionality
if (isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    header("Location: loginregister.php");
    exit();
}

// Handle service deletion
if (isset($_GET['delete_service_id'])) {
    $delete_service_id = $_GET['delete_service_id'];

    // Connect to database
    $conn = new mysqli("localhost", "root", "", "logincemetery_db");

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Delete the service
    $sql = "DELETE FROM services WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $delete_service_id);
    if ($stmt->execute()) {
        echo "<p style='color: lightgreen; text-align: center;'>Service deleted successfully!</p>";
    } else {
        echo "<p style='color: red; text-align: center;'>Error: " . $conn->error . "</p>";
    }
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Muntinlupa Public Cemetery Admin - Appointments</title>
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
        padding: 15px; /* Reduced padding */
        height: 100%;
        border-right: 1px solid white;
        display: flex;
        flex-direction: column;
        justify-content: flex-start; /* Make sure content starts from the top */
    }

    .sidebar h2 {
        text-align: center;
        margin: 30px; /* Increased margin */
        font-size: 40px; /* Increased font size */
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
        margin-bottom: 10px; /* Add some space at the bottom of the form */
    }

    .sidebar a{
        margin-bottom: 30px;
    }

    .sidebar a:hover, .logout-btn:hover {
        background-color: white;
        color: black;
    }

    .main-content {
        flex: 1;
        padding: 30px; /* Increased padding */
        background-color: black;
        overflow-y: auto;
    }

    .logout-btn .add_service {
        background-color: black;
        color: white;
        font-size: 25px; /* Increased font size for button */
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
    .main-content {
        flex: 1;
        padding: 30px;
        background-color: black;
        overflow-y: auto;
    }

        .service-section {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            height: auto;
            flex-wrap: wrap;
        }

        .input-form, .output-list {
            width: 48%;
            height: 650px; /* Set fixed height */
            padding: 20px;
            border-radius: 10px;
            border: 1px solid white;
            background-color: #222;
            color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            overflow-y: auto; /* Enable scrolling if content overflows */
        }

        .input-form h3, .output-list h3 {
            text-align: center;
            margin-bottom: 20px;
        }

        .input-form input, .input-form textarea, .input-form button {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            border: none;
            font-size: 20px;
        }

        .input-form button {
            background-color: #444;
            color: white;
            border: 1px solid white;
        }
        .input-form button:hover{
            background-color: white;
            color: black;
            border: 1px solid white;
        }
        .output-list div {
            margin-bottom: 15px;
            padding: 10px;
            background-color: #333;
            border-radius: 5px;
        }

        .output-list h4 {
            font-size: 25px;
            margin-bottom: 20px;
        }
        .output-list p {
            font-size: 25px;
            margin-bottom: 20px;
        }

        /* Style for success message */
        .success-message {
            background-color: lightgreen;
            color: black;
            padding: 10px;
            margin: 20px 0;
            border-radius: 5px;
            text-align: center;
            font-weight: bold;
            display: none;
        }

        /* Style for the delete button */
        .delete-btn {
            display: inline-block;
            padding: 10px;
            background-color: red;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .delete-btn:hover {
            background-color: darkred;
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
            <div class="service-section">
                <!-- Left: Add a service -->
                <div class="input-form">
                    <h3>Add a Service</h3>
                    <form method="post" action="">
                        <input type="text" name="service" placeholder="Service Name" required>
                        <textarea name="description" rows="4" placeholder="Description" required></textarea>
                        <input type="text" name="price" placeholder="Price Range" required>
                        <button type="submit" name="add_service">Add Service</button>
                    </form>
                </div>

                <!-- Right: Display services -->
                <div class="output-list">
                    <h3>Posted Services</h3>
                    <?php
                    // Connect to database
                    $conn = new mysqli("localhost", "root", "", "logincemetery_db");

                    // Check connection
                    if ($conn->connect_error) {
                        die("Connection failed: " . $conn->connect_error);
                    }

                    // Handle form submission for adding services
                    if (isset($_POST['add_service'])) {
                        $service = $conn->real_escape_string($_POST['service']);
                        $description = $conn->real_escape_string($_POST['description']);
                        $price = $conn->real_escape_string($_POST['price']);

                        $sql = "INSERT INTO services (service_name, description, price_range) VALUES ('$service', '$description', '$price')";
                        if ($conn->query($sql) === TRUE) {
                            echo "<div class='success-message' id='successMessage'>Service added successfully!</div>";
                        } else {
                            echo "<p style='color: red; text-align: center;'>Error: " . $conn->error . "</p>";
                        }
                    }

                    // Fetch and display services
                    $result = $conn->query("SELECT id, service_name, description, price_range FROM services");
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<div>
                                    <h4>" . htmlspecialchars($row['service_name']) . "</h4>
                                    <p>" . htmlspecialchars($row['description']) . "</p>
                                    <p><strong>Price Range:</strong> " . htmlspecialchars($row['price_range']) . "</p>
                                    <a href='hciadminservices.php?delete_service_id=" . $row['id'] . "' class='delete-btn' onclick='return confirm(\"Are you sure you want to delete this service?\")'>Delete</a>
                                  </div>";
                        }
                    } else {
                        echo "<p style='text-align: center;'>No services posted yet.</p>";
                    }

                    $conn->close();
                    ?>
                </div>
            </div>
        </div>
    </div>

    <?php include 'hcifooter.php'; ?>

    <script>
        // JavaScript to automatically hide the success message after 3 seconds
        window.onload = function() {
            var successMessage = document.getElementById('successMessage');
            if (successMessage) {
                setTimeout(function() {
                    successMessage.style.display = 'none';
                }, 3000); // Hide after 3 seconds
            }
        }

    </script>
</body>
</html>
