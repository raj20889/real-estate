<?php
session_start();


$servername = "localhost";
$username = "root";
$password = "";
$dbname = "real_state";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Invalid property ID.");
}
$property_id = intval($_GET['id']);


$sql = "SELECT * FROM properties WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $property_id);
$stmt->execute();
$property_result = $stmt->get_result();

if ($property_result->num_rows == 0) {
    die("Property not found.");
}
$property = $property_result->fetch_assoc();


$sql_inquiries = "SELECT inquiries.*, 
                        users.name AS user_name, 
                        users.email AS user_email, 
                        users.phone AS user_phone
                        FROM inquiries 
                        JOIN users ON inquiries.user_id = users.id 
                        WHERE property_id = ? 
                        ORDER BY inquiries.inquiry_date DESC, inquiries.created_at DESC";
$stmt_inquiries = $conn->prepare($sql_inquiries);
$stmt_inquiries->bind_param("i", $property_id);
$stmt_inquiries->execute();
$inquiries_result = $stmt_inquiries->get_result();


$sql_feedback = "SELECT feedback.*, 
                        users.name AS user_name, 
                        users.email AS user_email, 
                        users.phone AS user_phone
                        FROM feedback 
                        JOIN users ON feedback.user_id = users.id 
                        WHERE property_id = ? 
                        ORDER BY feedback.submitted_at DESC";
$stmt_feedback = $conn->prepare($sql_feedback);
$stmt_feedback->bind_param("i", $property_id);
$stmt_feedback->execute();
$feedback_result = $stmt_feedback->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($property['title']); ?> | View Property</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8fafc;
        }
        
        .property-card {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            border-radius: 12px;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .property-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        
        .contact-info {
            background-color: #f0f7ff;
            border-left: 4px solid #3b82f6;
            padding: 12px;
            border-radius: 8px;
        }
        
        .rating-stars {
            color: #f59e0b;
        }
        
        .section-title {
            position: relative;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        
        .section-title::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 50px;
            height: 3px;
            background-color: #3b82f6;
        }
        
        .message-box {
            background-color: white;
            border-radius: 8px;
            padding: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            margin-top: 10px;
        }
        
        .user-name {
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 4px;
        }
        
        .fullscreen-image-container {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.9);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }
        
        .fullscreen-image {
            max-width: 90%;
            max-height: 90%;
        }
        
        .close-button {
            position: absolute;
            top: 20px;
            right: 20px;
            color: white;
            font-size: 24px;
            cursor: pointer;
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="container mx-auto px-4 py-8 max-w-6xl">
        <div class="mb-6">
            <a href="seller_dashboard.php" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-300 shadow-md">
                <i class="fas fa-arrow-left mr-2"></i> Back to Dashboard
            </a>
        </div>

        <div class="property-card bg-white mb-8">
            <div class="p-6">
                <h1 class="text-3xl font-bold text-gray-800 mb-2"><?php echo htmlspecialchars($property['title']); ?></h1>
                <div class="flex items-center text-gray-600 mb-4">
                    <i class="fas fa-map-marker-alt mr-2 text-blue-500"></i>
                    <span><?php echo htmlspecialchars($property['location']); ?></span>
                </div>
                
                <div class="inline-block bg-blue-100 text-blue-800 px-4 py-2 rounded-full mb-6 font-semibold">
                    ₹<?php echo number_format($property['price'], 2); ?>
                </div>
                
                <div class="mb-6">
                    <h3 class="text-xl font-semibold text-gray-800 mb-3 section-title">Description</h3>
                    <p class="text-gray-600 leading-relaxed"><?php echo nl2br(htmlspecialchars($property['description'])); ?></p>
                </div>

                <div class="mb-6">
                    <h3 class="text-xl font-semibold text-gray-800 mb-3 section-title">Property Images</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <?php
                        $images = ['image1', 'image2', 'image3'];
                        foreach ($images as $image) {
                            if (!empty($property[$image])) {
                                echo '<img src="uploads/' . basename($property[$image]) . '" alt="Property Image" class="rounded-lg cursor-pointer image-popup" data-src="uploads/' . basename($property[$image]) . '">';
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <div class="property-card bg-white p-6">
                <h3 class="text-2xl font-semibold text-gray-800 mb-6 section-title">
                    <i class="fas fa-question-circle mr-2 text-blue-500"></i> Customer Inquiries
                </h3>
                
                <?php if ($inquiries_result->num_rows > 0): ?>
                    <div class="space-y-4">
                        <?php while ($inquiry = $inquiries_result->fetch_assoc()): ?>
                            <div class="inquiry-card bg-gray-50 p-4 rounded-lg border border-gray-200">
                                <div class="contact-info">
                                    <div class="user-name">
                                        <i class="fas fa-user mr-2 text-blue-500"></i>
                                        <?php echo htmlspecialchars($inquiry['user_name']); ?>
                                    </div>
                                    <div class="mt-3 space-y-2">
                                        <div class="flex items-center text-sm">
                                            <i class="fas fa-envelope mr-2 text-blue-500"></i>
                                            <a href="mailto:<?php echo htmlspecialchars($inquiry['user_email']); ?>" class="text-blue-600 hover:underline">
                                                <?php echo htmlspecialchars($inquiry['user_email']); ?>
                                            </a>
                                        </div>
                                        <div class="flex items-center text-sm">
                                            <i class="fas fa-phone mr-2 text-blue-500"></i>
                                            <a href="tel:<?php echo htmlspecialchars($inquiry['user_phone']); ?>" class="text-gray-700">
                                                <?php echo htmlspecialchars($inquiry['user_phone']); ?>
                                            </a>
                                        </div>
                                        <div class="flex items-center text-sm text-gray-500 mt-2">
                                            <i class="far fa-calendar-alt mr-2"></i>
                                            <?php echo date('M j, Y', strtotime($inquiry['inquiry_date'])); ?>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="message-box">
                                    <p class="text-gray-700"><?php echo htmlspecialchars($inquiry['message']); ?></p>
                                    <div class="text-right mt-2 text-sm text-gray-500">
                                        <i class="far fa-clock mr-1"></i>
                                        <?php echo date('g:i A', strtotime($inquiry['created_at'])); ?>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-8">
                        <i class="far fa-envelope-open text-4xl text-gray-400 mb-3"></i>
                        <p class="text-gray-500">No inquiries yet for this property</p>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="property-card bg-white p-6">
                <h3 class="text-2xl font-semibold text-gray-800 mb-6 section-title">
                    <i class="fas fa-comment-alt mr-2 text-blue-500"></i> Customer Feedback
                </h3>
                
                <?php if ($feedback_result->num_rows > 0): ?>
                    <div class="space-y-4">
                        <?php while ($feedback = $feedback_result->fetch_assoc()): ?>
                            <div class="feedback-card bg-gray-50 p-4 rounded-lg border border-gray-200">
                                <div class="contact-info">
                                    <div class="user-name">
                                        <i class="fas fa-user mr-2 text-blue-500"></i>
                                        <?php echo htmlspecialchars($feedback['user_name']); ?>
                                    </div>
                                    <div class="mt-3 space-y-2">
                                        <div class="flex items-center text-sm">
                                            <i class="fas fa-envelope mr-2 text-blue-500"></i>
                                            <a href="mailto:<?php echo htmlspecialchars($feedback['user_email']); ?>" class="text-blue-600 hover:underline">
                                                <?php echo htmlspecialchars($feedback['user_email']); ?>
                                            </a>
                                        </div>
                                        <div class="flex items-center text-sm">
                                            <i class="fas fa-phone mr-2 text-blue-500"></i>
                                            <a href="tel:<?php echo htmlspecialchars($feedback['user_phone']); ?>" class="text-gray-700">
                                                <?php echo htmlspecialchars($feedback['user_phone']); ?>
                                            </a>
                                        </div>
                                        <div class="flex items-center text-sm text-gray-500 mt-2">
                                            <i class="far fa-calendar-alt mr-2"></i>
                                            <?php echo date('M j, Y', strtotime($feedback['submitted_at'])); ?>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="flex items-center justify-between mb-3 mt-3">
                                    <div class="rating-stars text-lg">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <i class="fas fa-star<?php echo $i <= $feedback['rating'] ? '' : '-half-alt'; ?>"></i>
                                        <?php endfor; ?>
                                        <span class="ml-2 text-gray-700">Rating: <?php echo $feedback['rating']; ?>/5</span>
                                    </div>
                                </div>
                                
                                <div class="message-box">
                                    <p class="text-gray-700"><?php echo htmlspecialchars($feedback['comments']); ?></p>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-8">
                        <i class="far fa-comment-dots text-4xl text-gray-400 mb-3"></i>
                        <p class="text-gray-500">No feedback yet for this property</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="fullscreen-image-container" id="fullscreen-container">
            <img src="" alt="Fullscreen Image" class="fullscreen-image" id="fullscreen-img">
            <span class="close-button" id="close-fullscreen">&times;</span>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const images = document.querySelectorAll('.image-popup');
            const fullscreenContainer = document.getElementById('fullscreen-container');
            const fullscreenImage = document.getElementById('fullscreen-img');
            const closeButton = document.getElementById('close-fullscreen');
            
            images.forEach(image => {
                image.addEventListener('click', function() {
                    fullscreenImage.src = this.dataset.src;
                    fullscreenContainer.style.display = 'flex';
                });
            });
            
            closeButton.addEventListener('click', function() {
                fullscreenContainer.style.display = 'none';
            });
        });
    </script>
</body>
</html>

<?php
$conn->close();
?>