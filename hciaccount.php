<?php
session_start();

$conn = new mysqli('localhost', 'root', '', 'logincemetery_db');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['username'])) {
    header("Location: loginregister.php");
    exit();
}

$username = $_SESSION['username'];

$query = "SELECT * FROM users WHERE username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Calculate age from birthday
$birthdate = new DateTime($user['birthday']);
$today = new DateTime();
$age = $today->diff($birthdate)->y;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_info'])) {
    // Collect the updated data from the form
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $street = $_POST['street'];
    $barangay = $_POST['barangay'];
    $city = $_POST['city'];
    $birthday = $_POST['birthday'];
    $contact_number = $_POST['contact_number'];


    $street = $street ?: 'Street not provided';
    $barangay = $barangay ?: 'Barangay not provided';
    $city = $city ?: 'City not provided';

    // Prepare the SQL update query
    $update_query = "UPDATE users SET firstname = ?, lastname = ?, street = ?, barangay = ?, city = ?, birthday = ?, contact_number = ? WHERE username = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("ssssssss", $firstname, $lastname, $street, $barangay, $city, $birthday, $contact_number, $username);

    // Execute the update query and redirect on success
    if ($stmt->execute()) {
        header("Location: hciaccount.php");
        exit();
    } else {
        echo "Error updating information: " . $stmt->error;
    }
}

if (isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    header("Location: loginregister.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Dashboard</title>
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
            height: 800px;
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

        .content {
            flex: 1;
            padding: 30px; /* Increased padding */
            background-color: black;
            overflow-y: auto;
        }

        .content h1 {
            color: #f5f5f5;
            font-weight: bolder;
            margin-bottom: 30px; /* Increased margin */
            font-size: 45px; /* Increased font size */

        }

        .info-box {
            background: black;
            padding: 50px; /* Increased padding for a bigger box */
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px; /* Increased margin between sections */
            font-size: 25px; /* Increased font size for better readability */
            border: 5px solid white; /* Added black border */
        }

        .info-box p {
            margin-bottom: 20px; /* Increased margin between fields for better spacing */
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
            border: 1px solid white;
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

        #update-btn {
            padding: 18px 35px; /* Increased padding for the button */
            font-size: 22px; /* Increased font size for the update button */
            cursor: pointer;
            background-color: black;
            color: white;
            border: 1px solid white;
            border-radius: 10px;
            transition: background-color 0.3s ease;
        }

        #update-btn:hover {
            background-color: white;
            color: black;
        }

        #update-form {
            display: none;
        }
    </style>
    <script>
        function toggleUpdateForm() {
            const updateForm = document.getElementById('update-form');
            const personalInfo = document.getElementById('personal-info');
            const updateButton = document.getElementById('update-btn');

            if (updateForm.style.display === 'none') {
                updateForm.style.display = 'block';
                personalInfo.style.display = 'none';
                updateButton.style.display = 'none';
            } else {
                updateForm.style.display = 'none';
                personalInfo.style.display = 'block';
                updateButton.style.display = 'block';
            }
        }
    </script>
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

        <div class="content">
            <div id="personal-info" class="info-box">
                <h1>Personal Information</h1>
                <p><strong>Full Name:</strong> <?php echo htmlspecialchars($user['firstname']) . ' ' . htmlspecialchars($user['lastname']); ?></p>
                <p><strong>Age:</strong> <?php echo $age; ?> years old</p>
                <p><strong>Address:</strong> <?php echo $user['street'] . ' ' . $user['barangay'] . ' ' . $user['city'] ?: 'Not provided'; ?></p>
                <p><strong>Birthday:</strong> <?php echo $user['birthday'] ?: 'Not provided'; ?></p>
                <p><strong>Contact Number:</strong> <?php echo $user['contact_number'] ?: 'Not provided'; ?></p>
                <button id="update-btn" onclick="toggleUpdateForm()">Update Information</button>
            </div>

            <form id="update-form" method="post" class="info-box">
                <h2>Update Personal Information</h2>
                <input type="text" name="firstname" value="<?php echo htmlspecialchars($user['firstname']); ?>" placeholder="First Name" required>
                <input type="text" name="lastname" value="<?php echo htmlspecialchars($user['lastname']); ?>" placeholder="Last Name" required>
                <input type="text" name="street" value="<?php echo htmlspecialchars($user['street']); ?>" placeholder="House Number, Street" required>
                <input type="text" name="barangay" value="<?php echo htmlspecialchars($user['barangay']); ?>" placeholder="Barangay" required>
                <input type="text" name="city" value="<?php echo htmlspecialchars($user['city']); ?>" placeholder="City" required>
                <input type="date" name="birthday" value="<?php echo $user['birthday']; ?>" required>
                <input type="text" name="contact_number" value="<?php echo htmlspecialchars($user['contact_number']); ?>" placeholder="Contact Number" required>
                <button type="submit" name="update_info">Update Information</button>
                <button type="button" onclick="toggleUpdateForm()">Cancel</button>
            </form>
        </div>
    </div>

    <?php include 'hcifooter.php'; ?>
</body>
</html>
