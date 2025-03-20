<?php
include("../service/config.php");
session_start();

$register_message = "";

if (isset($_SESSION["is_login"])) {
    header("location: dashboard.php");
    exit();
}

if (isset($_POST['register'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $hash_password = hash('sha256', $password);

    $check_user = "SELECT * FROM users WHERE username='$username'";
    $result = $conn->query($check_user);
    
    if ($result->num_rows > 0) {
        $register_message = "Username sudah digunakan, silakan ganti yang lain";
    } else {
        $sql = "INSERT INTO users (username, password) VALUES ('$username', '$hash_password')";
        if ($conn->query($sql)) {
            $register_message = "Daftar akun berhasil, silakan login";
        } else {
            $register_message = "Daftar akun gagal, silakan coba lagi";
        }
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 flex justify-center items-center h-screen">
    <div class="bg-gray-800 p-8 rounded-xl shadow-md text-center text-white w-96">
        <h2 class="text-2xl font-bold text-yellow-500 mb-4">Register</h2>
        <i class="text-red-500"> <?= $register_message ?> </i>
        <form action="" method="POST" class="mt-4">
            <input type="text" name="username" placeholder="Username" class="w-full px-4 py-2 mb-3 bg-gray-700 border border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500 text-white" required />
            <input type="password" name="password" placeholder="Password" class="w-full px-4 py-2 mb-3 bg-gray-700 border border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500 text-white" required />
            <button type="submit" name="register" class="w-full bg-blue-500 text-white py-2 rounded-lg hover:bg-blue-600">Register</button>
        </form>

        <!-- Tombol Kembali -->
        <button onclick="history.back()" class="mt-4 w-full bg-gray-600 text-white py-2 rounded-lg hover:bg-gray-700">
            Kembali
        </button>
    </div>
</body>
</html>
