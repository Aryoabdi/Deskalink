<?php
session_start();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - Deskalink</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 flex items-center justify-center h-screen">

<div class="bg-gray-800 text-white p-6 rounded-lg shadow-lg max-w-md w-full">
    <h2 class="text-2xl font-bold mb-4">Lupa Password</h2>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="bg-red-500 bg-opacity-20 text-red-300 p-3 rounded-lg mb-4">
            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <form action="forgot_password_process.php" method="POST">
        <input type="text" name="username" class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 mb-4" placeholder="Masukkan Username" required>

        <button type="submit" class="w-full bg-green-500 text-white py-2 rounded-lg hover:bg-green-600">
            Kirim Kode
        </button>
    </form>

    <p class="text-center mt-4">
        <a href="login.php" class="text-blue-400 hover:underline">Kembali ke Login</a>
    </p>
</div>
</body>
</html>