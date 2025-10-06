<?php
session_start();
include 'db/connect.php';

// Update status if admin changes it
if (isset($_POST['update_status'])) {
    $property_id = intval($_POST['property_id']);
    $new_status = mysqli_real_escape_string($conn, $_POST['status']);
    
    $stmt = $conn->prepare("UPDATE properties SET status=? WHERE id=?");
    $stmt->bind_param("si", $new_status, $property_id);
    
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Property status updated successfully!";
    } else {
        $_SESSION['error_message'] = "Error updating property status: " . $conn->error;
    }
    
    header("Location: manage-property.php?status=" . urlencode($new_status));
    exit();
}

// Fetch properties based on selected status
$status_filter = isset($_GET['status']) ? mysqli_real_escape_string($conn, $_GET['status']) : 'all';

// Corrected query joining with sellers table instead of users
$sql = "SELECT p.*, s.name as seller_name FROM properties p LEFT JOIN sellers s ON p.seller_id = s.id";
if ($status_filter != 'all') {
    $sql .= " WHERE p.status='$status_filter'";
}
$sql .= " ORDER BY p.created_at DESC";

$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Properties | Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        body {
            background-image: url("images/adminBG.jpg");
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
        }
        
        .status-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            border-radius: 9999px;
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
        
        .property-image {
            transition: transform 0.3s ease;
        }
        
        .property-image:hover {
            transform: scale(1.05);
        }
        
        .action-btn {
            transition: all 0.2s ease;
        }
        
        .action-btn:hover {
            transform: translateY(-1px);
        }
        
        .fade-in {
            animation: fadeIn 0.3s ease-in-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen">
        <!-- Admin Header -->
        <header class="bg-white shadow-sm">
            <div class="max-w-7xl mx-auto px-4 py-4 sm:px-6 lg:px-8 flex justify-between items-center">
                <h1 class="text-xl font-semibold text-gray-900">Admin Dashboard</h1>
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-600">Welcome, Admin</span>
                </div>
            </div>
        </header>
        <a href="admin_dashboard.php" class="inline-block px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded transition">
            ← Back
        </a>
        
        <!-- Main Content -->
        <main class="max-w-7xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
            <!-- Page Header -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Manage Properties</h2>
                    <p class="mt-1 text-sm text-gray-600">Review and manage all property listings</p>
                </div>
            </div>
            
            <!-- Status Messages -->
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="fade-in mb-6 rounded-md bg-green-50 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-check-circle h-5 w-5 text-green-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800"><?php echo $_SESSION['success_message']; ?></p>
                        </div>
                    </div>
                </div>
                <?php unset($_SESSION['success_message']); ?>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="fade-in mb-6 rounded-md bg-red-50 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-circle h-5 w-5 text-red-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-red-800"><?php echo $_SESSION['error_message']; ?></p>
                        </div>
                    </div>
                </div>
                <?php unset($_SESSION['error_message']); ?>
            <?php endif; ?>
            
            <!-- Filter Controls -->
            <div class="bg-white shadow rounded-lg p-4 mb-6">
                <div class="flex flex-wrap items-center gap-4">
                    <span class="text-sm font-medium text-gray-700">Filter by Status:</span>
                    <a href="?status=all" class="px-3 py-1 rounded-full text-sm <?php echo $status_filter === 'all' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800 hover:bg-gray-200'; ?>">
                        All Properties
                    </a>
                    <a href="?status=pending" class="px-3 py-1 rounded-full text-sm <?php echo $status_filter === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800 hover:bg-gray-200'; ?>">
                        Pending
                    </a>
                    <a href="?status=approved" class="px-3 py-1 rounded-full text-sm <?php echo $status_filter === 'approved' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800 hover:bg-gray-200'; ?>">
                        Approved
                    </a>
                    <a href="?status=deactivated" class="px-3 py-1 rounded-full text-sm <?php echo $status_filter === 'deactivated' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800 hover:bg-gray-200'; ?>">
                        Deactivated
                    </a>
                </div>
            </div>
            
            <!-- Properties Table -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Property</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Seller</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Posted</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php if (mysqli_num_rows($result) > 0): ?>
                                <?php $counter = 1; while ($row = mysqli_fetch_assoc($result)): ?>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo $counter++; ?></td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    <?php if (!empty($row['image1'])): ?>
                                                        <img class="h-10 w-10 rounded-md object-cover" src="<?php echo htmlspecialchars($row['image1']); ?>" alt="<?php echo htmlspecialchars($row['title']); ?>">
                                                    <?php else: ?>
                                                        <div class="h-10 w-10 rounded-md bg-gray-200 flex items-center justify-center">
                                                            <i class="fas fa-home text-gray-400"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($row['title']); ?></div>
                                                    <div class="text-sm text-gray-500"><?php echo htmlspecialchars($row['location']); ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?php echo htmlspecialchars($row['seller_name'] ?? 'Unknown Seller'); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            ₹<?php echo number_format($row['price'], 2); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <form method="POST" class="flex items-center space-x-2">
                                                <input type="hidden" name="property_id" value="<?php echo $row['id']; ?>">
                                                <select name="status" class="text-sm border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                                    <option value="pending" <?php echo $row['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                                    <option value="approved" <?php echo $row['status'] == 'approved' ? 'selected' : ''; ?>>Approved</option>
                                                    <option value="deactivated" <?php echo $row['status'] == 'deactivated' ? 'selected' : ''; ?>>Deactivated</option>
                                                </select>
                                                <button type="submit" name="update_status" class="text-sm px-2 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 action-btn">
                                                    <i class="fas fa-save"></i>
                                                </button>
                                            </form>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?php echo date('M j, Y', strtotime($row['created_at'])); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex justify-end space-x-2">
                                                <a href="delete_property.php?id=<?php echo $row['id']; ?>" class="text-red-600 hover:text-red-900 action-btn" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                                <a href="property_images.php?id=<?php echo $row['id']; ?>" class="text-purple-600 hover:text-purple-900 action-btn" title="View Images">
                                                    <i class="fas fa-images"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                                        No properties found
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <script>
        function confirmDelete(id) {
            if (confirm('Are you sure you want to delete this property? This action cannot be undone.')) {
                window.location.href = 'delete_property.php?id=' + id;
            }
        }
    </script>
</body>
</html>

<?php
mysqli_close($conn);
?>