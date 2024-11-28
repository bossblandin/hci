<?php

$conn = new mysqli('localhost', 'root', '', 'logincemetery_db');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate input data
    $firstname = filter_var($_POST['firstname'], FILTER_SANITIZE_STRING);
    $lastname = filter_var($_POST['lastname'], FILTER_SANITIZE_STRING);
    $age = filter_var($_POST['age'], FILTER_VALIDATE_INT);
    $username = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);


    if ($age < 18) {
        echo "<script>
                alert('You must be at least 18 years old to register.');
                window.location.href = 'loginregister.php';
              </script>";
        exit;
    }

    $checkStmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $checkStmt->bind_param("s", $username);
    $checkStmt->execute();
    $checkStmt->store_result();

    if ($checkStmt->num_rows > 0) {
        echo "<script>
                alert('Username already exists. Please choose a different username.');
                window.location.href = 'loginregister.php';
              </script>";
        $checkStmt->close();
        exit;
    }

    $checkStmt->close();

    $stmt = $conn->prepare("INSERT INTO users (firstname, lastname, age, username, password) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssiss", $firstname, $lastname, $age, $username, $password);

    if ($stmt->execute()) {

        echo "<script>
                alert('Registration successful! You can now log in.');
                window.location.href = 'loginregister.php';
              </script>";
    } else {
        echo "<script>
                alert('Error: " . $stmt->error . "');
                window.location.href = 'loginregister.php';
              </script>";
    }

    $stmt->close();
}

$conn->close();
?>
