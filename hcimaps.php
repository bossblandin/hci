<?php
session_start();
include 'hciheader.php';

if (!isset($_SESSION['username'])) {
    header("Location: loginregister.php");
    exit();
}

$role = $_SESSION['role'];

// Database connection
$conn = new mysqli("localhost", "root", "", "logincemetery_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$searchResult = [];
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['search'])) {
    $search = $conn->real_escape_string($_POST['search']);

    // Query to fetch matching records based on multiple fields
    $sql = "SELECT * FROM records
            WHERE last_name LIKE '%$search%'
               OR first_name LIKE '%$search%'
               OR date_of_burial LIKE '%$search%'
               OR group_name LIKE '%$search%'
               OR group_number LIKE '%$search%'
               OR group_letter LIKE '%$search%'";

    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $searchResult[] = $row;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Records</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #000; /* Black background */
            color: #fff; /* White text */
        }
        .container {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 20px;
            max-width: 1700px;
            margin: 0 auto;
            gap: 40px; /* Increased spacing */
        }
        .map-container {
            flex: 2;
            background-color: #1a1a1a; /* Dark background for the map */
            border-radius: 15px;
            padding: 15px;
            box-shadow: 0 0 15px rgba(255, 255, 255, 0.2);
            height: 800px;
        }
        iframe {
            width: 100%;
            height: 600px; /* Increased map height */
            border: 0;
            border-radius: 10px;
        }
        .search-container {
            flex: 1.2; /* Make search container a bit larger */
            background-color: #1a1a1a; /* Match the map background */
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 0 15px rgba(255, 255, 255, 0.2);
        }
        .search-container h2, p {
            text-align: center;
            margin-bottom: 25px;
            font-weight: bold;
            font-size: 50px; /* Increased heading font size */
        }
        .search-container p {
            font-size: 25px;
            font-style: italic;
        }
        .search-container h3{
          font-size: 2rem;
          font-weight: bold;
          margin-top: 30px;
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        input[type="text"] {
            padding: 15px; /* Increased padding */
            font-size: 25px; /* Larger input font size */
            border: none;
            border-radius: 8px;
            background-color: #333;
            color: #fff;
        }
        button {
            padding: 15px; /* Increased padding */
            font-size: 25px; /* Larger button font size */
            border: none;
            border-radius: 8px;
            background-color: #007bff;
            color: white;
            border: 1px solid white;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: black;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
            margin-bottom: 30px;
            font-size: 18px; /* Larger table font size */
        }
        th, td {
            padding: 15px; /* Increased padding */
            border: 1px solid #444;
            text-align: left;
        }
        th {
            background-color: #333;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Map Section -->
        <div class="map-container">
          <iframe style="width:1000px;height:750px;padding:0;border:solid 1px black" src="https://www.mapchannels.com/js/locationmap/map.htm?mx=121.040439&my=14.403008&mz=15&mt=2&sx=121.040678&sy=14.402903&sh=294&sp=0&sz=0.9999999999999997&dm=1&mw=250&tc=1&mn=3" marginwidth="0" marginheight="0" frameborder="0" scrolling="no"></iframe></div>
        <!-- Search Section -->
        <div class="search-container">
            <h2>Search Records</h2>
            <p>Search by name, burial date, or group</p>
            <form method="POST" action="">
                <input type="text" name="search" placeholder="Search by name, burial date, or group" required>
                <button type="submit">Search</button>
            </form>
            <?php if (!empty($searchResult)) { ?>
                <h3>Results:</h3>
                <table>
                    <tr>
                        <th>ID</th>
                        <th>Last Name</th>
                        <th>First Name</th>
                        <th>Middle Initial</th>
                        <th>Suffix</th>
                        <th>Date of Burial</th>
                        <th>Group Name</th>
                        <th>Group Number</th>
                        <th>Group LE</th>
                    </tr>
                    <?php foreach ($searchResult as $record) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($record['id']); ?></td>
                            <td><?php echo htmlspecialchars($record['last_name']); ?></td>
                            <td><?php echo htmlspecialchars($record['first_name']); ?></td>
                            <td><?php echo htmlspecialchars($record['middle_initial']); ?></td>
                            <td><?php echo htmlspecialchars($record['suffix']); ?></td>
                            <td><?php echo htmlspecialchars($record['date_of_burial']); ?></td>
                            <td><?php echo htmlspecialchars($record['group_name']); ?></td>
                            <td><?php echo htmlspecialchars($record['group_number']); ?></td>
                            <td><?php echo htmlspecialchars($record['group_letter']); ?></td>
                        </tr>
                    <?php } ?>
                </table>
                <button id="backBtn" onclick="hideResults()">Back to Search</button> <!-- Back button only displayed when results exist -->
            <?php } elseif ($_SERVER['REQUEST_METHOD'] == 'POST') { ?>
                <p>No records found.</p>
            <?php } ?>
        </div>
    </div>
    <?php include 'hcifooter.php'; ?>

    <script>
        function hideResults() {
            // Hide the results table and the back button
            document.querySelector("h3").style.display = "none";
            document.querySelector("table").style.display = "none";
            document.getElementById("backBtn").style.display = "none";
            // Optionally, you could also clear the search input if needed
            document.querySelector("input[name='search']").value = '';
        }
    </script>
</body>
</html>
