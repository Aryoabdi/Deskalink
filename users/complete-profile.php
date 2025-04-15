<?php
require '../service/config.php';
session_start();

// Cek jika user belum login
if (!isset($_SESSION['is_login'])) {
    header('Location: login.php');
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $phone_number = $_POST['phone_number'];
    $address = $_POST['address'] ?? '';
    $bio = $_POST['bio'] ?? '';
    
    // Update user profile
    $stmt = $conn->prepare("UPDATE users SET phone_number = ?, address = ?, bio = ?, is_profile_completed = 1 WHERE user_id = ?");
    $stmt->bind_param("ssss", $phone_number, $address, $bio, $user_id);
    
    if ($stmt->execute()) {
        $_SESSION['is_profile_completed'] = 1;
        
        // Redirect ke pemilihan role
        header("Location: select-role.php");
        exit();
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete Your Profile - Deskalink</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-900 min-h-screen">
    <div class="max-w-2xl mx-auto px-4 py-16">
        <div class="bg-gray-800 rounded-xl p-8">
            <div class="text-center mb-8">
                <div class="w-24 h-24 mx-auto mb-4">
                    <img src="<?php echo $_SESSION['profile_image']; ?>" 
                         alt="Profile" 
                         class="w-full h-full rounded-full object-cover">
                </div>
                <h1 class="text-2xl font-bold text-white mb-2">Complete Your Profile</h1>
                <p class="text-gray-400">Please provide the following information to continue</p>
            </div>

            <form method="POST" class="space-y-6">
                <!-- Phone Number -->
                <div>
                    <label for="phone_number" class="block text-sm font-medium text-gray-300 mb-2">
                        Phone Number <span class="text-red-500">*</span>
                    </label>
                    <input type="tel" 
                           id="phone_number" 
                           name="phone_number" 
                           required
                           pattern="[0-9]+"
                           class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-green-500"
                           placeholder="Enter your phone number">
                </div>

                <!-- Address -->
                <div>
                    <label for="address" class="block text-sm font-medium text-gray-300 mb-2">
                        Address
                    </label>
                    <textarea id="address" 
                              name="address"
                              rows="3"
                              class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-green-500"
                              placeholder="Enter your address"></textarea>
                </div>

                <!-- Bio -->
                <div>
                    <label for="bio" class="block text-sm font-medium text-gray-300 mb-2">
                        Bio
                    </label>
                    <textarea id="bio" 
                              name="bio"
                              rows="4"
                              class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-green-500"
                              placeholder="Tell us about yourself"></textarea>
                </div>

                <button type="submit" 
                        class="w-full bg-green-500 text-white py-3 rounded-lg font-medium hover:bg-green-600 transition-colors">
                    Continue
                </button>
            </form>
        </div>
    </div>
</body>
</html>
