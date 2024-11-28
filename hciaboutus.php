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
     line-height: 1.2;
 }
 section {
     padding: 50px 8%;
     margin-top: 20px;
     margin-bottom: 20px;
 }

 .header {
     text-align: center;
     padding: 0px;
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
     align-items: center;
     padding: 40px;
     gap: 100px;
 }

 .image-container img {
     width: 900px;
     max-width: 900px;
     height: 600px;
     border-radius: 50px;
 }

 .text-container {
     width: 500px;
     height: 550px;
     border-radius: 15px;
     border: 1px solid #fff;
     padding: 20px;
     box-shadow: 0 4px 10px rgba(0, 0, 0, 0.5);
 }

 .text-container p {
     margin-bottom: 15px;
 }

 .p2 {
     text-align: center;
     font-weight: bold;
     font-size: 2rem;
     font-family: 'Funnel Display', serif;
 }

 .p3 {
     font-size: 1.5rem;
     font-family: 'Funnel Display', sans-serif;
 }

 .ul1 {
     list-style-type: none;
     padding-left: 0;
 }

 .ul1 li {
     font-size: 1.2rem;
 }
</style>
</head>
<body>
  <section>
  <div class="header">
      <h1>LIBINGAN PANG LUNGSOD NG MUNTINLUPA</h1>
      <p class="tagline">Muntinlupa Public Cemetery Information</p>
  </div>

  <div class="content">
      <div class="image-container">
          <img src="orgchart.png" alt="Cemetery View">
      </div>
      <div class="text-container" id="text-container">

      </div>
  </div>
</section>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const textContainers = [
      `<div class="text-container">
          <p class ="p2">Community Affairs and Development Office (C.A.D.O).</p>
          <p class ="p3">The Muntinlupa Public Cemetery is managed by the government under the Community Affairs and Development Office (C.A.D.O).</p>
          <p class="p2">Mission</p>
          <p class ="p3">To help develop the communities through quality servies</p>
          <p class ="p3">To assist in bringing to the communities the program of the city government</p>
      </div>`,
        `<div class="text-container">
            <p class ="p2">Community Affairs and Development Office (C.A.D.O).</p>
            <p class ="p3">The Muntinlupa Public Cemetery is managed by the government under the Community Affairs and Development Office (C.A.D.O).</p>
            <p class="p2">Vision</p>
            <p class ="p3">We envision Muntinlupa as a fully developed city with highly organized communities.</p>
        </div>`,
        `<div class="text-container">
            <p class ="p2">Public Cemetery Information</p>
            <p class ="p3">The Libigan Pang Lungsod ng Muntinlupa, located in Soldiers Hills Putatan, Muntinlupa City, Philippines, is a notable local burial ground with historical and cultural significance.</p>
            <p class ="p2">TIMELINE</p>
            <ul class ="ul1">
              <li>1996 - Year the Libingan Pang Lungsod ng Muntinlupa started to operate</li>
              <li>2015 - Year public viewing chapel built</li>
              <li>April to June 2020 - Public crematorium built</li>
              <li>July 9, 2020 - Public crematorium started to operate</li>
              <li>11,408 - Total niches built in the public cemetery</li>
            </ul>
        </div>`,
        `<div class="text-container">
            <p class ="p2">Total of Niches Per Group</p>
            <p class ="p2">Adult</p>
            <ul class ="ul1">
              <li>Group 1 = 335</li>
              <li>Group 2 = 340</li>
              <li>Group 3 = 309</li>
              <li>Group 4 = 304</li>
              <li>Group 5 = 275</li>
              <li>Group 6 = 275</li>
              <li>Group 7 = 228</li>
              <li>Group 8 = 272</li>
              <li>Group 9 = 289</li>
              <li>Group 10 = 290</li>
            </ul>
        </div>`,
        `<div class="text-container">
            <p class ="p2">Total of Niches Per Group</p>
            <p class ="p2">Adult</p>
            <ul class ="ul1">
              <li>Group 11 = 255</li>
              <li>Group 12 = 274</li>
              <li>Group 13 = 240</li>
              <li>Group 14 = 280</li>
              <li>Group ex 14 = 75</li>
              <li>Annex = 241</li>
              <li>Group ex 15 = 75</li>
              <li>Group 15 = 261</li>
            </ul>
        </div>`,
        `<div class="text-container">
            <p class ="p2">Total of Niches Per Group</p>
            <p class ="p2">BABY</p>
            <ul class ="ul1">
              <li>Group A = 566</li>
              <li>Group B = 565</li>
              <li>Group E = 192</li>
              <li>Group BABY 1 = 88</li>
              <li>Group BABY 1 = 88</li>
              <li>Group BABY 1 = 56</li>
              <li>Group BABY 1 = 56</li>
              <li>Group BABY 1 = 80</li>
            </ul>
        </div>`,
        `<div class="text-container">
            <p class ="p2">Total of Niches Per Group</p>
            <p class ="p2">ALANGANIN</p>
            <ul class ="ul1">
              <li>Group C = 326</li>
              <li>Group D = 326</li>
              <li>Group EX. ALA. = 48</li>
            </ul>
            <p class ="p2">BONE BOX</p>
            <ul class ="ul1">
              <li>BONE BOX UPPER = 749</li>
              <li>BONE BOX LOWER = 1119</li>
              <li>BONE BOX PROPER = 1146</li>
              <li>BONE BOX UPPER = 866</li>
            </ul>
        </div>`
    ];

    let currentIndex = 0;
    const container = document.getElementById("text-container");

    function updateTextContainer() {
        container.innerHTML = textContainers[currentIndex];
        currentIndex = (currentIndex + 1) % textContainers.length;
    }


    updateTextContainer();


    setInterval(updateTextContainer, 3000);
});

</script>

  <?php include 'hcifooter.php';?>

</body>
</html>
