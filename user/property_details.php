<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "real_state";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$property_id = isset($_GET['property_id']) ? (int)$_GET['property_id'] : 0;
if ($property_id == 0) die("Invalid Property ID.");

// Get property details
$sql = "SELECT p.*, s.name as seller_name 
        FROM properties p 
        LEFT JOIN sellers s ON p.seller_id = s.id 
        WHERE p.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $property_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) die("No property found with this ID.");
$property = $result->fetch_assoc();
$stmt->close();

// Get all feedback for this property
$feedback_sql = "SELECT f.*, u.name as user_name 
                 FROM feedback f 
                 LEFT JOIN users u ON f.user_id = u.id 
                 WHERE f.property_id = ? 
                 ORDER BY f.submitted_at DESC";
$feedback_stmt = $conn->prepare($feedback_sql);
$feedback_stmt->bind_param("i", $property_id);
$feedback_stmt->execute();
$feedback_result = $feedback_stmt->get_result();
$feedbacks = $feedback_result->fetch_all(MYSQLI_ASSOC);
$feedback_stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($property['title']); ?> - Property Details</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { font-family: 'Montserrat', sans-serif; background: #f9f9f9; margin: 0; padding: 20px; color: #333; }
        .back-button { padding: 12px 25px; background: #3b82f6; color: white; border: none; border-radius: 10px; cursor: pointer; transition: all 0.3s; }
        .back-button:hover { background: #2563eb; transform: scale(1.03); }
        .property-title { font-size: 2.5rem; font-weight: 700; color: #2d3748; margin: 20px 0 5px; }
        .property-location { font-size: 1.1rem; color: #718096; margin-bottom: 20px; }
        .image-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; margin: 20px 0; }
        .image-container { overflow: hidden; border-radius: 15px; cursor: pointer; transition: transform 0.3s; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .image-container:hover { transform: scale(1.03); }
        .image-container img { width: 100%; height: auto; display: block; border-radius: 15px; }
        .property-description { font-size: 1rem; line-height: 1.7; margin: 20px 0; color: #4a5568; }
        .property-price { font-size: 2.2rem; font-weight: 600; color: #48bb78; margin: 20px 0; }
        .property-details-info { background: #edf2f7; padding: 20px; border-radius: 15px; display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 30px; }
        .fullscreen-img { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.9); justify-content: center; align-items: center; z-index: 1000; }
        .fullscreen-img img { max-width: 95%; max-height: 95%; border-radius: 15px; }
        .close-btn { position: absolute; top: 30px; right: 30px; background: #e53e3e; color: white; padding: 10px 15px; border-radius: 50%; cursor: pointer; }
        .feedback-section { margin-top: 40px; border-top: 1px solid #e2e8f0; padding-top: 30px; }
        .feedback-title { font-size: 1.8rem; font-weight: 600; margin-bottom: 20px; color: #2d3748; }
        .feedback-item { background: white; padding: 20px; border-radius: 10px; margin-bottom: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .feedback-header { display: flex; justify-content: space-between; margin-bottom: 10px; }
        .feedback-user { font-weight: 600; color: #2d3748; }
        .feedback-date { color: #718096; font-size: 0.9rem; }
        .feedback-rating { color: #f59e0b; margin-bottom: 10px; }
        .feedback-comment { color: #4a5568; line-height: 1.6; }
        .no-feedback { color: #718096; font-style: italic; }
    </style>
</head>
<body>
    <button onclick="history.back()" class="back-button">← Back to Listings</button>
    
    <h2 class="property-title"><?php echo htmlspecialchars($property['title']); ?></h2>
    <p class="property-location"><?php echo htmlspecialchars($property['location']); ?></p>

    <div class="image-grid">
        <?php
        $images = ['image1', 'image2', 'image3'];
        foreach ($images as $image) {
            if (!empty($property[$image])) {
                echo '<div class="image-container" onclick="openFullScreen(\'../uploads/' . basename($property[$image]) . '\')">
                        <img src="../uploads/' . basename($property[$image]) . '" alt="Property Image">
                      </div>';
            }
        }
        ?>
    </div>

    <p class="property-description"><?php echo nl2br(htmlspecialchars($property['description'])); ?></p>
    <p class="property-price">₹<?php echo number_format($property['price'], 2); ?></p>

    <div class="property-details-info">
        <p><strong>Type:</strong> <?php echo ucfirst(htmlspecialchars($property['type'])); ?></p>
        <p><strong>Seller:</strong> <?php echo htmlspecialchars($property['seller_name'] ?? 'Unknown'); ?></p>
        <p><strong>Listed On:</strong> <?php echo date("F j, Y", strtotime($property['created_at'])); ?></p>
    </div>

    <!-- Feedback Section -->
    <div class="feedback-section">
        <h3 class="feedback-title">Customer Feedback</h3>
        
        <?php if (count($feedbacks) > 0): ?>
            <?php foreach ($feedbacks as $feedback): ?>
                <div class="feedback-item">
                    <div class="feedback-header">
                        <span class="feedback-user"><?php echo htmlspecialchars($feedback['user_name'] ?? 'Anonymous'); ?></span>
                        <span class="feedback-date"><?php echo date("F j, Y, g:i a", strtotime($feedback['submitted_at'])); ?></span>
                    </div>
                    <div class="feedback-rating">
                        <?php 
                        $rating = (int)$feedback['rating'];
                        echo str_repeat('★', $rating) . str_repeat('☆', 5 - $rating);
                        ?>
                    </div>
                    <p class="feedback-comment"><?php echo nl2br(htmlspecialchars($feedback['comments'])); ?></p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="no-feedback">No feedback yet for this property.</p>
        <?php endif; ?>
    </div>

    <div class="fullscreen-img" id="fullscreenImg">
        <span class="close-btn" onclick="closeFullScreen()">×</span>
        <img id="fullImage" src="" alt="Fullscreen Image">
    </div>

    <script>
        function openFullScreen(src) {
            document.getElementById('fullImage').src = src;
            document.getElementById('fullscreenImg').style.display = 'flex';
        }
        function closeFullScreen() {
            document.getElementById('fullscreenImg').style.display = 'none';
        }
    </script>
</body>
</html>