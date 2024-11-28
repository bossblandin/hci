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

// Get messages for the logged-in user
$query = "SELECT * FROM messages WHERE (sender = ? AND receiver = 'admin') OR (sender = 'admin' AND receiver = ?) ORDER BY timestamp ASC";
$stmt = $conn->prepare($query);
$stmt->bind_param("ss", $username, $username);
$stmt->execute();
$result = $stmt->get_result();

// Handle sending a message
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['send_message'])) {
    $message = $_POST['message'];
    if (!empty($message)) {
        $insert_query = "INSERT INTO messages (sender, receiver, message) VALUES (?, 'admin', ?)";
        $stmt = $conn->prepare($insert_query);
        $stmt->bind_param("ss", $username, $message);
        $stmt->execute();
        header("Location: hcimessenger.php"); // Refresh to show the new message
        exit();
    }
}

// Handle logout
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
    <title>Messenger</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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
        .logout-btn{
            background-color: black;
            color: white;
            font-size: 20px; /* Increased font size for button */
            cursor: pointer;
            font-weight: bold;
            margin-top: 300px;
        }

        .content {
            flex: 1;
            padding: 30px;
            background-color: #222;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
        }

        .content h1 {
            margin-bottom: 30px;
            color: white;
            font-size: 28px;
            font-weight: bold;
            text-align: center;
        }

        .messages {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
            background-color: #333;
            border-radius: 10px;
            border: 1px solid #444;
            margin-bottom: 20px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .message {
            max-width: 70%;
            padding: 12px;
            border-radius: 20px;
            margin-bottom: 10px;
            font-size: 16px;
            line-height: 1.5;
        }

        .message.user {
            align-self: flex-end;
            background-color: #28a745;
            color: white;
            text-align: right;
        }

        .message.admin {
            align-self: flex-start;
            background-color: #0056b3;
            color: white;
            text-align: left;
        }

        .message p {
            margin: 0;
            font-size: 16px;
        }

        .message small {
            display: block;
            margin-top: 5px;
            font-size: 12px;
            color: #ccc;
        }

        form textarea, form button {
            display: block;
            width: 100%;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 20px;
            border: 1px solid #444;
            font-size: 16px;
            background-color: #222;
            color: white;
        }

        form button {
            background-color: #28a745;
            color: white;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        form button:hover {
            background-color: #218838;
        }

        form textarea {
            resize: none;
            height: 60px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

    </style>


    <script>
        // Automatically scroll the chat to the bottom
        document.addEventListener("DOMContentLoaded", function () {
            const chatContainer = document.querySelector('.messages');
            if (chatContainer) {
                chatContainer.scrollTop = chatContainer.scrollHeight;
            }
        });
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
            <h1>Chat with Admin</h1>
            <div class="messages">
                <?php while ($message = $result->fetch_assoc()): ?>
                    <div class="message <?php echo $message['sender'] == $username ? 'user' : 'admin'; ?>">
                        <p><?php echo htmlspecialchars($message['message']); ?></p>
                        <p><small><?php echo $message['timestamp']; ?></small></p>
                    </div>
                <?php endwhile; ?>
            </div>

            <form method="post">
                <textarea name="message" rows="2" placeholder="Type your message..."></textarea>
                <button type="submit" name="send_message">Send</button>
            </form>
        </div>
    </div>

    <?php include 'hcifooter.php'; ?>
</body>
</html>
