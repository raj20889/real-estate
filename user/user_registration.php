<?php
include '../db/connect.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validation
    if (empty($name) || empty($email) || empty($phone) || empty($password) || empty($confirm_password)) {
        echo "<script>alert('Error: All fields are required!');</script>";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Error: Invalid email format!');</script>";
    } elseif (!preg_match('/^\+[0-9]{1,3}[0-9]{4,14}$/', $phone)) {
        echo "<script>alert('Error: Invalid phone number format! Must include country code (e.g., +1234567890)');</script>";
    } elseif ($password !== $confirm_password) {
        echo "<script>alert('Error: Passwords do not match!');</script>";
    } elseif (strlen($password) < 8) {
        echo "<script>alert('Error: Password must be at least 8 characters long!');</script>";
    } elseif (!preg_match('/[A-Z]/', $password)) {
        echo "<script>alert('Error: Password must contain at least one uppercase letter!');</script>";
    } elseif (!preg_match('/[a-z]/', $password)) {
        echo "<script>alert('Error: Password must contain at least one lowercase letter!');</script>";
    } elseif (!preg_match('/[0-9]/', $password)) {
        echo "<script>alert('Error: Password must contain at least one number!');</script>";
    } elseif (!preg_match('/[^A-Za-z0-9]/', $password)) {
        echo "<script>alert('Error: Password must contain at least one special character!');</script>";
    } else {
        
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // Check if email or phone already exists
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? OR phone = ?");
        $stmt->bind_param("ss", $email, $phone);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo "<script>alert('Error: Email or phone number already registered!');</script>";
        } else {
            // Insert new user
            $stmt = $conn->prepare("INSERT INTO users (name, email, phone, password) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $name, $email, $phone, $hashed_password);

            if ($stmt->execute()) {
                echo "<script>
                    alert('Success: Registration successful.');
                    window.location.href = 'user_login.php';
                </script>";
                exit();
            } else {
                echo "<script>alert('Error: Database Error - " . $conn->error . "');</script>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: url('../images/observation-urban-building-business-steel_1127-2397.avif') no-repeat center center/cover;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background: rgba(255, 255, 255, 0.9);
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            width: 100%;
            max-width: 450px;
            text-align: center;
            backdrop-filter: blur(8px);
            border: 2px solid rgba(0, 0, 0, 0.3);
            
        }
        h2 {
            color: #222;
            margin-bottom: 20px;
        }
        label {
            display: block;
            font-weight: bold;
            margin: 12px 0 6px;
            color: #222;
            text-align: left;
        }
        input {
            width: 100%;
            padding: 14px;
            border: 2px solid rgba(0, 0, 0, 0.4);
            border-radius: 6px;
            font-size: 15px;
            background: rgba(255, 255, 255, 0.95);
            color: #000;
            outline: none;
        }
        input::placeholder {
            color: rgba(0, 0, 0, 0.6);
        }
        input:focus {
            border-color: #3498db;
            box-shadow: 0 0 6px rgba(52, 152, 219, 0.7);
        }
        button {
            background-color: #3498db;
            color: white;
            padding: 14px;
            border: none;
            border-radius: 6px;
            width: 100%;
            font-size: 17px;
            cursor: pointer;
            transition: 0.3s;
            margin-top: 15px;
            font-weight: bold;
        }
        button:hover {
            background-color: #2980b9;
            transform: scale(1.05);
        }
        p {
            font-size: 15px;
            margin-top: 18px;
            color: #222;
        }
        a {
            color: #3498db;
            text-decoration: none;
            font-weight: bold;
        }
        a:hover {
            text-decoration: underline;
        }
        .password-requirements {
            text-align: left;
            font-size: 13px;
            color: #555;
            margin-top: 5px;
        }
        @media (max-width: 480px) {
    .container {
        padding: 20px;
        width: 95%;
    }
    
    input, button {
        padding: 10px;
    }
}
@media (min-width: 768px) {
    .form-row {
        display: flex;
        gap: 15px;
    }
    
    .form-row > div {
        flex: 1;
    }
}.container {
    overflow-y: auto;
    max-height: 90vh;
}
    </style>
</head>
<body>

    <div class="container ">
        <h2>User Registration</h2>
        <form action="" method="POST">
            <label for="name">Full Name:</label>
            <input type="text" id="name" name="name" required placeholder="Enter your full name">

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required placeholder="Enter your email">

            <label for="phone">Phone Number:</label>
            <input type="tel" id="phone" name="phone" pattern="\+[0-9]{1,3}[0-9]{4,14}" required 
                   placeholder="Enter phone with country code (e.g., +919876543210)">
            <div class="password-requirements">Format: +[country code][number] (e.g., +1 for US, +91 for India)</div>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required 
                   placeholder="Enter a password (min 8 characters)">
            <div class="password-requirements">
                Password must contain:
                <ul>
                    <li>At least 8 characters</li>
                    <li>At least one uppercase letter</li>
                    <li>At least one lowercase letter</li>
                    <li>At least one number</li>
                    <li>At least one special character</li>
                </ul>
            </div>

            <label for="confirm_password">Confirm Password:</label>
            <input type="password" id="confirm_password" name="confirm_password" required 
                   placeholder="Confirm your password">

            <button type="submit">Register</button>
        </form>

        <p>Already have an account? <a href="user_login.php">Login here</a></p>
    </div>

    <script>
       
        document.querySelector('form').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (password.length < 8) {
                alert('Password must be at least 8 characters long!');
                e.preventDefault();
                return;
            }
            
            if (!/[A-Z]/.test(password)) {
                alert('Password must contain at least one uppercase letter!');
                e.preventDefault();
                return;
            }
            
            if (!/[a-z]/.test(password)) {
                alert('Password must contain at least one lowercase letter!');
                e.preventDefault();
                return;
            }
            
            if (!/[0-9]/.test(password)) {
                alert('Password must contain at least one number!');
                e.preventDefault();
                return;
            }
            
            if (!/[^A-Za-z0-9]/.test(password)) {
                alert('Password must contain at least one special character!');
                e.preventDefault();
                return;
            }
            
            if (password !== confirmPassword) {
                alert('Passwords do not match!');
                e.preventDefault();
                return;
            }
        });
    </script>
</body>
</html>