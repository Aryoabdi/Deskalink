<?php
include("../service/config.php");
session_start();

// Cek apakah pengguna sudah login
// Proses login manual (Username/Email + Password)
if (isset($_POST['login'])) {
    $identifier = $_POST['identifier'];
    $password = $_POST['password'];

    $hash_password = hash('sha256', $password);
    $sql = "SELECT * FROM users WHERE (username='$identifier' OR email='$identifier') AND password='$hash_password'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        
        $_SESSION["is_login"] = true;
        $_SESSION["user_id"] = $data["user_id"];
        $_SESSION["username"] = $data["username"];
        $_SESSION["role"] = $data["role"];
        $_SESSION["full_name"] = $data["full_name"];

        // Jika role sudah ada, redirect ke dashboard sesuai role
        if ($data["role"] == "admin") {
            header("location: ../admin_dashboard/index.php");
        } elseif ($data["role"] == "partner") {
            header("location: ../partner_dashboard/index.php");
        } else {
            header("location: ../market/index.php");
        }
        exit();
    } else {
        $login_message = "Username/email atau password salah.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 flex justify-center items-center h-screen">
    <div class="bg-gray-800 text-white p-6 rounded-lg shadow-lg max-w-md w-full">
        <h2 class="text-2xl font-bold mb-4">Selamat Datang di DeskaLink</h2>

        <?php if (empty($user["role"])): ?>
            <p class="mb-3">Silakan pilih peran Anda:</p>
            <form action="" method="POST">
                <select name="role" class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 mb-3" required>
                    <option value="client">Client (Pembeli Jasa & Desain)</option>
                    <option value="partner">Partner (Menjual Jasa & Desain Digital)</option>
                </select>
                <button type="submit" class="w-full bg-green-500 text-white py-2 rounded-lg hover:bg-green-600">Simpan Role</button>
            </form>
        <?php else: ?>
            <p class="mb-4">Anda login sebagai <strong><?= ucfirst($user["role"]) ?></strong>.</p>
            <a href="logout.php" class="w-full bg-red-500 text-white py-2 px-4 rounded-lg hover:bg-red-600 inline-block text-center">Logout</a>
        <?php endif; ?>
    </div>
</body>
</html>
