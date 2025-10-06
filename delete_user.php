<?php
include 'db/connect.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "DELETE FROM users WHERE id = $id";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('User deleted successfully'); window.location.href='manage-user.php';</script>";
    } else {
        echo "Error deleting user: " . $conn->error;
    }
}

$conn->close();
?>
