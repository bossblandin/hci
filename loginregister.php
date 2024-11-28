<?php
include 'hciheader.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Muntinlupa Public Cemetery</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600;700;800;900&family=Poppins:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: Arial, sans-serif;
    }

    body {
        background-color: #000;
        color: #f5f5f5;
        line-height: 1.5;
    }
    section {
        padding: 85px 18% 85px;
    }
    .home {
        position: relative;
        width: 100%;
        height: 750px;
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        align-items: center;
        gap: 6rem;
    }
    .home-text h1 {
        color: #ffffff;
        text-shadow: 0px 0px 3px #888888;
        font-size: 5rem;
        font-weight: 900;
        line-height: 1.2;
        margin-bottom: 2rem;
        font-family: 'Playfair Display', serif;
    }
    .home-text p {
        max-width: 500px;
        font-size: 1.5rem;
        line-height: 32px;
        color: #fff2de;
        margin-bottom: 3rem;
    }
    .form-container {
        display: flex;
        gap: 2rem;
    }
    .form-box {
       background-color: #333;
       padding: 30px;
       border-radius: 10px;
       width: 600px;
       border: 1px solid white;
    }
    .form-box h2 {
        color: #fff;
        margin-bottom: 20px;
        font-size: 2rem;
        font-weight: 600;
    }
    .form-box input {
      width: 100%;
      padding: 15px;
      margin: 15px 0;
      border: none;
      border-radius: 10px;
      font-size: 1.2rem;
    }
    .form-box button {
      width: 100%;
      padding: 15px;
      background-color: #444;
      color: #fff;
      border: none;
      border-radius: 10px;
      cursor: pointer;
      font-size: 1.2rem;
      border: 1px solid white;
    }
    .form-box button:hover {
      background-color: white;
      color: black;
    }
    .toggle-btn {
        margin-top: 15px;
        text-align: center;
        color: #fff;
        cursor: pointer;
    }
    .toggle-btn:hover {
        background-color: white;
        color: black;
        }
    </style>
</head>
<body>

<section class="home" id="home">
    <div class="home-text">
        <h1>LIBINGAN PANG LUNGSOD NG MUNTINLUPA</h1>
        <p>Welcome to the web-based system of Muntinlupa Public Cemetery.</p>
    </div>

    <div class="form-container">
        <div class="form-box" id="registerForm" style="display: none;">
            <h2>Register</h2>
            <form action="hciregister.php" method="POST">
                <input type="text" name="firstname" placeholder="First Name" required>
                <input type="text" name="lastname" placeholder="Last Name" required>
                <input type="number" name="age" placeholder="Age" min="18" required>
                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit">Register</button>
            </form>
            <div class="toggle-btn" onclick="showLoginForm()">Already have an account? Log in here</div>
        </div>

        <div class="form-box" id="loginForm">
            <h2>Login</h2>
            <form action="hcilogin.php" method="POST">
                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit">Log In</button>
            </form>
            <div class="toggle-btn" onclick="showRegisterForm()">Don't have an account? Register here</div>
        </div>
    </div>
</section>

<script>
    document.getElementById('loginForm').style.display = 'block';

    function showRegisterForm() {
        document.getElementById('loginForm').style.display = 'none';
        document.getElementById('registerForm').style.display = 'block';
    }

    function showLoginForm() {
        document.getElementById('loginForm').style.display = 'block';
        document.getElementById('registerForm').style.display = 'none';
    }
</script>

<?php include 'hcifooter.php';?>

</body>
</html>
