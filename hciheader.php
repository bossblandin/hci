<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Website Header</title>
    <style>
        *{
            padding: 0;
            margin: 0;
            box-sizing: border-box;
            text-decoration: none;
            list-style: none;
            scroll-behavior: smooth;
        }
        :root {
            --header-color: #000;
            --main-color: #fff;
            --hover-color: #888;
            --button-border-radius: 20px;
            --padding-horizontal: 20px;
            --padding-vertical: 10px;
        }
        body {
            margin: 0;
            padding: 0;
            background-color: #121212;
            font-family: Arial, sans-serif;
        }
        header {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: var(--header-color);
            padding: 25px 5%;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
            border-bottom: 2px solid var(--main-color);
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        .logo {
            color: var(--main-color);
            font-size: 2.2rem;
            font-weight: bold;
        }
        .navbar {
            display: flex;
            font-size: 1.2rem;
            gap: 80px;
        }
        .navbar a {
            color: var(--main-color);
            text-decoration: none;
            padding: var(--padding-vertical) var(--padding-horizontal);
            border: 1px solid var(--main-color);
            border-radius: var(--button-border-radius);
            transition: all 0.3s ease;
        }
        .navbar a:hover {
            color: var(--header-color);
            background: var(--main-color);
        }
        .navbar a.active {
            background: var(--main-color);
            color: var(--header-color);
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">Muntinlupa Public Cemetery</div>
        <nav class="navbar">
            <a href="homepage.php">Home</a>
            <a href="hcimaps.php">Maps</a>
            <a href="hciservices.php">Services</a>
            <a href="hciaboutus.php">About Us</a>
            <a href="hciaccount.php">Account</a>
        </nav>
    </header>

    <script>
        // Get all the navigation links
        const navLinks = document.querySelectorAll('.navbar a');

        // Get the current page's filename
        const currentPage = window.location.pathname.split('/').pop();

        // Loop through each link
        navLinks.forEach(link => {
            // Check if the link href matches the current page
            if (link.getAttribute('href') === currentPage) {
                // Add the 'active' class to the current page link
                link.classList.add('active');
            }

            // Add a click event listener to each link
            link.addEventListener('click', function() {
                // Remove the active class from all links
                navLinks.forEach(link => link.classList.remove('active'));

                // Add the active class to the clicked link
                this.classList.add('active');
            });
        });
    </script>
</body>
</html>
