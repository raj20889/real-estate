<?php
// Enable errors for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../classes/User.php';

$user = new User();
$message = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    $result = $user->register($name, $email, $phone, $password, $confirm_password);
    if ($result['status']) {
        echo "<script>
            alert('" . $result['message'] . "');
            window.location.href='" . $result['redirect'] . "';
        </script>";
        exit();
    } else {
        $message = $result['message'];
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
        /* Keep your previous styling as-is */
        body { font-family: Arial, sans-serif; background: url('../images/observation-urban-building-business-steel_1127-2397.avif') no-repeat center center/cover; display:flex; justify-content:center; align-items:center; height:100vh; margin:0;}
        .container {background: rgba(255,255,255,0.9); padding:40px; border-radius:12px; box-shadow:0 10px 30px rgba(0,0,0,0.5); width:100%; max-width:450px; text-align:center; backdrop-filter: blur(8px); border:2px solid rgba(0,0,0,0.3); overflow-y:auto; max-height:90vh;}
        h2 {color:#222; margin-bottom:20px;}
        label {display:block; font-weight:bold; margin:12px 0 6px; color:#222; text-align:left;}
        input {width:100%; padding:14px; border:2px solid rgba(0,0,0,0.4); border-radius:6px; font-size:15px; background: rgba(255,255,255,0.95); color:#000; outline:none;}
        input::placeholder {color: rgba(0,0,0,0.6);}
        input:focus {border-color:#3498db; box-shadow:0 0 6px rgba(52,152,219,0.7);}
        button {background-color:#3498db; color:white; padding:14px; border:none; border-radius:6px; width:100%; font-size:17px; cursor:pointer; transition:0.3s; margin-top:15px; font-weight:bold;}
        button:hover {background-color:#2980b9; transform:scale(1.05);}
        p {font-size:15px; margin-top:18px; color:#222;}
        a {color:#3498db; text-decoration:none; font-weight:bold;}
        a:hover {text-decoration:underline;}
        .password-requirements {text-align:left; font-size:13px; color:#555; margin-top:5px;}
        .error {background-color:#fdecea; color:#d32f2f; padding:12px; text-align:center; border-radius:6px; margin-bottom:20px; border-left:4px solid #d32f2f; font-size:14px;}
    </style>
</head>
<body>
<div class="container">
    <h2>User Registration</h2>

    <?php if ($message): ?>
        <div class="error"><?= $message ?></div>
    <?php endif; ?>

    <form action="" method="POST">
        <label for="name">Full Name:</label>
        <input type="text" id="name" name="name" required placeholder="Enter your full name">

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required placeholder="Enter your email">

        <label for="phone">Phone Number:</label>
        <input type="tel" id="phone" name="phone" pattern="\+[0-9]{1,3}[0-9]{4,14}" required placeholder="Enter phone with country code (e.g., +919876543210)">
        <div class="password-requirements">Format: +[country code][number] (e.g., +1 for US, +91 for India)</div>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required placeholder="Enter a password (min 8 characters)">
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
        <input type="password" id="confirm_password" name="confirm_password" required placeholder="Confirm your password">

        <button type="submit">Register</button>
    </form>

    <p>Already have an account? <a href="user_login.php">Login here</a></p>
</div>

<script>
document.querySelector('form').addEventListener('submit', function(e) {
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    if(password !== confirmPassword) { alert('Passwords do not match!'); e.preventDefault(); }
});
</script>
</body>
</html>
