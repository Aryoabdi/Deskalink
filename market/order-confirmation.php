<?php
require '../service/config.php';
session_start();

if (!isset($_SESSION['is_login'])) {
    header('Location: ../users/login.php');
    exit();
}

if (!isset($_GET['order_id'])) {
    header('Location: index.php');
    exit();
}

$order_id = $_GET['order_id'];
$user_id = $_SESSION['user_id'];

// Fetch order details
$sql = "SELECT o.*, u.full_name, u.email, u.phone_number 
        FROM orders o 
        JOIN users u ON o.user_id = u.user_id 
        WHERE o.order_id = ? AND o.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $order_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: index.php');
    exit();
}

$order = $result->fetch_assoc();

// Fetch order items
$sql = "SELECT oi.*, 
        CASE 
            WHEN oi.item_type = 'product' THEN p.name 
            WHEN oi.item_type = 'service' THEN s.title
            WHEN oi.item_type = 'design' THEN d.title
        END as item_name,
        CASE 
            WHEN oi.item_type = 'product' THEN p.image_url
            WHEN oi.item_type = 'service' THEN s.thumbnail
            WHEN oi.item_type = 'design' THEN d.thumbnail
        END as item_image
        FROM order_items oi
        LEFT JOIN products p ON oi.product_id = p.product_id AND oi.item_type = 'product'
        LEFT JOIN services s ON oi.product_id = s.service_id AND oi.item_type = 'service'
        LEFT JOIN designs d ON oi.product_id = d.design_id AND oi.item_type = 'design'
        WHERE oi.order_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $order_id);
$stmt->execute();
$items_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation - Deskalink</title>
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
                </div>
                <div class="flex items-center space-x-4">
                    <a href="cart.php" class="text-gray-300 hover:text-white">
                        <i class="fas fa-shopping-cart"></i>
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
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Order Confirmation -->
    <div class="max-w-6xl mx-auto px-4 py-8">
        <div class="bg-gray-800 rounded-lg shadow-lg overflow-hidden">
            <div class="p-6">
                <div class="flex items-center justify-center mb-8">
                    <div class="bg-green-500 rounded-full p-3">
                        <i class="fas fa-check text-white text-2xl"></i>
                    </div>
                </div>

                <h1 class="text-2xl font-bold text-white text-center mb-2">Order Confirmed!</h1>
                <p class="text-gray-400 text-center mb-8">Thank you for your purchase. Your order has been received.</p>

                <!-- Order Details -->
                <div class="bg-gray-700 rounded-lg p-6 mb-8">
                    <h2 class="text-xl font-semibold text-white mb-4">Order Details</h2>
                    <div class="grid grid-cols-2 gap-4 text-gray-300">
                        <div>
                            <p class="mb-2"><span class="font-medium">Order ID:</span> <?php echo $order_id; ?></p>
                            <p class="mb-2"><span class="font-medium">Date:</span> <?php echo date('d M Y H:i', strtotime($order['order_date'])); ?></p>
                            <p class="mb-2"><span class="font-medium">Status:</span> 
                                <span class="bg-yellow-500 text-white px-2 py-1 rounded-full text-xs">
                                    <?php echo ucfirst($order['status']); ?>
                                </span>
                            </p>
                        </div>
                        <div>
                            <p class="mb-2"><span class="font-medium">Name:</span> <?php echo htmlspecialchars($order['full_name']); ?></p>
                            <p class="mb-2"><span class="font-medium">Email:</span> <?php echo htmlspecialchars($order['email']); ?></p>
                            <p class="mb-2"><span class="font-medium">Phone:</span> <?php echo htmlspecialchars($order['phone_number']); ?></p>
                        </div>
                    </div>
                </div>

                <!-- Order Items -->
                <div class="divide-y divide-gray-700">
                    <?php while($item = $items_result->fetch_assoc()): ?>
                        <div class="py-4 flex items-center">
                            <img src="<?php echo $item['item_image'] ?? '../assets/default-product.jpg'; ?>" 
                                 alt="<?php echo htmlspecialchars($item['item_name']); ?>"
                                 class="w-16 h-16 object-cover rounded-lg">
                            <div class="ml-4 flex-1">
                                <h3 class="text-white font-medium"><?php echo htmlspecialchars($item['item_name']); ?></h3>
                                <p class="text-gray-400">
                                    <?php echo ucfirst($item['item_type']); ?> × <?php echo $item['quantity']; ?>
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-green-500 font-semibold">
                                    Rp <?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?>
                                </p>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>

                <!-- Total -->
                <div class="border-t border-gray-700 mt-6 pt-6">
                    <div class="flex justify-between items-center">
                        <span class="text-xl font-semibold text-white">Total Amount:</span>
                        <span class="text-2xl font-bold text-green-500">
                            Rp <?php echo number_format($order['total_amount'], 0, ',', '.'); ?>
                        </span>
                    </div>
                </div>

                <!-- Actions -->
                <div class="mt-8 flex justify-center space-x-4">
                    <a href="orders.php" class="bg-green-500 text-white px-6 py-3 rounded-lg hover:bg-green-600">
                        View My Orders
                    </a>
                    <a href="index.php" class="bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-500">
                        Continue Shopping
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 