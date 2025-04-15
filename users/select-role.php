<?php
require '../service/config.php';
session_start();

// Cek jika user belum login
if (!isset($_SESSION['is_login'])) {
    header('Location: login.php');
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['role'])) {
    $role = $_POST['role'];
    $user_id = $_SESSION['user_id'];
    
    // Update role user
    $stmt = $conn->prepare("UPDATE users SET role = ? WHERE user_id = ?");
    $stmt->bind_param("ss", $role, $user_id);
    
    if ($stmt->execute()) {
        $_SESSION['role'] = $role;
        
        // Redirect sesuai role
        if ($role == "admin") {
            header("location: ../admin_dashboard/index.php");
        } elseif ($role == "partner") {
            header("location: ../partner_dashboard/dashboard_partner.php");
        } else {
            header("location: ../market/index.php");
        }
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
    <title>Select Role - Deskalink</title>
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
    <div class="max-w-4xl mx-auto px-4 py-16">
        <div class="text-center mb-12">
            <h1 class="text-3xl font-bold text-white mb-4">Welcome to Deskalink!</h1>
            <p class="text-gray-400">Please select your role to continue</p>
        </div>

        <div class="grid md:grid-cols-2 gap-8">
            <!-- Client Role Card -->
            <form method="POST" class="group">
                <input type="hidden" name="role" value="client">
                <button type="submit" class="w-full bg-gray-800 rounded-xl p-6 text-left transition-all duration-300 hover:bg-gray-700 hover:shadow-xl hover:scale-105">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-user text-white text-xl"></i>
                        </div>
                        <div class="bg-green-500 bg-opacity-20 text-green-500 px-3 py-1 rounded-full text-sm">
                            Client
                        </div>
                    </div>
                    <h3 class="text-xl font-semibold text-white mb-2">Join as a Client</h3>
                    <p class="text-gray-400 text-sm">
                        Browse and purchase products, services, and designs from our talented partners.
                        Get access to exclusive deals and personalized recommendations.
                    </p>
                    <div class="mt-4 text-green-500 group-hover:translate-x-2 transition-transform">
                        Select <i class="fas fa-arrow-right ml-2"></i>
                    </div>
                </button>
            </form>

            <!-- Partner Role Card -->
            <form method="POST" class="group">
                <input type="hidden" name="role" value="partner">
                <button type="submit" class="w-full bg-gray-800 rounded-xl p-6 text-left transition-all duration-300 hover:bg-gray-700 hover:shadow-xl hover:scale-105">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-store text-white text-xl"></i>
                        </div>
                        <div class="bg-blue-500 bg-opacity-20 text-blue-500 px-3 py-1 rounded-full text-sm">
                            Partner
                        </div>
                    </div>
                    <h3 class="text-xl font-semibold text-white mb-2">Join as a Partner</h3>
                    <p class="text-gray-400 text-sm">
                        Showcase your products, services, and designs to a wide audience.
                        Manage your store and grow your business with our platform.
                    </p>
                    <div class="mt-4 text-blue-500 group-hover:translate-x-2 transition-transform">
                        Select <i class="fas fa-arrow-right ml-2"></i>
                    </div>
                </button>
            </form>
        </div>

        <div class="mt-8 text-center">
            <p class="text-gray-400 text-sm">
                You can change your role later in your profile settings
            </p>
        </div>
    </div>
</body>
</html> 