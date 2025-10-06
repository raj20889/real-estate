<?php
include '../db/connect.php';


session_start();
if (!isset($_SESSION['user_id'])) {
    die("User not logged in.");
}
$user_id = $_SESSION['user_id']; 

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$success_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $property_id = $_POST['property_id']; 
    $rating = $_POST['rating'];
    $message = $_POST['message'];

    
    $stmt = $conn->prepare("INSERT INTO feedback (property_id, user_id, rating, comments, submitted_at) VALUES (?, ?, ?, ?, NOW())");
    $stmt->bind_param("iiis", $property_id, $user_id, $rating, $message);

    if ($stmt->execute()) {
        $success_message = "Feedback submitted successfully!";
    } else {
        $success_message = "Error submitting feedback.";
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
    <title>Property Feedback Form</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background-image: url('https://images.unsplash.com/photo-1600585154340-be6161a56a0c');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
         
            background-blend-mode: overlay;
        }
        
        .form-container {
            backdrop-filter: blur(8px);
            background-color: rgba(255, 255, 255, 0.92);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }
        
        .rating-option {
            transition: all 0.2s ease;
        }
        
        .rating-option:hover {
            background-color: rgba(59, 130, 246, 0.1);
            transform: translateY(-2px);
        }
        
        .rating-option.selected {
            background-color: rgba(59, 130, 246, 0.2);
            border-color: #3b82f6;
        }
        
        textarea {
            min-height: 120px;
            transition: all 0.3s ease;
        }
        
        textarea:focus {
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
        }
        
        .success-modal {
            animation: fadeIn 0.3s ease-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-4">
    <div class="form-container p-8 rounded-xl w-full max-w-md">
        <div class="text-center mb-6">
            <h2 class="text-3xl font-bold text-gray-800">Property Feedback</h2>
            <p class="text-gray-600 mt-2">Share your experience with this property</p>
        </div>

        <form method="POST" action="" class="space-y-5">
            <input type="hidden" name="property_id" value="<?php echo isset($_GET['property_id']) ? htmlspecialchars($_GET['property_id']) : ''; ?>">

            
            <div>
                <label class="block text-gray-700 font-medium mb-3">Your Rating</label>
                <div class="space-y-2">
                    <label class="rating-option flex items-center p-3 border rounded-lg cursor-pointer">
                        <input type="radio" name="rating" value="5" class="mr-3" checked>
                        <span class="text-yellow-400 text-xl">⭐⭐⭐⭐⭐</span>
                        <span class="ml-2 text-gray-700">Excellent</span>
                    </label>
                    <label class="rating-option flex items-center p-3 border rounded-lg cursor-pointer">
                        <input type="radio" name="rating" value="4" class="mr-3">
                        <span class="text-yellow-400 text-xl">⭐⭐⭐⭐</span>
                        <span class="ml-2 text-gray-700">Good</span>
                    </label>
                    <label class="rating-option flex items-center p-3 border rounded-lg cursor-pointer">
                        <input type="radio" name="rating" value="3" class="mr-3">
                        <span class="text-yellow-400 text-xl">⭐⭐⭐</span>
                        <span class="ml-2 text-gray-700">Average</span>
                    </label>
                    <label class="rating-option flex items-center p-3 border rounded-lg cursor-pointer">
                        <input type="radio" name="rating" value="2" class="mr-3">
                        <span class="text-yellow-400 text-xl">⭐⭐</span>
                        <span class="ml-2 text-gray-700">Poor</span>
                    </label>
                    <label class="rating-option flex items-center p-3 border rounded-lg cursor-pointer">
                        <input type="radio" name="rating" value="1" class="mr-3">
                        <span class="text-yellow-400 text-xl">⭐</span>
                        <span class="ml-2 text-gray-700">Very Bad</span>
                    </label>
                </div>
            </div>

    
            <div>
                <label class="block text-gray-700 font-medium mb-2">Your Feedback</label>
                <textarea name="message" rows="4" class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" required placeholder="Please share your detailed experience with this property..."></textarea>
            </div>

            <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-medium py-3 px-4 rounded-lg shadow-md transition-all duration-300 transform hover:scale-[1.02]">
                Submit Feedback
            </button>
        </form>

        <div class="text-center mt-6">
            <a href="user_dashboard.php" class="inline-flex items-center px-5 py-2.5 text-white bg-gray-500 hover:bg-gray-600 rounded-lg shadow-md transition-all duration-300 hover:shadow-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
                Back to Homepage
            </a>
        </div>
    </div>

    
    <?php if (!empty($success_message)): ?>
    <div id="popup" class="success-modal fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
        <div class="bg-white p-8 rounded-xl shadow-2xl w-96 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h3 class="text-xl font-semibold text-gray-800 mb-2"><?php echo htmlspecialchars($success_message); ?></h3>
            <p class="text-gray-600 mb-6">Thank you for your valuable feedback!</p>
            <button onclick="closePopup()" class="px-6 py-2 bg-blue-500 text-white rounded-lg shadow-md hover:bg-blue-600 transition-all duration-300">
                OK
            </button>
        </div>
    </div>

    <script>
        function closePopup() {
            document.getElementById("popup").style.display = "none";
            <?php if (strpos($success_message, 'successfully') !== false): ?>
                setTimeout(function() {
                    window.location.href = "user_dashboard.php";
                }, 500);
            <?php endif; ?>
        }
        
        
        document.querySelectorAll('.rating-option').forEach(option => {
            option.addEventListener('click', function() {
                document.querySelectorAll('.rating-option').forEach(opt => {
                    opt.classList.remove('selected');
                });
                this.classList.add('selected');
            });
        });
    </script>
    <?php endif; ?>
</body>
</html>