<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db/connect.php';


if (!isset($_SESSION['seller_id'])) {
    header("Location: login.php");
    exit();
}

$seller_id = $_SESSION['seller_id']; 
$message = "";
$message_type = ""; 
$show_success_modal = false;


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $conn->real_escape_string($_POST['title']);
    $location = $conn->real_escape_string($_POST['location']);
    $description = $conn->real_escape_string($_POST['description']);
    $price = floatval($_POST['price']);
    $type = $conn->real_escape_string($_POST['type']);

    
    $uploadDir = "uploads/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
    $maxFileSize = 16 * 1024 * 1024; 

    $imagePaths = [];
    $imageFields = ['image1', 'image2', 'image3']; 

    $upload_errors = false;

    foreach ($imageFields as $field) {
        if (isset($_FILES[$field]) && $_FILES[$field]['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES[$field]['tmp_name'];
            $fileSize = $_FILES[$field]['size'];
            $fileType = mime_content_type($fileTmpPath);
            $fileName = uniqid() . "_" . basename($_FILES[$field]['name']);

            if (!in_array($fileType, $allowedTypes)) {
                $message = "Error: Only JPG, JPEG, and PNG files are allowed.";
                $message_type = "error";
                $upload_errors = true;
                break;
            }

            if ($fileSize > $maxFileSize) {
                $message = "Error: Each file must be ≤ 16MB.";
                $message_type = "error";
                $upload_errors = true;
                break;
            }

            if (move_uploaded_file($fileTmpPath, $uploadDir . $fileName)) {
                $imagePaths[] = $uploadDir . $fileName;
            } else {
                $message = "Error: File upload failed.";
                $message_type = "error";
                $upload_errors = true;
                break;
            }
        } else {
            $message = "Error: All 3 images are required.";
            $message_type = "error";
            $upload_errors = true;
            break;
        }
    }

    
    if (!$upload_errors && count($imagePaths) === 3) {
        $stmt = $conn->prepare("INSERT INTO properties (seller_id, title, location, description, price, type, image1, image2, image3) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        if ($stmt === false) {
            die("Error preparing statement: " . $conn->error);
        }
        $stmt->bind_param("isssdssss", $seller_id, $title, $location, $description, $price, $type, $imagePaths[0], $imagePaths[1], $imagePaths[2]);

        if ($stmt->execute()) {
            $show_success_modal = true;
        
            $_POST = array();
        } else {
            $message = "Error: " . $stmt->error;
            $message_type = "error";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Property</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>

        body{
            background-image:url("images/adminlogin.jpg");
        }
        :root {
            --primary-color: #4a6fa5;
            --secondary-color: #166088;
            --accent-color: #4fc3f7;
            --success-color: #4caf50;
            --error-color: #f44336;
            --light-gray: #f5f5f5;
            --dark-gray: #333;
            --medium-gray: #757575;
        }
        
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: var(--dark-gray);
            background-color: var(--light-gray);
            padding: 20px;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            position: relative;
        }
        
        .header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 20px;
            text-align: center;
        }
        
        .header h1 {
            font-size: 24px;
            margin-bottom: 5px;
        }
        
        .header p {
            font-size: 14px;
            opacity: 0.9;
        }
        
        .form-container {
            padding: 30px;
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
        
        .modal-icon {
            font-size: 50px;
            color: var(--success-color);
            margin-bottom: 20px;
        }
        
        .modal-title {
            font-size: 22px;
            margin-bottom: 15px;
            color: var(--dark-gray);
        }
        
        .modal-message {
            margin-bottom: 25px;
            color: var(--medium-gray);
        }
        
        .modal-btn {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .modal-btn:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
        }
        
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-row {
            display: flex;
            gap: 20px;
        }
        
        .form-col {
            flex: 1;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            font-size: 14px;
            color: var(--secondary-color);
        }
        
        .input-icon {
            position: relative;
        }
        
        .input-icon i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--medium-gray);
            z-index: 1;
        }
        
        input[type="text"],
        input[type="number"],
        textarea,
        select,
        input[type="file"] {
            width: 100%;
            padding: 12px 15px 12px 40px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 15px;
            transition: all 0.3s;
        }
        
        select {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 15px;
        }
        
        textarea {
            padding-left: 15px;
            min-height: 120px;
            resize: vertical;
        }
        
        input[type="file"] {
            padding: 8px 15px;
            background: var(--light-gray);
            border: 1px dashed #ccc;
        }
        
        .file-hint {
            font-size: 12px;
            color: var(--medium-gray);
            margin-top: 5px;
        }
        
        input[type="text"]:focus,
        input[type="number"]:focus,
        textarea:focus,
        select:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 3px rgba(79, 195, 247, 0.2);
            outline: none;
        }
        
        .btn {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border: none;
            padding: 14px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            width: 100%;
            transition: all 0.3s;
        }
        
        .btn:hover {
            opacity: 0.9;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        
        .message {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 6px;
            font-weight: 500;
            text-align: center;
        }
        
        .success {
            background-color: rgba(76, 175, 80, 0.1);
            color: var(--success-color);
            border: 1px solid rgba(76, 175, 80, 0.3);
        }
        
        .error {
            background-color: rgba(244, 67, 54, 0.1);
            color: var(--error-color);
            border: 1px solid rgba(244, 67, 54, 0.3);
        }
        
        .seller-id {
            background: var(--light-gray);
            padding: 10px 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        
        .seller-id strong {
            color: var(--secondary-color);
        }
        
        @media (max-width: 768px) {
            .form-row {
                flex-direction: column;
                gap: 0;
            }
            
            .form-container {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
<a href="seller_dashboard.php" class="btn mt-5">
    <i class="fas fa-arrow-left"></i> Back
</a>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-home"></i> Add New Property</h1>
            <p>Fill in the details of your property listing</p>
        </div>
        

        
        <div class="form-container">
        
            <?php if ($message && !$show_success_modal): ?>
                <div class="message <?php echo $message_type; ?>">
                    <?php if ($message_type == 'success'): ?>
                        <i class="fas fa-check-circle"></i> 
                    <?php else: ?>
                        <i class="fas fa-exclamation-circle"></i> 
                    <?php endif; ?>
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <div class="seller-id">
                <strong>Seller ID:</strong> <?php echo $seller_id; ?>
            </div>

            <form id="property-form" action="" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="title">Property Title</label>
                    <div class="input-icon">
                        <i class="fas fa-heading"></i>
                        <input type="text" id="title" name="title" placeholder="Beautiful 3-Bedroom Apartment in City Center" value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="location">Location</label>
                    <div class="input-icon">
                        <i class="fas fa-map-marker-alt"></i>
                        <input type="text" id="location" name="location" placeholder="123 Main Street, City, Country" value="<?php echo isset($_POST['location']) ? htmlspecialchars($_POST['location']) : ''; ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="description">Property Description</label>
                    <textarea id="description" name="description" placeholder="Describe your property in detail (features, amenities, nearby attractions, etc.)" required><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
                </div>

                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="price">Price (Rs)</label>
                            <div class="input-icon">
                                <i class="fas fa-tag"></i>
                                <input type="number" id="price" name="price" placeholder="50000" min="50000" step="1000" value="<?php echo isset($_POST['price']) ? htmlspecialchars($_POST['price']) : ''; ?>" required max="999999999">
                            </div>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="type">Property Type</label>
                            <div class="input-icon">
                                <i class="fas fa-building"></i>
                                <select id="type" name="type" required>
                                    <option value="apartment" <?php echo (isset($_POST['type']) && $_POST['type'] == 'apartment') ? 'selected' : ''; ?>>Apartment</option>
                                    <option value="house" <?php echo (isset($_POST['type']) && $_POST['type'] == 'house') ? 'selected' : ''; ?>>House</option>
                                    <option value="condo" <?php echo (isset($_POST['type']) && $_POST['type'] == 'condo') ? 'selected' : ''; ?>>Condo</option>
                                    <option value="villa" <?php echo (isset($_POST['type']) && $_POST['type'] == 'villa') ? 'selected' : ''; ?>>Villa</option>
                                    <option value="townhouse" <?php echo (isset($_POST['type']) && $_POST['type'] == 'townhouse') ? 'selected' : ''; ?>>Townhouse</option>
                                    <option value="studio" <?php echo (isset($_POST['type']) && $_POST['type'] == 'studio') ? 'selected' : ''; ?>>Studio</option>
                                    <option value="commercial" <?php echo (isset($_POST['type']) && $_POST['type'] == 'commercial') ? 'selected' : ''; ?>>Commercial Property</option>
                                    <option value="land" <?php echo (isset($_POST['type']) && $_POST['type'] == 'land') ? 'selected' : ''; ?>>Land</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="image1">Property Images</label>
                    <div class="form-row">
                        <div class="form-col">
                            <input type="file" id="image1" name="image1" accept="image/*" required>
                        </div>
                        <div class="form-col">
                            <input type="file" id="image2" name="image2" accept="image/*" required>
                        </div>
                        <div class="form-col">
                            <input type="file" id="image3" name="image3" accept="image/*" required>
                        </div>
                    </div>
                    <p class="file-hint">
                        <i class="fas fa-info-circle"></i> Upload exactly 3 images (JPEG, JPG, or PNG), each ≤ 16MB. First image will be used as the main display.
                    </p>
                </div>

                <button type="submit" class="btn">
                    <i class="fas fa-plus-circle"></i> Add Property
                </button>
            </form>
        </div>
    </div>

    
    <?php if ($show_success_modal): ?>
    <div class="modal" id="successModal" style="display: flex;">
        <div class="modal-content">
            <div class="modal-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <h2 class="modal-title">Success!</h2>
            <p class="modal-message">Your property has been added successfully.</p>
            <button class="modal-btn" onclick="window.location.href='seller_dashboard.php'">Back to Dashboard</button>
        </div>
    </div>
    <script>
        
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('successModal').style.display = 'flex';
        });
    </script>
    <?php endif; ?>
</body>
</html>