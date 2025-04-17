<?php
require '../service/config.php';
session_start();

if (!isset($_SESSION['is_login'])) {
    header('Location: ../users/login.php');
    exit();
}

// Handle quantity updates
if (isset($_POST['update_quantity'])) {
    $item_id = $_POST['item_id'];
    $type = $_POST['type'];
    $new_quantity = $_POST['quantity'];

    foreach ($_SESSION['cart'] as &$item) {
        if ($item['item_id'] === $item_id && $item['type'] === $type) {
            $item['quantity'] = $new_quantity;
            break;
        }
    }
}

// Handle item removal
if (isset($_POST['remove_item'])) {
    $item_id = $_POST['item_id'];
    $type = $_POST['type'];
    foreach ($_SESSION['cart'] as $key => $item) {
        if ($item['item_id'] === $item_id && $item['type'] === $type) {
            unset($_SESSION['cart'][$key]);
            break;
        }
    }
    $_SESSION['cart'] = array_values($_SESSION['cart']); // Reindex array
}

// Calculate total
$total = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $total += $item['price'] * $item['quantity'];
    }
}

// Handle checkout
if (isset($_POST['checkout']) && !empty($_SESSION['cart'])) {
    $user_id = $_SESSION['user_id'];
    $order_id = uniqid('ORD');
    $order_date = date('Y-m-d H:i:s');
    $status = 'pending';

    // Create order
    $stmt = $conn->prepare("INSERT INTO orders (order_id, user_id, total_amount, status, order_date) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdss", $order_id, $user_id, $total, $status, $order_date);
    $stmt->execute();

    // Create order items
    $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price, item_type) VALUES (?, ?, ?, ?, ?)");
    foreach ($_SESSION['cart'] as $item) {
        $stmt->bind_param("ssids", $order_id, $item['item_id'], $item['quantity'], $item['price'], $item['type']);
        $stmt->execute();
    }

    // Clear cart
    unset($_SESSION['cart']);

    // Redirect to order confirmation
    header("Location: order-confirmation.php?order_id=" . $order_id);
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - Deskalink</title>
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
                        <div class="relative">
                            <button id="profileBtn" class="flex items-center space-x-2 text-white focus:outline-none">
                                <img src="<?php echo $_SESSION['profile_image'] ?? '../assets/default-avatar.png'; ?>" 
                                    class="w-8 h-8 rounded-full">
                                <span><?php echo $_SESSION['full_name']; ?></span>
                            </button>
                            <div id="dropdownMenu" class="absolute right-0 w-48 py-2 mt-2 bg-gray-800 rounded-lg shadow-xl hidden z-50">
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

    <script>
        // Dropdown toggle
        const profileBtn = document.getElementById('profileBtn');
        const dropdownMenu = document.getElementById('dropdownMenu');

        document.addEventListener('click', function(event) {
            const isClickInside = profileBtn.contains(event.target) || dropdownMenu.contains(event.target);
            
            if (isClickInside) {
                dropdownMenu.classList.toggle('hidden');
            } else {
                dropdownMenu.classList.add('hidden');
            }
        });
    </script>

    <!-- Cart Content -->
    <div class="max-w-6xl mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold text-white mb-8">Shopping Cart</h1>

        <?php if (empty($_SESSION['cart'])): ?>
            <div class="bg-gray-800 rounded-lg shadow-md p-6 text-center">
                <p class="text-gray-300 mb-4">Your cart is empty</p>
                <a href="index.php" class="inline-block bg-green-500 text-white px-6 py-2 rounded-lg hover:bg-green-600">
                    Continue Shopping
                </a>
            </div>
        <?php else: ?>
            <div class="bg-gray-800 rounded-lg shadow-md overflow-hidden">
                <!-- Cart Items -->
                <div class="divide-y divide-gray-700">
                    <?php foreach ($_SESSION['cart'] as $item): ?>
                        <div class="p-6 flex items-center">
                            <img src="<?php echo $item['image_url'] ?? '../assets/default-product.jpg'; ?>" 
                                 alt="<?php echo htmlspecialchars($item['name']); ?>"
                                 class="w-24 h-24 object-cover rounded-lg">
                            
                            <div class="ml-6 flex-1">
                                <h3 class="text-lg font-semibold text-white"><?php echo htmlspecialchars($item['name']); ?></h3>
                                <p class="text-gray-400">Type: <?php echo ucfirst($item['type']); ?></p>
                                <p class="text-gray-400">Rp <?php echo number_format($item['price'], 0, ',', '.'); ?></p>
                            </div>

                            <div class="flex items-center space-x-4">
                                <form method="POST" class="flex items-center space-x-2">
                                    <input type="hidden" name="item_id" value="<?php echo $item['item_id']; ?>">
                                    <input type="hidden" name="type" value="<?php echo $item['type']; ?>">
                                    <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1"
                                           class="w-16 px-2 py-1 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-green-500">
                                    <button type="submit" name="update_quantity" class="text-green-500 hover:text-green-600">
                                        <i class="fas fa-sync-alt"></i>
                                    </button>
                                </form>

                                <form method="POST" class="ml-4">
                                    <input type="hidden" name="item_id" value="<?php echo $item['item_id']; ?>">
                                    <input type="hidden" name="type" value="<?php echo $item['type']; ?>">
                                    <button type="submit" name="remove_item" class="text-red-500 hover:text-red-600">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Cart Summary -->
                <div class="bg-gray-700 p-6">
                    <div class="flex justify-between items-center mb-4">
                        <span class="text-lg font-semibold text-white">Total:</span>
                        <span class="text-2xl font-bold text-green-500">Rp <?php echo number_format($total, 0, ',', '.'); ?></span>
                    </div>

                    <form method="POST" class="flex justify-between">
                        <a href="index.php" class="bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-500">
                            Continue Shopping
                        </a>
                        <button type="submit" name="checkout" class="bg-green-500 text-white px-6 py-3 rounded-lg hover:bg-green-600">
                            Proceed to Checkout
                        </button>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html> 