<?php
session_start();
require '../service/config.php';

// Cek jika user belum terverifikasi OTP
if (!isset($_SESSION['reset_verified']) || $_SESSION['reset_verified'] !== true) {
    $_SESSION['error'] = "Harap verifikasi OTP terlebih dahulu.";
    header('Location: forgot_password.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $email = $_SESSION['reset_email'];
    
    if (empty($password) || empty($confirm_password)) {
        $_SESSION['error'] = "Semua field wajib diisi.";
        header('Location: new_password.php');
        exit;
    }
    
    if ($password !== $confirm_password) {
        $_SESSION['error'] = "Konfirmasi password tidak cocok.";
        header('Location: new_password.php');
        exit;
    }
    
    // Update password user menggunakan hash SHA-256 agar konsisten dengan register.php
    $hashed_password = hash('sha256', $password);
    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
    
    if (!$stmt) {
        $_SESSION['error'] = "Gagal menyiapkan query untuk update password.";
        header('Location: new_password.php');
        exit;
    }
    
    $stmt->bind_param("ss", $hashed_password, $email);
    
    if ($stmt->execute()) {
        // Hapus session reset password
        unset($_SESSION['reset_email']);
        unset($_SESSION['reset_verified']);
        
        $_SESSION['success'] = "Password berhasil diubah. Silakan login dengan password baru Anda.";
        header('Location: login.php');
        exit;
    } else {
        $_SESSION['error'] = "Gagal mengubah password.";
        header('Location: new_password.php');
        exit;
    }
    
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - DeskaLink</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 flex justify-center items-center h-screen">
    <div class="bg-gray-800 text-white p-6 rounded-lg shadow-lg max-w-md w-full">
        <h2 class="text-2xl font-bold mb-4">Buat Password Baru</h2>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="bg-red-500 text-white p-3 rounded-lg mb-4">
                <?php 
                    echo $_SESSION['error']; 
                    unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>
        
        <form action="new_password.php" method="post">
            <div class="mb-4">
                <label for="password" class="block mb-2">Password Baru:</label>
                <input type="password" name="password" id="password" class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>
            
            <div class="mb-4">
                <label for="confirm_password" class="block mb-2">Konfirmasi Password Baru:</label>
                <input type="password" name="confirm_password" id="confirm_password" class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>
            
            <button type="submit" class="w-full bg-green-500 text-white py-2 rounded-lg hover:bg-green-600 mb-3">Simpan Password Baru</button>
        </form>
    </div>
</body>
</html>