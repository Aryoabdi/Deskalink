<?php
include("../service/config.php");
session_start();

$login_message = "";

if (isset($_SESSION["is_login"])) {
    header("location: index.php");
    exit();
}

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $hash_password = hash('sha256', $password);
    $sql = "SELECT * FROM users WHERE username='$username' AND password='$hash_password'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        $_SESSION["username"] = $data["username"];
        $_SESSION["is_login"] = true;
        header("location: index.php");
        exit();
    } else {
        $login_message = "Akun tidak ditemukan";
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 flex justify-center items-center h-screen">
    <div class="bg-gray-800 p-8 rounded-xl shadow-md text-center text-white w-96">
        <h2 class="text-2xl font-bold text-yellow-500 mb-4">Login to DeskaLink</h2>
        <i class="text-red-500"> <?= $login_message ?> </i>
        <form action="" method="POST" class="mt-4">
            <input type="text" name="username" placeholder="Username" class="w-full px-4 py-2 mb-3 bg-gray-700 border border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500 text-white" required />
            <input type="password" name="password" placeholder="Password" class="w-full px-4 py-2 mb-3 bg-gray-700 border border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500 text-white" required />
            <button type="submit" name="login" class="w-full bg-green-500 text-white py-2 rounded-lg hover:bg-green-600">Login</button>
        </form>

        <!-- Tombol Kembali -->
        <button onclick="history.back()" class="mt-4 w-full bg-gray-600 text-white py-2 rounded-lg hover:bg-gray-700">
            Kembali
        </button>
    </div>
</body>
</html>
