<?php
session_start();
include 'hciheader.php';

if (!isset($_SESSION['username'])) {
    header("Location: loginregister.php");
    exit();
}

$role = $_SESSION['role'];
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
            padding: 50px 8%;
            margin-top: 20px;
            margin-bottom: 20px;
        }

        .header {
            text-align: center;
        }

        .header h1 {
            font-size: 3em;
            color: #e0e0e0;
            font-family: 'Anton', serif;
        }

        .tagline {
            font-size: 1.2em;
            color: #c0c0c0;
            font-style: italic;
        }

        .content {
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding: 40px;
            gap: 100px;
        }

        .image-container img {
            width: 1000px;
            max-width: 1000px;
            height: 600px;
            border-radius: 50px;
        }

        .text-container {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            height: auto;
            gap: 20px;
        }

        .announcement-box {
            background-color: #1a1a1a;
            border: 2px solid white;
            padding: 20px;
            border-radius: 20px;
            width: 100%;
        }

        .announcement-header {
            margin-bottom: 20px;
        }

        .announcement-header h2 {
            font-size: 2em;
            color: #f5f5f5;
        }

        .announcement-container {
            max-height: 500px;
            height: 500px;
            width: 400px;
            overflow-y: auto;
        }

        .announcement-item {
            border-bottom: 1px solid #444;
            margin-bottom: 15px;
            padding-bottom: 15px;
        }

        .announcement-item h3 {
            font-size: 1.5em;
            color: #f5f5f5;
        }

        .announcement-item p {
            font-size: 1.2em;
            color: #c0c0c0;
        }
        .categories {
            height: 750px;
            margin: 8rem;
        }

        .categories-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(275px, auto));
            gap: 2.5rem;
            align-items: center;
            text-align: center;
            margin-top: 1rem;
        }

        .features {
            padding: 30px 20px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            box-shadow: -1px 15px 26px -4px rgba(161, 151, 151, 0.15);
            border-radius: 0.7rem;
            cursor: pointer;
            transition: all ease 0.5s;
            border: 1px solid white;
        }

        .features:hover {
            transform: translateY(-8px);
        }

        .features1 {
            width: 100%;
            height: 190px;
        }

        .features1 img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .features h3 {
            margin: 15px 0;
            font-weight: bold;
            font-size: 25px;
            font-family: 'Playfair Display', serif;
            color: white;
        }

        .features p {
            font-size: 1rem;
            line-height: 30px;
            color: white;
        }

        .home-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-top: 30px;
        }

        .btn {
            display: inline-block;
            padding: 12px 26px;
            background: black;
            color: #ffffff;
            font-size: 1rem;
            font-weight: bold;
            border-radius: 0.7rem;
            transition: all ease .50s;
            border: 1px solid white;
        }

        .btn:hover {
            transform: scale(1.1) translateX(-5px);
            background-color: white;
            color: black;
        }
    </style>
</head>
<body>
<section>
    <div class="header">
        <h1>LIBINGAN PANG LUNGSOD NG MUNTINLUPA</h1>
        <p class="tagline">For more than 28 years, LIBINGAN PANG LUNGSOD NG MUNTINLUPA has been a respected place for providing a tranquil and peaceful final resting area for our departed loved ones.</p>
    </div>

    <div class="content">
        <div class="image-container">
            <img id="cemeteryImage" src="cemetery4.jpg" alt="Cemetery View">
        </div>
        <div class="text-container">
            <div class="announcement-box">
                <div class="announcement-header">
                    <h2>Announcements</h2>
                </div>
                <div class="announcement-container">
                    <?php
                    // Database connection
                    include 'db_connection.php';

                    // Fetch all announcements
                    $query = "SELECT title, content FROM announcements ORDER BY id DESC";
                    $result = mysqli_query($conn, $query);

                    if ($result && mysqli_num_rows($result) > 0) {
                        while ($announcement = mysqli_fetch_assoc($result)) {
                            echo "<div class='announcement-item'>";
                            echo "<h3>" . htmlspecialchars($announcement['title']) . "</h3>";
                            echo "<p>" . htmlspecialchars($announcement['content']) . "</p>";
                            echo "</div>";
                        }
                    } else {
                        echo "<p>No announcements at this time. Stay tuned for updates!</p>";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="categories" id="categories">
    <div class="header">
        <h1>FEATURES</h1>
    </div>

    <div class="categories-content">
        <div class="features">
            <div class="features1">
                <img src="mapslogo.png" alt="Map Logo">
            </div>
            <h3>Maps</h3>
            <p>A static map that displays the entire layout of the cemetery. The map provides a comprehensive visual representation of the grounds, including designated sections, pathways, and entrance points. It offers an overview of the cemetery’s organization, helping users locate specific areas and identify key points of interest. The detailed depiction aids in navigation and planning visits.</p>
            <div class="home-btn">
                <a href="#" class="btn">View More</a>
            </div>
        </div>

        <div class="features">
            <div class="features1">
                <img src="servicess.png" alt="Cemetery Image">
            </div>
            <h3>Services</h3>
            <p>The cemetery offers a range of comprehensive services designed to provide respectful and dignified care for loved ones. The focus is on creating a serene environment for families and visitors, providing compassionate support and personalized options. The well-maintained grounds ensure a beautiful setting, preserving the dignity of the resting place.</p>
            <div class="home-btn">
                <a href="#" class="btn">View More</a>
            </div>
        </div>

        <div class="features">
            <div class="features1">
                <img src="aboutus.png" alt="About Us Image">
            </div>
            <h3>About Us</h3>
            <p>Our cemetery is a place of peace and remembrance, offering a tranquil environment where families can honor their loved ones. Committed to dignity and respect, we provide a serene setting that reflects the natural beauty of the surroundings. We are dedicated to compassionate care and maintaining high standards, ensuring a place of rest that families can visit and cherish for generations.</p>
            <div class="home-btn">
                <a href="hciaboutus.php" class="btn">View More</a>
            </div>
        </div>
    </div>
</section>


<script>
    const images = ["cemetery4.jpg", "cemetery1.jpg", "cemetery2.jpg", "cemetery3.jpg", "cemetery9.jpg", "cemetery5.jpg", "cemetery6.jpg", "cemetery7.jpg", "cemetery8.jpg"];
    let currentIndex = 0;
    const imageElement = document.getElementById("cemeteryImage");

    function changeImage() {
        currentIndex = (currentIndex + 1) % images.length;
        imageElement.src = images[currentIndex];
    }

    setInterval(changeImage, 2000);
</script>

<?php include 'hcifooter.php'; ?>

</body>
</html
