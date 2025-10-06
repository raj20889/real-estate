<?php
include 'db/connect.php';


if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "DELETE FROM properties WHERE id=$id";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Property Deleted Successfull'); window.location.href='manage-property.php';</script>";
      
    } else {
        echo "Error deleting record: " . $conn->error;
    }
}
?>
