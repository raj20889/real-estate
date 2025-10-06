<?php
session_start();
include '../db/connect.php';


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = "";
$show_success_modal = false;


$query = $conn->prepare("SELECT name, email, profile_pic FROM users WHERE id = ?");
$query->bind_param("i", $user_id);
$query->execute();
$query->bind_result($name, $email, $profile_pic);
$query->fetch();
$query->close();


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_name = $conn->real_escape_string($_POST['name']);
    $new_email = $conn->real_escape_string($_POST['email']);
    

    if (!empty($_POST['password'])) {
        $new_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $conn->query("UPDATE users SET password='$new_password' WHERE id=$user_id");
    }
    
    
    if (!empty($_FILES['profile_pic']['name'])) {
        $uploadDir = "../uploads/profile/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        
        if (!empty($profile_pic) && $profile_pic !== '../uploads/profile/default.jpg') {
            if (file_exists($profile_pic)) {
                unlink($profile_pic);
            }
        }
        
        $fileTmpPath = $_FILES['profile_pic']['tmp_name'];
        $fileName = uniqid() . "_" . basename($_FILES['profile_pic']['name']);
        $filePath = $uploadDir . $fileName;
        
        if (move_uploaded_file($fileTmpPath, $filePath)) {
            $profile_pic = $filePath;
            $conn->query("UPDATE users SET profile_pic='$filePath' WHERE id=$user_id");
        }
    }
    
    
    $conn->query("UPDATE users SET name='$new_name', email='$new_email' WHERE id=$user_id");
    
    
    $query = $conn->prepare("SELECT name, email, profile_pic FROM users WHERE id = ?");
    $query->bind_param("i", $user_id);
    $query->execute();
    $query->bind_result($name, $email, $profile_pic);
    $query->fetch();
    $query->close();
    
    $show_success_modal = true;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background-image: url('https://images.unsplash.com/photo-1600585154340-be6161a56a0c');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
           
            background-color: rgba(244, 244, 244, 0.9);
        }
        
        .profile-container {
            backdrop-filter: blur(8px);
            background-color: rgba(255, 255, 255, 0.85);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }
        
        .modal-content {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            text-align: center;
            max-width: 400px;
            width: 90%;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
            animation: modalFadeIn 0.3s;
        }
        
        @keyframes modalFadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .profile-img {
            width: 120px;
            height: 120px;
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        
        .profile-img:hover {
            transform: scale(1.05);
        }
        
        .file-upload-label {
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .file-upload-label:hover {
            transform: translateY(-2px);
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-4">
    <div class="profile-container p-6 rounded-xl shadow-2xl w-full max-w-lg">
        
        <a href="user_dashboard.php" class="inline-flex items-center px-4 py-2 mb-4 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 transition-colors">
            ← Back to Dashboard
        </a>

        <h2 class="text-3xl font-bold text-center mb-6 text-gray-800">Profile Settings</h2>
        
        
        <div class="flex justify-center mb-6">
            <div class="relative group">
                <img src="<?php echo !empty($profile_pic) ? htmlspecialchars($profile_pic) : '../uploads/profile/default.jpg'; ?>" 
                     class="profile-img rounded-full border-4 border-white shadow-lg">
                <label for="profile_pic" class="file-upload-label absolute bottom-0 right-0 bg-blue-500 rounded-full p-2 shadow-md cursor-pointer hover:bg-blue-600 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </label>
            </div>
        </div>

        <form action="" method="POST" enctype="multipart/form-data" class="mt-4 space-y-5">
            <div>
                <label for="name" class="block font-medium text-gray-700 mb-1">Full Name</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition">
            </div>

            <div>
                <label for="email" class="block font-medium text-gray-700 mb-1">Email Address</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition">
            </div>

            <div>
                <label for="password" class="block font-medium text-gray-700 mb-1">New Password</label>
                <input type="password" id="password" name="password" placeholder="Leave blank to keep current password"
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition">
                <p class="text-xs text-gray-500 mt-1">Minimum 8 characters</p>
            </div>

            <input type="file" id="profile_pic" name="profile_pic" accept="image/*" class="hidden">

            <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-md hover:bg-blue-700 transition-colors font-medium">
                Save Changes
            </button>
        </form>
    </div>

    
    <?php if ($show_success_modal): ?>
    <div class="modal" id="successModal" style="display: flex;">
        <div class="modal-content">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h2 class="mt-3 text-lg font-medium text-gray-900">Profile Updated!</h2>
            <div class="mt-2">
                <p class="text-sm text-gray-500">Your changes have been saved successfully.</p>
            </div>
            <div class="mt-4">
                <button type="button" onclick="document.getElementById('successModal').style.display='none'" 
                    class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                    Close
                </button>
            </div>
        </div>
    </div>
    <script>

        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('successModal').style.display = 'flex';
        });
    </script>
    <?php endif; ?>

    <script>
        
        document.getElementById('profile_pic').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    document.querySelector('.profile-img').src = event.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
        
        
        document.querySelector('.file-upload-label').addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('profile_pic').click();
        });
    </script>
</body>
</html>