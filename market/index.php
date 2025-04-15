<?php
require '../service/config.php';
session_start();

// Mengambil semua produk/jasa dari partner
$sql = "SELECT p.*, u.full_name as partner_name, u.profile_image as partner_image 
        FROM products p 
        JOIN users u ON p.partner_id = u.user_id 
        WHERE p.status = 'active'";

// Add search functionality
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = '%' . $_GET['search'] . '%';
    $sql .= " AND (p.name LIKE ? OR p.description LIKE ?)";
}

// Add category filter
if (isset($_GET['category']) && !empty($_GET['category'])) {
    $sql .= " AND p.category = ?";
}

$sql .= " ORDER BY p.created_at DESC";

$stmt = $conn->prepare($sql);

// Bind parameters if they exist
if (isset($_GET['search']) && !empty($_GET['search'])) {
    if (isset($_GET['category']) && !empty($_GET['category'])) {
        $stmt->bind_param("sss", $search, $search, $_GET['category']);
    } else {
        $stmt->bind_param("ss", $search, $search);
    }
} elseif (isset($_GET['category']) && !empty($_GET['category'])) {
    $stmt->bind_param("s", $_GET['category']);
}

$stmt->execute();
$result = $stmt->get_result();

// Get existing services from services table
$sql_services = "SELECT s.*, u.full_name as partner_name, u.profile_image as partner_image 
                 FROM services s 
                 JOIN users u ON s.partner_id = u.user_id 
                 WHERE s.status = 'approved'";

if (isset($_GET['search']) && !empty($_GET['search'])) {
    $sql_services .= " AND (s.title LIKE ? OR s.description LIKE ?)";
}

if (isset($_GET['category']) && $_GET['category'] == 'service') {
    $stmt_services = $conn->prepare($sql_services);
    if (isset($_GET['search']) && !empty($_GET['search'])) {
        $stmt_services->bind_param("ss", $search, $search);
    }
    $stmt_services->execute();
    $services_result = $stmt_services->get_result();
}

// Get existing designs from designs table
$sql_designs = "SELECT d.*, u.full_name as partner_name, u.profile_image as partner_image 
                FROM designs d 
                JOIN users u ON d.partner_id = u.user_id 
                WHERE d.status = 'approved'";

if (isset($_GET['search']) && !empty($_GET['search'])) {
    $sql_designs .= " AND (d.title LIKE ? OR d.description LIKE ?)";
}

if (isset($_GET['category']) && $_GET['category'] == 'service') {
    $stmt_designs = $conn->prepare($sql_designs);
    if (isset($_GET['search']) && !empty($_GET['search'])) {
        $stmt_designs->bind_param("ss", $search, $search);
    }
    $stmt_designs->execute();
    $designs_result = $stmt_designs->get_result();
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deskalink Market</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-900">
    <!-- Navigation -->
    <nav class="bg-gray-800 shadow-lg">
        <div class="max-w-6xl mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-8">
                    <a href="index.php" class="text-2xl font-bold text-white">Deskalink</a>
                    <div class="hidden md:flex items-center space-x-4">
                        <a href="index.php" class="text-gray-300 hover:text-white">Home</a>
                        <a href="categories.php" class="text-gray-300 hover:text-white">Categories</a>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="cart.php" class="text-gray-300 hover:text-white">
                        <i class="fas fa-shopping-cart"></i>
                        <?php
                        if(isset($_SESSION['cart']) && count($_SESSION['cart']) > 0) {
                            echo '<span class="bg-green-500 text-white rounded-full px-2 py-1 text-xs">' . count($_SESSION['cart']) . '</span>';
                        }
                        ?>
                    </a>
                    <?php if(isset($_SESSION['is_login'])): ?>
                        <div class="relative group">
                            <button class="flex items-center space-x-2 text-white">
                                <img src="<?php echo $_SESSION['profile_image'] ?? '../assets/default-avatar.png'; ?>" 
                                     class="w-8 h-8 rounded-full">
                                <span><?php echo $_SESSION['full_name']; ?></span>
                            </button>
                            <div class="absolute right-0 w-48 py-2 mt-2 bg-gray-800 rounded-lg shadow-xl hidden group-hover:block">
                                <a href="profile.php" class="block px-4 py-2 text-gray-300 hover:text-white hover:bg-gray-700">Profile</a>
                                <a href="orders.php" class="block px-4 py-2 text-gray-300 hover:text-white hover:bg-gray-700">My Orders</a>
                                <a href="../users/logout.php" class="block px-4 py-2 text-gray-300 hover:text-white hover:bg-gray-700">Logout</a>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="../users/login.php" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600">Login</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="max-w-6xl mx-auto px-4 py-8">
        <!-- Search and Filter -->
        <div class="mb-8">
            <form action="" method="GET" class="flex gap-4">
                <input type="text" name="search" placeholder="Search products or services..." 
                       class="flex-1 px-4 py-2 bg-gray-800 border border-gray-700 text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                <select name="category" class="px-4 py-2 bg-gray-800 border border-gray-700 text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                    <option value="">All Categories</option>
                    <option value="service">Services</option>
                    <option value="product">Products</option>
                </select>
                <button type="submit" class="bg-green-500 text-white px-6 py-2 rounded-lg hover:bg-green-600">
                    Search
                </button>
            </form>
        </div>

        <!-- Products Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php 
            // Display products
            while($row = $result->fetch_assoc()): 
            ?>
            <div class="bg-gray-800 rounded-lg shadow-md overflow-hidden">
                <img src="<?php echo $row['image_url'] ?? '../assets/default-product.jpg'; ?>" 
                     alt="<?php echo htmlspecialchars($row['name']); ?>"
                     class="w-full h-48 object-cover">
                <div class="p-4">
                    <div class="flex items-center space-x-2 mb-2">
                        <img src="<?php echo $row['partner_image'] ?? '../assets/default-avatar.png'; ?>" 
                             class="w-6 h-6 rounded-full">
                        <span class="text-sm text-gray-400"><?php echo htmlspecialchars($row['partner_name']); ?></span>
                    </div>
                    <h3 class="text-lg font-semibold mb-2 text-white"><?php echo htmlspecialchars($row['name']); ?></h3>
                    <p class="text-gray-400 text-sm mb-2"><?php echo substr(htmlspecialchars($row['description']), 0, 100) . '...'; ?></p>
                    <div class="flex justify-between items-center mt-4">
                        <span class="text-lg font-bold text-green-500">Rp <?php echo number_format($row['price'], 0, ',', '.'); ?></span>
                        <a href="detail.php?id=<?php echo $row['product_id']; ?>&type=product" 
                           class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                            View Details
                        </a>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>

            <?php 
            // Display services if category is service or not specified
            if (isset($services_result)):
            while($row = $services_result->fetch_assoc()): 
            ?>
            <div class="bg-gray-800 rounded-lg shadow-md overflow-hidden">
                <img src="<?php echo $row['thumbnail'] ?? '../assets/default-service.jpg'; ?>" 
                     alt="<?php echo htmlspecialchars($row['title']); ?>"
                     class="w-full h-48 object-cover">
                <div class="p-4">
                    <div class="flex items-center space-x-2 mb-2">
                        <img src="<?php echo $row['partner_image'] ?? '../assets/default-avatar.png'; ?>" 
                             class="w-6 h-6 rounded-full">
                        <span class="text-sm text-gray-400"><?php echo htmlspecialchars($row['partner_name']); ?></span>
                    </div>
                    <h3 class="text-lg font-semibold mb-2 text-white"><?php echo htmlspecialchars($row['title']); ?></h3>
                    <p class="text-gray-400 text-sm mb-2"><?php echo substr(htmlspecialchars($row['description']), 0, 100) . '...'; ?></p>
                    <div class="flex justify-between items-center mt-4">
                        <span class="text-lg font-bold text-green-500">Rp <?php echo number_format($row['price'], 0, ',', '.'); ?></span>
                        <a href="detail.php?id=<?php echo $row['service_id']; ?>&type=service" 
                           class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                            View Details
                        </a>
                    </div>
                </div>
            </div>
            <?php endwhile; endif; ?>

            <?php 
            // Display designs if category is service or not specified
            if (isset($designs_result)):
            while($row = $designs_result->fetch_assoc()): 
            ?>
            <div class="bg-gray-800 rounded-lg shadow-md overflow-hidden">
                <img src="<?php echo $row['thumbnail'] ?? '../assets/default-design.jpg'; ?>" 
                     alt="<?php echo htmlspecialchars($row['title']); ?>"
                     class="w-full h-48 object-cover">
                <div class="p-4">
                    <div class="flex items-center space-x-2 mb-2">
                        <img src="<?php echo $row['partner_image'] ?? '../assets/default-avatar.png'; ?>" 
                             class="w-6 h-6 rounded-full">
                        <span class="text-sm text-gray-400"><?php echo htmlspecialchars($row['partner_name']); ?></span>
                    </div>
                    <h3 class="text-lg font-semibold mb-2 text-white"><?php echo htmlspecialchars($row['title']); ?></h3>
                    <p class="text-gray-400 text-sm mb-2"><?php echo substr(htmlspecialchars($row['description']), 0, 100) . '...'; ?></p>
                    <div class="flex justify-between items-center mt-4">
                        <span class="text-lg font-bold text-green-500">Rp <?php echo number_format($row['price'], 0, ',', '.'); ?></span>
                        <a href="detail.php?id=<?php echo $row['design_id']; ?>&type=design" 
                           class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                            View Details
                        </a>
                    </div>
                </div>
            </div>
            <?php endwhile; endif; ?>
        </div>
    </div>
</body>
</html> 