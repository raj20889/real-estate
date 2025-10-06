<?php
// Enable errors for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/classes/Seller.php';

$seller = new Seller();
$message = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];

    $result = $seller->register($name, $email, $phone, $password);
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
<title>Seller Registration</title>
<style>
    body {font-family: Arial, sans-serif; margin:0; padding:0; background: url('images/seller1.jpg') no-repeat center center/cover; display:flex; justify-content:center; align-items:center; height:100vh;}
    .form-container {background-color: rgba(255,255,255,0.95); padding:30px; border-radius:8px; box-shadow:0 0 15px rgba(0,0,0,0.2); max-width:400px; width:100%; box-sizing:border-box;}
    h2 {text-align:center; color:#333; margin-bottom:20px;}
    label {font-size:14px; color:#555; margin-bottom:8px; display:block;}
    input[type="text"], input[type="email"], input[type="tel"], input[type="password"] {width:100%; padding:12px; margin-bottom:15px; border:1px solid #ddd; border-radius:5px; font-size:14px;}
    button {background-color:#28a745; color:white; padding:12px 20px; border:none; border-radius:5px; cursor:pointer; font-size:16px; width:100%; transition:0.3s; margin-top:10px;}
    button:hover {background-color:#218838;}
    p {text-align:center; font-size:14px; color:#777; margin-top:15px;}
    a {color:#007bff; text-decoration:none;}
    a:hover {text-decoration:underline;}
    .password-requirements {text-align:left; font-size:13px; color:#555; margin:-10px 0 15px 0; padding:5px; background-color:#f8f9fa; border-radius:4px;}
    .password-requirements ul {margin:5px 0; padding-left:20px;}
    .phone-format {font-size:12px; color:#666; margin:-10px 0 10px 0;}
    .error {background-color:#fdecea; color:#d32f2f; padding:12px; text-align:center; border-radius:6px; margin-bottom:20px; border-left:4px solid #d32f2f; font-size:14px;}
</style>
</head>
<body>

<div class="form-container">
    <h2>Seller Registration</h2>

    <?php if ($message): ?>
        <div class="error"><?= $message ?></div>
    <?php endif; ?>

    <form action="" method="POST" id="registrationForm">
        <label for="name">Full Name:</label>
        <input type="text" id="name" name="name" required placeholder="Enter your full name">

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required placeholder="Enter your email">

        <label for="phone">Phone Number:</label>
        <input type="tel" id="phone" name="phone" pattern="\+[0-9]{1,3}[0-9]{4,14}" required placeholder="e.g., +919876543210">
        <div class="phone-format">Format: +[country code][number] (e.g., +1 for US, +91 for India)</div>

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

        <button type="submit">Register</button>
    </form>

    <p>Already have an account? <a href="sellerlogin.php">Login here</a></p>
</div>

<script>
document.getElementById('registrationForm').addEventListener('submit', function(e) {
    const password = document.getElementById('password').value;
    let isValid = true;
    let errorMessage = '';

    if (password.length < 8) { errorMessage += 'Password must be at least 8 characters long!\n'; isValid=false; }
    if (!/[A-Z]/.test(password)) { errorMessage += 'Password must contain at least one uppercase letter!\n'; isValid=false; }
    if (!/[a-z]/.test(password)) { errorMessage += 'Password must contain at least one lowercase letter!\n'; isValid=false; }
    if (!/[0-9]/.test(password)) { errorMessage += 'Password must contain at least one number!\n'; isValid=false; }
    if (!/[^A-Za-z0-9]/.test(password)) { errorMessage += 'Password must contain at least one special character!\n'; isValid=false; }

    if (!isValid) { alert('Password Requirements:\n\n'+errorMessage); e.preventDefault(); }
});
</script>

</body>
</html>
