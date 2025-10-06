<?php
include 'db/connect.php';



$sql = "SELECT id, name, email, phone FROM sellers";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Sellers</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f4f4f4;
            background-image: url("images/adminBG.jpg");
        }
        .container {
            max-width: 900px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        h2 {
            color: #333;
        }
        .btn {
            display: inline-block;
            padding: 8px 12px;
            font-size: 14px;
            color: white;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            margin: 5px;
            cursor: pointer;
        }
        .edit-btn { background: #28a745; }
        .delete-btn { background: #dc3545; }
        .back-btn { background: #6c757d; }
        .edit-btn:hover { background: #1e7e34; }
        .delete-btn:hover { background: #b52b3b; }
        .back-btn:hover { background: #5a6268; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th { background-color: #007bff; color: white; }
        tr:hover { background-color: #f1f1f1; }
    </style>
</head>
<body>

<div class="container">
    <h2>Manage Sellers</h2>
    <p>Here you can view and manage all registered sellers.</p>

    <a href="admin_dashboard.php" class="btn back-btn">Back</a>

    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Actions</th>
        </tr>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['id']}</td>
                        <td>{$row['name']}</td>
                        <td>{$row['email']}</td>
                        <td>{$row['phone']}</td>
                        <td>
                            <a href='edit_seller.php?id={$row['id']}' class='btn edit-btn'>Edit</a>
                            <a href='delete_seller.php?id={$row['id']}' class='btn delete-btn' onclick='return confirm(\"Are you sure?\")'>Delete</a>
                        </td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='5'>No sellers found</td></tr>";
        }
        $conn->close();
        ?>
    </table>
</div>

</body>
</html>
