<?php
require '../service/config.php';
session_start();

if (!isset($_SESSION['is_login'])) {
    header('Location: ../users/login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle profile update
if (isset($_POST['update_profile'])) {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    
    // Update profile image if provided
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
        $target_dir = "../uploads/profiles/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $file_extension = strtolower(pathinfo($_FILES["profile_image"]["name"], PATHINFO_EXTENSION));
        $new_filename = $user_id . "_" . time() . "." . $file_extension;
        $target_file = $target_dir . $new_filename;
        
        if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file)) {
            $profile_image = $target_file;
            $sql = "UPDATE users SET full_name=?, email=?, phone_number=?, profile_image=? WHERE user_id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssss", $full_name, $email, $phone, $profile_image, $user_id);
        }
    } else {
        $sql = "UPDATE users SET full_name=?, email=?, phone_number=? WHERE user_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $full_name, $email, $phone, $user_id);
    }
    
    if ($stmt->execute()) {
        $_SESSION['full_name'] = $full_name;
        $_SESSION['email'] = $email;
        if (isset($profile_image)) {
            $_SESSION['profile_image'] = $profile_image;
        }
        $success_message = "Profile updated successfully!";
    } else {
        $error_message = "Failed to update profile.";
    }
}

// Fetch current user data
$sql = "SELECT * FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - Deskalink</title>
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
                    <!-- Dropdown Trigger -->
                    <div class="relative">
                        <button id="userDropdownBtn" class="flex items-center space-x-2 text-white focus:outline-none">
                            <img src="<?php echo $_SESSION['profile_image'] ?? '../assets/default-avatar.png'; ?>" 
                                class="w-8 h-8 rounded-full">
                            <span><?php echo $_SESSION['full_name']; ?></span>
                        </button>
                        <!-- Dropdown Menu -->
                        <div id="userDropdown" class="absolute right-0 w-48 py-2 mt-2 bg-gray-800 rounded-lg shadow-xl hidden z-50">
                            <a href="profile.php" class="block px-4 py-2 text-gray-300 hover:text-white hover:bg-gray-700">Profile</a>
                            <a href="orders.php" class="block px-4 py-2 text-gray-300 hover:text-white hover:bg-gray-700">My Orders</a>
                            <a href="../users/logout.php" class="block px-4 py-2 text-gray-300 hover:text-white hover:bg-gray-700">Logout</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Script to handle dropdown toggle -->
    <script>
        const userDropdownBtn = document.getElementById('userDropdownBtn');
        const userDropdown = document.getElementById('userDropdown');

        userDropdownBtn.addEventListener('click', function (e) {
            e.stopPropagation(); // prevent click from bubbling
            userDropdown.classList.toggle('hidden');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function (e) {
            if (!userDropdown.contains(e.target) && !userDropdownBtn.contains(e.target)) {
                userDropdown.classList.add('hidden');
            }
        });
    </script>


    <!-- Main Content -->
    <div class="max-w-6xl mx-auto px-4 py-8">
        <div class="bg-gray-800 rounded-lg shadow-lg p-6">
            <h2 class="text-2xl font-bold text-white mb-6">Edit Profile</h2>

            <?php if (isset($success_message)): ?>
                <div class="bg-green-500 text-white px-4 py-2 rounded-lg mb-4">
                    <?php echo $success_message; ?>
                </div>
            <?php endif; ?>

            <?php if (isset($error_message)): ?>
                <div class="bg-red-500 text-white px-4 py-2 rounded-lg mb-4">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST" enctype="multipart/form-data" class="space-y-6">
                <div class="flex items-center space-x-6">
                    <div class="shrink-0">
                        <img class="h-24 w-24 object-cover rounded-full"
                             src="<?php echo $user['profile_image'] ?? '../assets/default-avatar.png'; ?>"
                             alt="Current profile photo" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300">
                            Change Profile Photo
                        </label>
                        <input type="file" name="profile_image" accept="image/*"
                               class="mt-1 block w-full text-sm text-gray-300
                                      file:mr-4 file:py-2 file:px-4
                                      file:rounded-full file:border-0
                                      file:text-sm file:font-semibold
                                      file:bg-green-500 file:text-white
                                      hover:file:bg-green-600">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Full Name</label>
                    <input type="text" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>"
                           class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Email</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>"
                           class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Phone Number</label>
                    <input type="tel" name="phone" value="<?php echo htmlspecialchars($user['phone_number']); ?>"
                           class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>

                <div class="flex justify-end">
                    <button type="submit" name="update_profile"
                            class="bg-green-500 text-white px-6 py-2 rounded-lg hover:bg-green-600">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html> 