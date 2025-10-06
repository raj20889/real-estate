<?php
session_start();
include 'db/connect.php';

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $rating = $_POST['rating'];
    $comments = $_POST['comments'];
    
    $stmt = $conn->prepare("INSERT INTO feedback (rating, comments, submitted_at) VALUES (?, ?, NOW())");
    $stmt->bind_param("ss", $rating, $comments);
    
    if ($stmt->execute()) {
        echo "<p class='text-green-600 text-center'>Feedback submitted successfully!</p>";
    } else {
        echo "<p class='text-red-600 text-center'>Error submitting feedback.</p>";
    }
    
    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback Form</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex items-center justify-center min-h-screen bg-gray-100">
    <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md">
        <h2 class="text-2xl font-semibold text-gray-700 text-center">Feedback</h2>
        <form method="POST" action="">
            <label class="block mt-4 text-gray-600 font-medium">Rating:</label>
            <select name="rating" class="w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                <option value="1">1 - Poor</option>
                <option value="2">2 - Fair</option>
                <option value="3">3 - Good</option>
                <option value="4">4 - Very Good</option>
                <option value="5">5 - Excellent</option>
            </select>

            <label class="block mt-4 text-gray-600 font-medium">Comments:</label>
            <textarea name="comments" rows="4" class="w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" placeholder="Leave your feedback here..." required></textarea>
            
            <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-medium p-2 rounded-lg mt-4">Submit Feedback</button>
        </form>
    </div>
</body>
</html>
