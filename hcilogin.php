<?php
session_start();

$conn = new mysqli('localhost', 'root', '', 'logincemetery_db');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, password, role FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $hashed_password, $role);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $role;

            if ($role === 'admin') {
                echo "<script>
                        alert('Successfully logged in as Admin!');
                        window.location.href = 'hciadmin.php';
                      </script>";
            } else {
                echo "<script>
                        alert('Successfully logged in!');
                        window.location.href = 'homepage.php';
                      </script>";
            }
            exit();
        } else {
            echo "<script>
                    alert('Invalid username or password. Please try again.');
                    window.location.href = 'loginregister.php';
                  </script>";
        }
    } else {
        echo "<script>
                alert('Invalid username or password. Please try again.');
                window.location.href = 'loginregister.php';
              </script>";
    }

    $stmt->close();
}

$conn->close();
?>
