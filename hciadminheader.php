<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Website Header</title>
    <style>
        * {
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
            font-family: Arial, sans-serif;
        }
        header {
            width: 100%;
            height: 120px; /* Set a fixed height for centering */
            display: flex;
            align-items: center;
            justify-content: center; /* Center horizontally */
            background: var(--header-color);
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
            border-bottom: 5px solid var(--main-color);
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        .logo {
            color: var(--main-color);
            font-size: 3.2rem;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">Muntinlupa Public Cemetery Administrator</div>
    </header>
</body>
</html>
