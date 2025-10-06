<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "real_state";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch counts dynamically
$sql_sellers = "SELECT COUNT(*) AS total_sellers FROM sellers";
$sql_users = "SELECT COUNT(*) AS total_users FROM users";
$sql_properties = "SELECT COUNT(*) AS total_properties FROM properties";

$result_sellers = $conn->query($sql_sellers);
$result_users = $conn->query($sql_users);
$result_properties = $conn->query($sql_properties);

// Fetch data
$sellers_count = ($result_sellers->num_rows > 0) ? $result_sellers->fetch_assoc()['total_sellers'] : 0;
$users_count = ($result_users->num_rows > 0) ? $result_users->fetch_assoc()['total_users'] : 0;
$properties_count = ($result_properties->num_rows > 0) ? $result_properties->fetch_assoc()['total_properties'] : 0;
?>

<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background-color: rgba(244, 244, 244, 0.9);
            background-image: url('https://images.unsplash.com/photo-1600585154340-be6161a56a0c');
            background-size: cover;
            background-position: center;
      
        }
        .header {
            background: #007bff;
            color: white;
            padding: 15px;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            position: relative;
        }
        .logout-btn {
            position: absolute;
            right: 20px;
            top: 12px;
            background-color: #dc3545;
            color: white;
            padding: 10px 15px;
            font-size: 16px;
            font-weight: bold;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            cursor: pointer;
        }
        .logout-btn:hover {
            background-color: #b52b3b;
        }
        .container {
            max-width: 1000px;
            margin: 20px auto;
            background: rgba(255, 255, 255, 0.95);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            flex-grow: 1;
        }
        .stats {
            display: flex;
            justify-content: space-around;
            margin: 20px 0;
        }
        .stat-box {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            flex: 1;
            margin: 10px;
        }
        .stat-box h3 {
            margin: 0;
            font-size: 22px;
            color: #333;
        }
        .stat-box p {
            font-size: 30px;
            font-weight: bold;
            margin: 5px 0;
            color: #007bff;
        }
        .menu {
            display: flex;
            justify-content: space-around;
            margin-top: 20px;
        }
        .menu a {
            text-decoration: none;
            padding: 15px 20px;
            font-size: 18px;
            font-weight: bold;
            color: white;
            background-color: #28a745;
            border-radius: 5px;
            text-align: center;
        }
        .menu a:hover {
            background-color: #218838;
        }
        .footer {
            background: rgba(0, 123, 255, 0.9);
            color: white;
            text-align: center;
            padding: 15px 0;
            margin-top: auto;
            backdrop-filter: blur(5px);
        }
    </style>
</head>
<body>

<div class="header">
    Admin Dashboard
    <a href="user/logout.php" class="logout-btn">Logout</a>
</div>

<div class="container">
    <h2>Welcome, Admin!</h2>
    <p>Manage sellers, users, and properties from the options below:</p>

    <div class="stats">
        <div class="stat-box">
            <h3>Total Sellers</h3>
            <p><?php echo $sellers_count; ?></p>
        </div>
        <div class="stat-box">
            <h3>Total Users</h3>
            <p><?php echo $users_count; ?></p>
        </div>
        <div class="stat-box">
            <h3>Total Properties</h3>
            <p><?php echo $properties_count; ?></p>
        </div>
    </div>

    <div class="menu">
        <a href="manage-seller.php">Manage Sellers</a>
        <a href="manage-user.php">Manage Users</a>
        <a href="manage-property.php">Manage Properties</a>
    </div>
</div>

<div class="footer">
    <p>&copy; 2025 Real Estate Admin Panel. All Rights Reserved.</p>
</div>

</body>
</html>

<?php

$conn->close();
?>