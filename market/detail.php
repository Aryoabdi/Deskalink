<?php
require '../service/config.php';
session_start();

if (!isset($_GET['id']) || !isset($_GET['type'])) {
    header('Location: index.php');
    exit();
}

$id = $_GET['id'];
$type = $_GET['type'];

// Prepare query based on item type
switch($type) {
    case 'product':
        $sql = "SELECT p.*, u.full_name as partner_name, u.profile_image as partner_image, u.description as partner_description 
                FROM products p 
                JOIN users u ON p.partner_id = u.user_id 
                WHERE p.product_id = ? AND p.status = 'active'";
        $id_field = 'product_id';
        $name_field = 'name';
        $image_field = 'image_url';
        break;
    case 'service':
        $sql = "SELECT s.*, u.full_name as partner_name, u.profile_image as partner_image, u.description as partner_description 
                FROM services s 
                JOIN users u ON s.partner_id = u.user_id 
                WHERE s.service_id = ? AND s.status = 'approved'";
        $id_field = 'service_id';
        $name_field = 'title';
        $image_field = 'thumbnail';
        break;
    case 'design':
        $sql = "SELECT d.*, u.full_name as partner_name, u.profile_image as partner_image, u.description as partner_description 
                FROM designs d 
                JOIN users u ON d.partner_id = u.user_id 
                WHERE d.design_id = ? AND d.status = 'approved'";
        $id_field = 'design_id';
        $name_field = 'title';
        $image_field = 'thumbnail';
        break;
    default:
        header('Location: index.php');
        exit();
}

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: index.php');
    exit();
}

$item = $result->fetch_assoc();

// Handle Add to Cart
if (isset($_POST['add_to_cart'])) {
    if (!isset($_SESSION['is_login'])) {
        header('Location: ../users/login.php');
        exit();
    }

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    $quantity = $_POST['quantity'] ?? 1;
    
    // Check if item already in cart
    $found = false;
    foreach ($_SESSION['cart'] as &$cart_item) {
        if ($cart_item['item_id'] === $id && $cart_item['type'] === $type) {
            $cart_item['quantity'] += $quantity;
            $found = true;
            break;
        }
    }

    if (!$found) {
        $_SESSION['cart'][] = [
            'item_id' => $id,
            'type' => $type,
            'name' => $item[$name_field],
            'price' => $item['price'],
            'quantity' => $quantity,
            'image_url' => $item[$image_field],
            'partner_id' => $item['partner_id']
        ];
    }

    header('Location: cart.php');
    exit();
}

// Get preview images for designs
$preview_images = [];
if ($type === 'design') {
    $preview_sql = "SELECT * FROM design_previews WHERE design_id = ?";
    $preview_stmt = $conn->prepare($preview_sql);
    $preview_stmt->bind_param("s", $id);
    $preview_stmt->execute();
    $preview_result = $preview_stmt->get_result();
    while ($row = $preview_result->fetch_assoc()) {
        $preview_images[] = $row['image_url'];
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($item[$name_field]); ?> - Deskalink</title>
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

    <!-- Item Detail -->
    <div class="max-w-6xl mx-auto px-4 py-8">
        <div class="bg-gray-800 rounded-lg shadow-lg overflow-hidden">
            <div class="md:flex">
                <!-- Item Images -->
                <div class="md:w-1/2">
                    <?php if ($type === 'design' && !empty($preview_images)): ?>
                        <div class="relative">
                            <img src="<?php echo $item[$image_field]; ?>" 
                                 alt="<?php echo htmlspecialchars($item[$name_field]); ?>"
                                 class="w-full h-96 object-cover">
                            <div class="mt-4 grid grid-cols-4 gap-2 p-4">
                                <?php foreach($preview_images as $preview): ?>
                                    <img src="<?php echo $preview; ?>" 
                                         alt="Preview" 
                                         class="w-full h-20 object-cover rounded cursor-pointer hover:opacity-75">
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <img src="<?php echo $item[$image_field]; ?>" 
                             alt="<?php echo htmlspecialchars($item[$name_field]); ?>"
                             class="w-full h-96 object-cover">
                    <?php endif; ?>
                </div>

                <!-- Item Info -->
                <div class="md:w-1/2 p-8">
                    <div class="flex items-center space-x-4 mb-4">
                        <img src="<?php echo $item['partner_image'] ?? '../assets/default-avatar.png'; ?>" 
                             class="w-12 h-12 rounded-full">
                        <div>
                            <h3 class="font-semibold text-white"><?php echo htmlspecialchars($item['partner_name']); ?></h3>
                            <p class="text-sm text-gray-400"><?php echo htmlspecialchars($item['partner_description'] ?? ''); ?></p>
                        </div>
                    </div>

                    <h1 class="text-3xl font-bold text-white mb-4"><?php echo htmlspecialchars($item[$name_field]); ?></h1>
                    <p class="text-gray-300 mb-6"><?php echo nl2br(htmlspecialchars($item['description'])); ?></p>
                    
                    <div class="mb-6">
                        <span class="text-3xl font-bold text-green-500">
                            Rp <?php echo number_format($item['price'], 0, ',', '.'); ?>
                        </span>
                    </div>

                    <form action="" method="POST" class="space-y-4">
                        <div class="flex items-center space-x-4">
                            <label class="text-gray-300">Quantity:</label>
                            <input type="number" name="quantity" value="1" min="1" 
                                   class="w-20 px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-green-500">
                        </div>

                        <button type="submit" name="add_to_cart" 
                                class="w-full bg-green-500 text-white px-6 py-3 rounded-lg hover:bg-green-600 transition-colors">
                            Add to Cart
                        </button>
                    </form>

                    <?php if ($type === 'design'): ?>
                        <div class="mt-6 p-4 bg-gray-700 rounded-lg">
                            <h4 class="text-white font-semibold mb-2">File Information:</h4>
                            <p class="text-gray-300">
                                This design includes:
                                <?php if ($item['file_url']): ?>
                                    <br>- Downloadable files after purchase
                                <?php endif; ?>
                                <br>- High resolution images
                                <br>- Usage license
                            </p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 