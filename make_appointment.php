<?php
session_start();
include 'db_connection.php'; // Include the database connection file
include 'hciheader.php'; // Include the header

if (!isset($_SESSION['username'])) {
    header("Location: loginregister.php");
    exit();
}

$service_id = $_POST['service_id'] ?? null;

if (!$service_id) {
    header("Location: hciservices.php"); // Redirect to services page if no service ID
    exit();
}

// Initialize variables
$successMessage = "";
$errorMessage = "";

$service_id = $_POST['service_id'] ?? null;

if (!$service_id) {
    $errorMessage = "Invalid service selection.";
}
$service_name = ""; // Initialize the variable

// Query to get the service name from the database
if ($service_id) {
    $query = "SELECT service_name FROM services WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'i', $service_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $service_name);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
}

// Process the form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($service_id)) {
    // Check if form fields are set, else set them to empty strings
    $appointment_date = $_POST['appointment_date'] ?? '';
    $appointment_time = $_POST['appointment_time'] ?? '';
    $name_of_deceased = $_POST['name_of_deceased'] ?? '';
    $date_of_burial = $_POST['date_of_burial'] ?? '';
    $purpose = $_POST['purpose'] ?? '';

    // Check for required fields
    if (empty($appointment_date) || empty($appointment_time) || empty($purpose)) {
        $errorMessage = "Please fill in all required fields.";
    } else {
        // Insert into the database
        $insertQuery = "INSERT INTO appointments (service_id, appointment_date, appointment_time, name_of_deceased, date_of_burial, purpose, username)
                        VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $insertQuery);

        // Bind the parameters
        mysqli_stmt_bind_param($stmt, 'issssss', $service_id, $appointment_date, $appointment_time, $name_of_deceased, $date_of_burial, $purpose, $_SESSION['username']);

        // Execute the query and check if it's successful
        if (mysqli_stmt_execute($stmt)) {
            $successMessage = "Appointment successfully made for the service: " . htmlspecialchars($service_name);

            // Redirect after a successful message is displayed
            echo "<script>
                    alert('$successMessage');
                    window.location.href = 'hciservices.php';
                  </script>";
        } else {
            $errorMessage = "There was an error making the appointment. Please try again.";
        }

        // Close the statement
        mysqli_stmt_close($stmt);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Make an Appointment</title>
    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: Arial, sans-serif;
    }
    body {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-height: 100vh;
        background-color: #000;
        color: #f5f5f5;
    }
    header {
        text-align: center;
        background-color: #111;
        padding: 20px 0;
        color: #f5f5f5;
        border-bottom: 1px solid #444;
    }
    .header-container {
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    .nav-buttons {
        display: flex;
        gap: 15px;
        margin-top: 10px;
    }
    .nav-buttons .btn {
        padding: 10px 20px;
        background-color: transparent;
        border: 1px solid #f5f5f5;
        color: #f5f5f5;
        text-decoration: none;
        border-radius: 5px;
        transition: background 0.3s ease, color 0.3s ease;
    }
    .nav-buttons .btn:hover {
        background-color: #f5f5f5;
        color: #000;
    }
    footer {
        text-align: center;
        background-color: #111;
        padding: 20px 0;
        color: #f5f5f5;
        margin-top: 20px;
        border-top: 1px solid #444;
    }
    .social-icons {
        margin-top: 10px;
        display: flex;
        justify-content: center;
        gap: 15px;
    }
    .social-icons img {
        width: 30px;
        height: 30px;
        transition: transform 0.3s ease;
    }
    .social-icons img:hover {
        transform: scale(1.2);
    }
    .main-content {
        flex: 1;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 20px;
    }
    .appointment-form {
        background-color: #111;
        padding: 20px;
        border-radius: 8px;
        width: 800px;
        border: 2px solid white;
    }
    .appointment-form h3 {
        text-align: center;
        font-size: 2em;
        font-weight: bold;
        color: #e0e0e0;
        margin-bottom: 20px;
    }
    .appointment-form label {
        display: block;
        margin-bottom: 5px;
        color: #ccc;
        font-size: 1.2em;
        font-weight: bold;
    }
    .appointment-form input,
    .appointment-form textarea,
    .appointment-form button {
        width: 100%;
        margin-bottom: 30px;
        padding: 10px;
        border: 1px solid #444;
        border-radius: 5px;
        background: #222;
        color: white;
    }
    .appointment-form button {
        background-color: #007bff;
        cursor: pointer;
        font-size: 1.5em;
    }
    .appointment-form button:hover {
        background-color: #0056b3;
    }
    .appointment-form textarea {
        resize: vertical;
        height: 100px;
    }
    </style>
</head>
<body>

<div class="main-content">
    <div class="appointment-form">
        <h3>Make an Appointment for: <?php echo htmlspecialchars($service_name); ?></h3>

        <?php if ($successMessage): ?>
            <p style="color: green; font-weight: bold;"><?php echo $successMessage; ?></p>
        <?php elseif ($errorMessage): ?>
            <p style="color: red; font-weight: bold;"><?php echo $errorMessage; ?></p>
        <?php endif; ?>

        <form method="POST">
            <input type="hidden" name="service_id" value="<?php echo htmlspecialchars($service_id); ?>">

            <label for="appointment_date">Appointment Date</label>
            <input type="date" id="appointment_date" name="appointment_date" required value="<?php echo htmlspecialchars($appointment_date ?? ''); ?>">

            <label for="appointment_time">Appointment Time</label>
            <input type="time" id="appointment_time" name="appointment_time" required value="<?php echo htmlspecialchars($appointment_time ?? ''); ?>">

            <label for="name_of_deceased">Name of Deceased (Optional)</label>
            <input type="text" id="name_of_deceased" name="name_of_deceased" placeholder="Enter the name of the deceased" value="<?php echo htmlspecialchars($name_of_deceased ?? ''); ?>">

            <label for="date_of_burial">Date of Burial (Optional)</label>
            <input type="date" id="date_of_burial" name="date_of_burial" value="<?php echo htmlspecialchars($date_of_burial ?? ''); ?>">

            <label for="purpose">Purpose / Additional Information</label>
            <textarea id="purpose" name="purpose" placeholder="Enter purpose or additional details" required><?php echo htmlspecialchars($purpose ?? ''); ?></textarea>

            <button type="submit">Confirm Appointment</button>
        </form>
    </div>
</div>

<?php include 'hcifooter.php'; ?> <!-- Include the footer -->

</body>
</html>
