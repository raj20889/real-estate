<?php
session_start();
include 'db/connect.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    
    $query = "SELECT id, full_name, password_hash FROM admins WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $full_name, $hashed_password);
        $stmt->fetch();

        
        if (password_verify($password, $hashed_password)) {
            // Set session variables
            $_SESSION["admin_id"] = $id;
            $_SESSION["admin_name"] = $full_name;
            header("Location: admin_dashboard.php");
            exit;
        } else {
            $error = "Invalid email or password!";
        }
    } else {
        $error = "No account found with this email!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), 
                        url('images/adminlogin.jpg') no-repeat center center/cover;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        
        
        .back-btn {
            position: absolute;
            top: 25px;
            left: 25px;
            display: inline-flex;
            align-items: center;
            background-color: rgba(255, 255, 255, 0.9);
            color: #2c3e50;
            padding: 8px 15px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 500;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        
        .back-btn:hover {
            background-color: white;
            transform: translateX(-5px);
        }
        
        .back-btn svg {
            margin-right: 5px;
            width: 16px;
            height: 16px;
        }

        .form-container {
            background-color: rgba(255, 255, 255, 0.95);
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            width: 100%;
            max-width: 450px;
            backdrop-filter: blur(3px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        h2 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 25px;
            font-size: 28px;
        }
        
        label {
            color: #34495e;
            font-size: 14px;
            font-weight: 600;
            display: block;
            margin-bottom: 8px;
        }
        
        input {
            width: 100%;
            padding: 12px 15px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 15px;
            transition: all 0.3s;
        }
        
        input:focus {
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
            outline: none;
        }
        
        button {
            background-color: #3498db;
            color: white;
            padding: 14px;
            border: none;
            border-radius: 6px;
            width: 100%;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            margin-top: 15px;
            transition: all 0.3s;
        }
        
        button:hover {
            background-color: #2980b9;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .error {
            background-color: #fdecea;
            color: #d32f2f;
            padding: 12px;
            text-align: center;
            border-radius: 6px;
            margin-bottom: 20px;
            border-left: 4px solid #d32f2f;
            font-size: 14px;
        }
        
        .form-footer {
            text-align: center;
            font-size: 14px;
            color: #666;
            margin-top: 20px;
        }
        
        a {
            color: #3498db;
            text-decoration: none;
            font-weight: 500;
        }
        
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    
    <a href="index.php" class="back-btn">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor">
            <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/>
        </svg>
        Back to Home
    </a>

    <div class="form-container">
        <h2>Admin Login</h2>

        <?php if (isset($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form action="" method="POST">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required placeholder="Enter your admin email">

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required placeholder="Enter your password">

            <button type="submit">Login</button>
        </form>

        <div class="form-footer">
           
        </div>
    </div>
</body>
</html>