<?php 
session_start();
include '../db/connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];


$user_query = "SELECT name, profile_pic FROM users WHERE id = ?";
$stmt = $conn->prepare($user_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_result = $stmt->get_result();
$user = $user_result->fetch_assoc();


$profile_pic = !empty($user['profile_pic']) ? $user['profile_pic'] : 'uploads/profile/default_profile.png';


$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';
$search_sql = "SELECT * FROM properties WHERE (title LIKE ? OR location LIKE ?) AND status = 'approved' ORDER BY created_at DESC";

$stmt = $conn->prepare($search_sql);
$search_param = "%{$search_query}%";
$stmt->bind_param("ss", $search_param, $search_param);
$stmt->execute();
$properties_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <style>
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background-color: rgba(244, 244, 244, 0.9);
           
            background-size: cover;
            background-position: center;
          
        }
        .footer {
            margin-top: auto;
            background: rgba(17, 24, 39, 0.9);
            backdrop-filter: blur(5px);
        }
    </style>
</head>
<body class="bg-gray-50">


    <nav class="bg-white shadow-md p-4">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-3xl font-bold text-blue-700 tracking-wide">🏡 Real Estate Listing</h1>

            <form method="GET" class="flex bg-gray-100 rounded-full p-2 shadow-md">
                <input type="text" name="search" class="px-4 py-2 w-80 rounded-full outline-none" placeholder="Search properties..." value="<?php echo htmlspecialchars($search_query); ?>">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-full ml-2">Search</button>
            </form>

            <div class="flex items-center gap-6">
                <a href="user_dashboard.php" class="text-gray-800 hover:text-blue-500">Home</a>
                <a href="about.php" class="text-gray-800 hover:text-blue-500">About</a>
                <a href="logout.php" class="text-red-500 hover:text-red-700">Logout</a>

                
                <?php
                $default_profile_pic = 'https://www.w3schools.com/howto/img_avatar.png';
                $profile_pic = !empty($user['profile_pic']) ? $user['profile_pic'] : $default_profile_pic;
                ?>
                <a href="profile.php" class="relative w-14 h-14 bg-gray-300 flex items-center justify-center rounded-full shadow-md border-2 border-gray-300 overflow-hidden">
                    <img src="<?php echo htmlspecialchars($profile_pic); ?>" alt="Profile" class="w-full h-full object-cover">
                </a>
            </div>
        </div>
    </nav>


    <div id="carouselExampleIndicators" class="carousel slide mt-6 container mx-auto" data-bs-ride="carousel">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" class="active"></button>
            <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="1"></button>
            <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="2"></button>
        </div>
        <div class="carousel-inner rounded-lg shadow-lg">
            <div class="carousel-item active">
                <img src="..\carousel\a.webp" class="d-block w-100 h-72 object-cover rounded-lg" alt="House 1">
            </div>
            <div class="carousel-item">
                <img src="..\carousel\2.png" class="d-block w-100 h-72 object-cover rounded-lg" alt="House 2">
            </div>
            <div class="carousel-item">
                <img src="..\carousel\3.avif" class="d-block w-100 h-72 object-cover rounded-lg" alt="House 3">
            </div>
        </div>
    </div>

    
      <div class="container mx-auto px-6 py-12 flex-grow">
        <h3 class="text-3xl font-bold text-gray-800 text-center mb-6">Available Properties</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <?php if ($properties_result->num_rows > 0): ?>
                <?php while ($property = $properties_result->fetch_assoc()): ?>
                    <div class="bg-white p-6 rounded-lg shadow-lg transform transition duration-300 hover:scale-105">
                        <img src="../<?php echo htmlspecialchars($property['image1']); ?>" class="w-full h-48 object-cover rounded-md" alt="Property Image">
                        <h4 class="text-2xl font-semibold mt-4 text-blue-700"><?php echo htmlspecialchars($property['title']); ?></h4>
                        <p class="text-gray-600">📍 <?php echo htmlspecialchars($property['location']); ?></p>
                        <p class="text-gray-600">💰 Rs. <?php echo number_format($property['price'], 2); ?></p>
                        <p class="text-sm text-gray-500">Type: <?php echo htmlspecialchars($property['type']); ?></p>

                        <div class="mt-4 flex justify-between space-x-2">
                            <a href="property_details.php?property_id=<?php echo $property['id']; ?>" class="bg-blue-500 text-white px-3 py-2 rounded hover:bg-blue-600 flex-1 text-center">View</a>
                            <a href="enquiry.php?property_id=<?php echo $property['id']; ?>" class="bg-gray-500 text-white px-3 py-2 rounded hover:bg-gray-600 flex-1 text-center">Enquiry</a>
                            <a href="feedback.php?property_id=<?php echo $property['id']; ?>" class="bg-green-500 text-white px-3 py-2 rounded hover:bg-green-600 flex-1 text-center">Feedback</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="text-gray-500 text-center w-full">No approved properties found.</p>
            <?php endif; ?>
        </div>
    </div>

    
    <footer class="footer text-white py-8">
        <div class="container mx-auto flex justify-between">
            <div>
                <h4 class="text-xl font-semibold">Real Estate</h4>
                <p class="text-gray-400 mt-2">Find your dream home with us.</p>
            </div>
            <div>
                <h4 class="text-xl font-semibold">Quick Links</h4>
                <ul class="mt-2">
                    <li><a href="about.php" class="text-gray-400 hover:text-white">About Us</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-xl font-semibold">Follow Us</h4>
                <div class="flex space-x-4 mt-2">
                    <i class="fab fa-facebook text-lg cursor-pointer hover:text-blue-400"></i>
                    <i class="fab fa-twitter text-lg cursor-pointer hover:text-blue-300"></i>
                    <i class="fab fa-instagram text-lg cursor-pointer hover:text-pink-400"></i>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>