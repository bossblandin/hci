<?php
session_start();

// Redirect if not admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: loginregister.php");
    exit();
}

// Logout functionality
if (isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    header("Location: loginregister.php");
    exit();
}

// Database connection
$servername = "localhost"; // Change if hosted elsewhere
$username = "root";        // Your database username
$password = "";            // Your database password
$dbname = "logincemetery_db"; // Your database name

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle announcement submission
$successMessage = $errorMessage = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title'], $_POST['content'])) {
    $title = $conn->real_escape_string(trim($_POST['title']));
    $content = $conn->real_escape_string(trim($_POST['content']));

    if (!empty($title) && !empty($content)) {
        $sql = "INSERT INTO announcements (title, content) VALUES ('$title', '$content')";
        if ($conn->query($sql) === TRUE) {
            $successMessage = "Announcement successfully posted!";
        } else {
            $errorMessage = "Error posting announcement: " . $conn->error;
        }
    } else {
        $errorMessage = "Both title and content are required.";
    }
}

// Handle announcement deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'], $_POST['announcement_id'])) {
    $announcementId = intval($_POST['announcement_id']);  // Ensure it's an integer

    // Delete the announcement from the database
    $sql = "DELETE FROM announcements WHERE id = $announcementId";
    if ($conn->query($sql) === TRUE) {
        $successMessage = "Announcement successfully deleted!";
    } else {
        $errorMessage = "Error deleting announcement: " . $conn->error;
    }
}

// Retrieve posted announcements
$announcements = [];
$sql = "SELECT id, title, content, date_posted FROM announcements ORDER BY date_posted DESC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $announcements[] = $row;
    }
}

$conn->close();
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

    .logout-btn {
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
        display: flex; /* Enable flexbox layout */
        justify-content: space-between; /* Distribute items with space between */
        padding: 30px;
        background-color: black;
        overflow-y: auto;
    }

    .announcement-form {
        background-color: #222;
        width: 48%; /* Allocate 48% of the space for the form */
        height: 650px;
        padding: 20px;
        border-radius: 10px;
        border: 1px solid white;
    }

    .announcement-form h2 {
        margin-bottom: 30px;
        font-size: 1.5rem;
        color: white;
    }

    .announcement-form input, .announcement-form textarea {
        width: 100%;
        padding: 10px;
        margin-bottom: 30px;
        font-size: 30px;
        color: black;
        background-color: white;
        border: 1px solid white;
        border-radius: 5px;
    }

    .announcement-form button {
        background-color: white;
        color: black;
        padding: 10px 20px;
        border: none;
        font-size: 30px;
        cursor: pointer;
        border-radius: 5px;
    }

    .announcement-form button:hover {
        background-color: black;
        color: white;
        border: 1px solid white;
    }

    .message {
        margin: 10px 0;
        padding: 10px;
        border-radius: 5px;
    }

    .success {
        background-color: #4CAF50;
        color: white;
    }

    .error {
        background-color: #f44336;
        color: white;
    }

    .announcement-list {
        background-color: #222;
        width: 48%; /* Allocate 48% of the space for the list */
        padding: 20px;
        border-radius: 10px;
        border: 1px solid white;
        overflow-y: auto;
        height: 650px;
    }

    .announcement-list h2 {
        margin-bottom: 30px;
        font-size: 1.5rem;
        color: white;
    }

    .announcement-item {
        background-color: #333;
        color: white;
        padding: 20px;
        margin-bottom: 20px;
        border-radius: 10px;
    }

    .announcement-item h3 {
        font-size: 1.5rem;
        margin-bottom: 10px;
    }

    .announcement-item p {
        font-size: 1rem;
        line-height: 1.5;
    }

    .delete-btn {
        background-color: #f44336;
        color: white;
        padding: 5px 10px;
        border: none;
        font-size: 16px;
        cursor: pointer;
        border-radius: 5px;
    }

    .delete-btn:hover {
        background-color: #d32f2f;
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
            <div class="announcement-form">
                <h2>Post an Announcement</h2>
                <?php if (!empty($successMessage)): ?>
                    <div class="message success" id="successMessage"><?= $successMessage; ?></div>
                <?php elseif (!empty($errorMessage)): ?>
                    <div class="message error"><?= $errorMessage; ?></div>
                <?php endif; ?>
                <form method="post">
                    <input type="text" name="title" placeholder="Enter announcement title" required>
                    <textarea name="content" placeholder="Enter announcement content" required></textarea>
                    <button type="submit">Post Announcement</button>
                </form>
            </div>

            <div class="announcement-list">
                <h2>Posted Announcements</h2>
                <?php if (empty($announcements)): ?>
                    <p>No announcements posted yet.</p>
                <?php else: ?>
                    <?php foreach ($announcements as $announcement): ?>
                        <div class="announcement-item">
                            <h3><?= htmlspecialchars($announcement['title']); ?></h3>
                            <p><?= htmlspecialchars($announcement['content']); ?></p>
                            <p><em>Posted on: <?= date('F j, Y, g:i a', strtotime($announcement['date_posted'])); ?></em></p>
                            <form method="post" style="display: inline;">
                                <input type="hidden" name="announcement_id" value="<?= $announcement['id']; ?>">
                                <button type="submit" name="delete" class="delete-btn">Delete</button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php include 'hcifooter.php'; ?>
</body>
</html>
