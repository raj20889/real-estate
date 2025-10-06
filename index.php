<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);


$servername = "localhost";
$username = "root";
$password = "";
$dbname = "real_state";


$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$sql = "SELECT id, title, location, description, price, type, image1, image2, image3 FROM properties WHERE status = 'approved'";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Real Estate Listings</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.css" />

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        
        .navbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background-color: #007bff;
            padding: 15px 20px;
        }
        .logo {
            font-size: 22px;
            font-weight: bold;
            color: white;
        }
        .navbar a {
            color: white;
            text-decoration: none;
            padding: 10px 15px;
            margin: 0 5px;
            background-color: #0056b3;
            border-radius: 5px;
        }
        .navbar a:hover {
            background-color: #003d82;
        }

        
        .greeting {
            text-align: center;
            font-size: 26px;
            font-weight: bold;
            color: #333;
            margin: 20px 0;
        }

        
        .hero {
            width: 100%;
            position: relative;
        }
        .swiper {
            width: 100%;
            height: 500px;
        }
        .swiper-slide img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

    
        .swiper-button-prev, .swiper-button-next {
            color: white;
        }
        .swiper-pagination-bullet {
            background: white;
        }

        
        .container {
            max-width: 1000px;
            margin: auto;
            padding: 20px;
        }
        .heading {
            text-align: center;
            margin: 20px 0;
            font-size: 24px;
            color: #333;
        }
        .property-list {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }
        .property {
            background: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        .property img {
            width: 100%;
            height: 200px;
            border-radius: 5px;
            object-fit: cover;
        }
        .property h3 {
            margin: 10px 0;
            font-size: 18px;
        }
        .property p {
            font-size: 14px;
            color: #555;
        }
        .property .price {
            font-size: 16px;
            font-weight: bold;
            color: #28a745;
        }

        
        .footer {
            background: #007bff;
            color: white;
            text-align: center;
            padding: 10px 0;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="navbar">
    <div class="logo">🏡 Real Estate Listings</div>
    <div>
        <a href="user/user_login.php">Login as User</a>
        <a href="sellerlogin.php">Login as Seller</a>
        <a href="adminlogin.php">Login as Admin</a>
    </div>
</div>

<div class="greeting">
    Welcome to Your Dream Home Search! 🏠✨
</div>

<div class="hero">
    <div class="swiper mySwiper">
        <div class="swiper-wrapper">
            <div class="swiper-slide"><img src="carousel/3.avif" alt="Luxury Homes"></div>
            <div class="swiper-slide"><img src="carousel/a.webp" alt="Modern Apartments"></div>
            <div class="swiper-slide"><img src="carousel/1.webp" alt="Spacious Villa"></div>
        </div>
        <div class="swiper-button-next"></div>
        <div class="swiper-button-prev"></div>
        <div class="swiper-pagination"></div>
    </div>
</div>

<div class="container">
    <h2 class="heading">Available Properties</h2>
    <div class="property-list">
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<a href="user/property_details.php?property_id=' . htmlspecialchars($row["id"]) . '" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600" style="text-decoration: none;">';
                echo '<div class="property">';
                echo '<img src="' . htmlspecialchars($row["image1"]) . '" alt="Property Image">';
                echo '<h3>' . htmlspecialchars($row["title"]) . '</h3>';
                echo '<p>Location: ' . htmlspecialchars($row["location"]) . '</p>';
                echo '<p>' . htmlspecialchars($row["description"]) . '</p>';
                echo '<p class="price">₹' . number_format($row["price"], 2) . '</p>';
                echo '<div class="grid grid-cols-3 gap-4 mt-4">';
                echo '</div>';
                echo '</div>';
                echo '</a>';
            }
        } else {
            echo "<p>No approved properties found.</p>";
        }
        ?>
    </div>
</div>

<div class="footer">
    <p>&copy; 2025 Real Estate Listings. All Rights Reserved.</p>
</div>

<script src="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.js"></script>
<script>
    var swiper = new Swiper(".mySwiper", {
        loop: true, 
        autoplay: {
            delay: 3000, 
            disableOnInteraction: false 
        },
        navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev"
        },
        pagination: {
            el: ".swiper-pagination",
            clickable: true
        },
        speed: 1000 
    });
</script>

</body>
</html>

<?php

$conn->close();
?>