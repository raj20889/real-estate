<?php
include 'db/connect.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "DELETE FROM sellers WHERE id = $id";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Seller deleted successfully'); window.location.href='manage-seller.php';</script>";
    } else {
        echo "Error deleting seller: " . $conn->error;
    }
}

$conn->close();
?>
