<?php
include 'db/connect.php';

$success_message = '';
$error_message = '';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM sellers WHERE id = $id";
    $result = $conn->query($sql);
    $seller = $result->fetch_assoc();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    $sql = "UPDATE sellers SET name='$name', email='$email', phone='$phone' WHERE id=$id";

    if ($conn->query($sql) === TRUE) {
        
        $seller['name'] = $name;
        $seller['email'] = $email;
        $seller['phone'] = $phone;
        $success_message = 'Seller updated successfully!';
    } else {
        $error_message = "Error updating seller: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Seller | Real Estate</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8fafc;
            background-image: url("images/adminlogin.jpg");
        }
        
        .form-container {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            border-radius: 12px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .form-container:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        
        .input-field {
            transition: all 0.3s ease;
            border: 1px solid #e2e8f0;
        }
        
        .input-field:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
        }
        
        .submit-btn {
            transition: all 0.3s ease;
            letter-spacing: 0.5px;
        }
        
        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }
        
        .cancel-btn {
            transition: all 0.3s ease;
        }
        
        .cancel-btn:hover {
            background-color: #f1f5f9;
            transform: translateY(-2px);
        }
        
        .section-title {
            position: relative;
            padding-bottom: 10px;
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
        
        .alert-success {
            animation: fadeIn 0.5s, fadeOut 0.5s 2.5s forwards;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes fadeOut {
            from { opacity: 1; transform: translateY(0); }
            to { opacity: 0; transform: translateY(-20px); }
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="w-full max-w-md">
        
            <?php if ($success_message): ?>
                <div class="alert-success fixed top-4 left-1/2 transform -translate-x-1/2 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 z-50">
                    <span class="block sm:inline"><?php echo $success_message; ?></span>
                </div>
            <?php endif; ?>
            
    
            <?php if ($error_message): ?>
                <div class="fixed top-4 left-1/2 transform -translate-x-1/2 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 z-50">
                    <span class="block sm:inline"><?php echo $error_message; ?></span>
                </div>
            <?php endif; ?>
            
        
            <div class="mb-6 flex items-center justify-between">
                <a href="manage-seller.php" class="flex items-center text-blue-600 hover:text-blue-800">
                    <i class="fas fa-arrow-left mr-2"></i>
                    <span>Back to manage</span>
                </a>
            </div>
            
        
            <div class="form-container bg-white p-8">
                <div class="text-center mb-8">
                    <i class="fas fa-user-edit text-4xl text-blue-500 mb-3"></i>
                    <h2 class="text-2xl font-bold text-gray-800 section-title inline-block">Edit Seller</h2>
                </div>
                
                <form method="POST" class="space-y-6">
                
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-user text-gray-400"></i>
                            </div>
                            <input type="text" id="name" name="name" value="<?= htmlspecialchars($seller['name']) ?>" 
                                   class="input-field pl-10 w-full px-4 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="Enter seller's full name" required>
                        </div>
                    </div>
                    
                
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-envelope text-gray-400"></i>
                            </div>
                            <input type="email" id="email" name="email" value="<?= htmlspecialchars($seller['email']) ?>" 
                                   class="input-field pl-10 w-full px-4 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="Enter seller's email" required>
                        </div>
                    </div>
                    
                
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-phone text-gray-400"></i>
                            </div>
                            <input type="text" id="phone" name="phone" value="<?= htmlspecialchars($seller['phone']) ?>" 
                                   class="input-field pl-10 w-full px-4 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="Enter seller's phone number" required>
                        </div>
                    </div>
                    
                
                    <div class="flex items-center justify-between pt-4">
                        <a href="manage-seller.php" class="cancel-btn px-6 py-3 rounded-lg border border-gray-300 text-gray-700 font-medium">
                            Cancel
                        </a>
                        <button type="submit" class="submit-btn px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700">
                            Update Seller <i class="fas fa-save ml-2"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>

        setTimeout(() => {
            const successAlert = document.querySelector('.alert-success');
            if (successAlert) {
                successAlert.style.display = 'none';
            }
        }, 3000);
    </script>
</body>
</html>

<?php
$conn->close();
?>