<?php
require_once 'classes/Admin.php';

$admin = new Admin();
$message = null;
$message_type = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $phone = trim($_POST["phone"]);
    $password = trim($_POST["password"]);
    $confirm_password = trim($_POST["confirm-password"]);

    $result = $admin->register($full_name, $email, $phone, $password, $confirm_password);

    $message = $result['message'];
    $message_type = $result['status'] ? 'success' : 'error';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Registration</title>
<style>
    body {
        font-family: Arial, sans-serif;
        background: url('images/admin.jpg') no-repeat center center/cover;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        margin: 0;
    }
    .form-container {
        background-color: rgba(255,255,255,0.9);
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        width: 100%;
        max-width: 450px;
    }
    h2 {text-align:center; color:#2c3e50;}
    label {color:#34495e; font-weight:bold; display:block; margin-top:10px;}
    input {width:100%; padding:10px; margin-top:5px; border:1px solid #ccc; border-radius:4px; box-sizing:border-box;}
    input:focus {border-color:#3498db; box-shadow:0 0 5px rgba(52,152,219,0.5);}
    button {background-color:#3498db; color:white; padding:12px 20px; border:none; border-radius:4px; width:100%; font-size:16px; cursor:pointer; margin-top:15px;}
    button:hover {background-color:#2980b9;}
    p {text-align:center; font-size:14px; margin-top:15px;}
    a {color:#3498db; text-decoration:none;}
    a:hover {text-decoration:underline;}
    .message {text-align:center; font-size:14px; padding:10px; border-radius:5px; margin-bottom:15px;}
    .error { background-color:#ffdddd; color:#a94442; }
    .success { background-color:#ddffdd; color:#4caf50; }
</style>
</head>
<body>
<div class="form-container">
    <h2>Admin Registration</h2>

    <?php if ($message): ?>
        <p class="message <?= $message_type ?>"><?= $message ?></p>
    <?php endif; ?>

    <form action="" method="POST">
        <label for="name">Full Name:</label>
        <input type="text" id="name" name="name" required placeholder="Enter your full name">

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required placeholder="Enter your email">

        <label for="phone">Phone Number:</label>
        <input type="tel" id="phone" name="phone" pattern="[0-9]{10}" required placeholder="Enter 10-digit phone number">

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required placeholder="Enter a password">

        <label for="confirm-password">Confirm Password:</label>
        <input type="password" id="confirm-password" name="confirm-password" required placeholder="Confirm your password">

        <button type="submit">Register</button>
    </form>

    <p>Already have an account? <a href="adminlogin.php">Login here</a></p>
</div>
</body>
</html>
