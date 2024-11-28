<?php
session_start();

$conn = new mysqli('localhost', 'root', '', 'logincemetery_db');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the admin is logged in
if (!isset($_SESSION['username'])) {
    header("Location: loginregister.php");
    exit();
}

// Admin username from the session
$username = $_SESSION['username'];

// Fetch all unique users who have sent messages along with their full name and unread message count
$users_query = "
    SELECT DISTINCT m.sender, u.firstname, u.lastname,
           COUNT(CASE WHEN m.receiver = 'admin' AND m.status = 'unread' THEN 1 END) AS unread_count
    FROM messages m
    JOIN users u ON m.sender = u.username
    WHERE m.sender != 'admin'
    GROUP BY m.sender, u.firstname, u.lastname
";
$users_result = $conn->query($users_query);

// Get the selected user for chat
$selected_user = $_GET['user'] ?? null;

// Fetch chat messages with the selected user
$messages = [];
if ($selected_user) {
    $stmt = $conn->prepare("SELECT * FROM messages WHERE (sender = ? AND receiver = 'admin') OR (sender = 'admin' AND receiver = ?) ORDER BY timestamp ASC");
    $stmt->bind_param("ss", $selected_user, $selected_user);
    $stmt->execute();
    $result = $stmt->get_result();
    $messages = $result->fetch_all(MYSQLI_ASSOC);

    // Mark messages as read
    $stmt = $conn->prepare("UPDATE messages SET status = 'read' WHERE receiver = 'admin' AND sender = ?");
    $stmt->bind_param("s", $selected_user);
    $stmt->execute();
}

// Handle sending a message
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['send_message']) && $selected_user) {
    $message = $_POST['message'];
    if (!empty($message)) {
        $stmt = $conn->prepare("INSERT INTO messages (sender, receiver, message, status) VALUES ('admin', ?, ?, 'unread')");
        $stmt->bind_param("ss", $selected_user, $message);
        $stmt->execute();
        header("Location: hciadminmessenger.php?user=" . urlencode($selected_user));
        exit();
    }
}

// Handle back button
if (isset($_POST['back'])) {
    header("Location: hciadmin.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Messenger</title>
    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        display: flex;
        flex-direction: column;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #f4f4f4;
        color: #f5f5f5;
        height: 100vh;
    }

    .container {
        display: flex;
        flex: 1;
        height: 800px;
        justify-content: space-between;
    }

    .sidebar {
        width: 350px;
        background-color: #333;
        color: white;
        padding: 30px;
        height: 100%;
        border-right: 1px solid #444;
        overflow-y: auto;
    }

    .sidebar h2 {
        text-align: center;
        margin-bottom: 30px;
        font-size: 24px;
        font-weight: bold;
    }

    .sidebar a, .back-btn {
        display: block;
        text-decoration: none;
        color: white;
        padding: 12px;
        margin: 12px 0;
        border-radius: 5px;
        cursor: pointer;
        font-size: 18px;
        border: 1px solid white;
        text-align: center;
        transition: background-color 0.3s, color 0.3s;
    }

    .sidebar a:hover, .back-btn:hover {
        background-color: #444;
        color: #fff;
    }

    .back-btn {
        margin-top: 50px;
        background-color: #333;
        color: white;
        font-size: 20px;
        font-weight: bold;
    }

    .content {
        flex: 1;
        padding: 20px;
        background-color: #222;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .content h1 {
        margin-bottom: 30px;
        color: white;
        font-size: 26px;
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

    .message.admin {
        align-self: flex-end;
        background-color: #0056b3;
        color: white;
        text-align: right;
    }

    .message.user {
        align-self: flex-start;
        background-color: #28a745;
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
        background-color:#007bff;
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

    .unread-count {
        background-color: red;
        color: white;
        font-size: 14px;
        font-weight: bold;
        padding: 5px;
        border-radius: 50%;
        margin-left: 10px;
        position: relative;
        top: -5px;
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
    <?php include 'hciadminheader.php'; ?>

    <div class="container">
        <div class="sidebar">
            <h2>Users</h2>
            <?php while ($user = $users_result->fetch_assoc()): ?>
                <a href="hciadminmessenger.php?user=<?php echo urlencode($user['sender']); ?>">
                    <?php echo htmlspecialchars($user['firstname'] . " " . $user['lastname']); ?>
                    <?php if ($user['unread_count'] > 0): ?>
                        <span class="unread-count"><?php echo $user['unread_count']; ?></span>
                    <?php endif; ?>
                </a>
            <?php endwhile; ?>
            <form method="post">
                <button type="submit" name="back" class="back-btn">Back</button>
            </form>
        </div>

        <div class="content">
            <h1>Chat with <?php echo $selected_user ? htmlspecialchars($selected_user) : "Select a User"; ?></h1>
            <div class="messages">
                <?php if ($selected_user): ?>
                    <?php foreach ($messages as $message): ?>
                        <div class="message <?php echo $message['sender'] === 'admin' ? 'admin' : 'user'; ?>">
                            <p><?php echo htmlspecialchars($message['message']); ?></p>
                            <p><small><?php echo $message['timestamp']; ?></small></p>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No user selected or no messages to display.</p>
                <?php endif; ?>
            </div>
            <?php if ($selected_user): ?>
                <form method="post">
                    <textarea name="message" rows="2" placeholder="Type your message..."></textarea>
                    <button type="submit" name="send_message">Send</button>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <?php include 'hcifooter.php'; ?>
</body>
</html>
