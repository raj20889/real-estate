<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "real_state";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$seller_id = $_SESSION['seller_id']; 
$sql = "SELECT * FROM properties WHERE seller_id = '$seller_id'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seller Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background-image: url('https://images.unsplash.com/photo-1512917774080-9991f1c4c750');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            background-color: rgba(249, 250, 251, 0.9);
            background-blend-mode: overlay;
        }
        
        .property-card {
            transition: all 0.3s ease;
            backdrop-filter: blur(2px);
            background-color: rgba(255, 255, 255, 0.85);
        }
        
        .property-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            background-color: rgba(255, 255, 255, 0.95);
        }
        
        .dashboard-header {
            backdrop-filter: blur(5px);
            background-color: rgba(255, 255, 255, 0.8);
        }
        
        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .status-pending {
            background-color: #fef3c7;
            color: #92400e;
        }
        
        .status-approved {
            background-color: #d1fae5;
            color: #065f46;
        }
        
        .status-deactivated {
            background-color: #fee2e2;
            color: #991b1b;
        }
    </style>
</head>
<body class="min-h-screen p-4">

    <div class="dashboard-header max-w-6xl mx-auto p-6 rounded-xl shadow-sm mb-6">
        <h2 class="text-3xl font-bold text-gray-800 text-center">Seller Dashboard</h2>
        <p class="text-center text-gray-600 mt-2">Manage your property listings efficiently</p>

        <div class="flex justify-between mt-6">
            <a href="insert_property.php" class="flex items-center bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md transition-all">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                Add Property
            </a>
            <a href="user/logout.php" class="flex items-center bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-md transition-all">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z" clip-rule="evenodd" />
                </svg>
                Logout
            </a>
        </div>
    </div>

    <div class="max-w-6xl mx-auto">
        <h3 class="text-xl font-semibold text-gray-800 mb-4 px-2">Your Listed Properties</h3>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    // Determine status badge class and text
                    $statusClass = '';
                    $statusText = '';
                    switch ($row['status']) {
                        case 'pending':
                            $statusClass = 'status-pending';
                            $statusText = 'Pending Approval';
                            break;
                        case 'approved':
                            $statusClass = 'status-approved';
                            $statusText = 'Active';
                            break;
                        case 'deactivated':
                            $statusClass = 'status-deactivated';
                            $statusText = 'Deactivated';
                            break;
                    }
                    
                    echo "
                    <div class='property-card border border-gray-200 rounded-lg overflow-hidden shadow-sm'>
                        <div class='relative'>
                            <img src='{$row['image1']}' alt='Property Image' class='w-full h-48 object-cover'>
                            <span class='status-badge $statusClass absolute top-2 right-2'>
                                $statusText
                            </span>
                        </div>
                        <div class='p-4'>
                            <h3 class='text-lg font-semibold text-gray-800 mb-1'>{$row['title']}</h3>
                            <p class='text-sm text-gray-600 mb-2 flex items-center'>
                                <svg xmlns='http://www.w3.org/2000/svg' class='h-4 w-4 mr-1' fill='none' viewBox='0 0 24 24' stroke='currentColor'>
                                    <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z' />
                                    <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M15 11a3 3 0 11-6 0 3 3 0 016 0z' />
                                </svg>
                                {$row['location']}
                            </p>
                            <p class='text-green-600 font-bold text-lg mb-3'>₹" . number_format($row['price'], 2) . "</p>
                            <div class='flex justify-between'>
                                <a href='view_property.php?id={$row['id']}' class='flex-1 mr-2 bg-blue-500 hover:bg-blue-600 text-white text-center py-2 rounded-md text-sm transition-colors'>
                                    View
                                </a>
                                <a href='edit_property.php?id={$row['id']}' class='flex-1 mx-2 bg-yellow-500 hover:bg-yellow-600 text-white text-center py-2 rounded-md text-sm transition-colors'>
                                    Edit
                                </a>
                                <a href='delete_property_seller.php?id={$row['id']}' class='flex-1 ml-2 bg-red-500 hover:bg-red-600 text-white text-center py-2 rounded-md text-sm transition-colors' onclick='return confirm(\"Are you sure you want to delete this property?\");'>
                                    Delete
                                </a>
                            </div>
                        </div>
                    </div>";
                }
            } else {
                echo "<div class='col-span-3 text-center py-10'>
                    <svg xmlns='http://www.w3.org/2000/svg' class='h-12 w-12 mx-auto text-gray-400' fill='none' viewBox='0 0 24 24' stroke='currentColor'>
                        <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4' />
                    </svg>
                    <p class='text-gray-600 mt-3'>No properties listed yet. Add your first property to get started!</p>
                </div>";
            }
            ?>
        </div>
    </div>

</body>
</html>

<?php
$conn->close();
?>