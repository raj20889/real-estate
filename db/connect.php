<?php
$servername = "localhost";  // Change if using a remote DB
$username = "root";         // Your MySQL username
$password = "";             // Your MySQL password
$database = "real_state"; // Change to your actual DB name

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// echo "Connected successfully"; // Uncomment to test connection
?>
