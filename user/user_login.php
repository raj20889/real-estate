<?php
include '../db/connect.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        echo "<script>alert('Error: Both fields are required!');</script>";
    } else {
        
        $stmt = $conn->prepare("SELECT id, name, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            $hashed_password = $row['password'];

            if (password_verify($password, $hashed_password)) {
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['user_name'] = $row['name'];
                echo "<script>
                    alert('Login Successful!');
                    window.location.href = 'user_dashboard.php';
                </script>";
                exit();
            } else {
                echo "<script>alert('Error: Incorrect Password!');</script>";
            }
        } else {
            echo "<script>alert('Error: Email not registered!');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Login</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), 
                        url('../images/wooden-sale-lettering-wooden-houses_23-2148346242.jpg') no-repeat center center/cover;
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
            padding: 10px 20px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 600;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            z-index: 10;
        }
        
        .back-btn:hover {
            background-color: white;
            transform: translateX(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }
        
        .back-btn svg {
            margin-right: 8px;
            width: 18px;
            height: 18px;
        }

        .container {
            background: rgba(255, 255, 255, 0.95);
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 450px;
            text-align: center;
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            position: relative;
        }
        
        h2 {
            color: #2c3e50;
            margin-bottom: 30px;
            font-size: 28px;
            font-weight: 600;
        }
        
        label {
            display: block;
            font-weight: 600;
            margin: 15px 0 8px;
            color: #34495e;
            text-align: left;
            font-size: 15px;
        }
        
        input {
            width: 100%;
            padding: 14px 18px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 15px;
            background: rgba(255, 255, 255, 0.95);
            color: #333;
            outline: none;
            transition: all 0.3s;
        }
        
        input::placeholder {
            color: #95a5a6;
        }
        
        input:focus {
            border-color: #3498db;
            box-shadow: 0 0 0 4px rgba(52, 152, 219, 0.2);
        }
        
        button {
            background-color: #3498db;
            color: white;
            padding: 15px;
            border: none;
            border-radius: 8px;
            width: 100%;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 20px;
            transition: all 0.3s;
        }
        
        button:hover {
            background-color: #2980b9;
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
        }
        
        .form-footer {
            font-size: 15px;
            margin-top: 25px;
            color: #555;
        }
        
        a {
            color: #3498db;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.2s;
        }
        
        a:hover {
            text-decoration: underline;
            color: #2980b9;
        }
    </style>
</head>
<body>
    
    <a href="../index.php" class="back-btn">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"/>
        </svg>
        Back to Home
    </a>

    <div class="container">
        <h2>User Login</h2>
        <form action="" method="POST">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required placeholder="Enter your email address">

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required placeholder="Enter your password">

            <button type="submit">Login</button>
        </form>

        <div class="form-footer">
            <p>Don't have an account? <a href="user_registration.php">Register here</a></p>
        </div>
    </div>

    <script>
    document.addEventListener("DOMContentLoaded", function () {
        document.querySelector("form").addEventListener("submit", function (event) {
            let email = document.getElementById("email").value.trim();
            let password = document.getElementById("password").value.trim();
            let emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            if (!email || !password) {
                alert("Error: Both fields are required!");
                event.preventDefault();
                return;
            }

            if (!emailPattern.test(email)) {
                alert("Error: Please enter a valid email address!");
                event.preventDefault();
                return;
            }

            if (password.length < 6) {
                alert("Error: Password must be at least 6 characters long!");
                event.preventDefault();
                return;
            }
        });
    });
</script>


</body>
</html>