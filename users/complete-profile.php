<?php
include("../service/config.php");
session_start();

if (!isset($_SESSION['google_login'])) {
    header("location: login.php");
    exit();
}

$user_id = $_SESSION['google_login']['user_id'];

if (isset($_POST['submit'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $role = $_POST['role'];

    // Validasi: username tidak boleh sama
    $check = $conn->query("SELECT * FROM users WHERE username='$username'");
    if ($check->num_rows > 0) {
        $error = "Username sudah digunakan.";
    } else {
        $update = $conn->prepare("UPDATE users SET username=?, phone_number=?, role=? WHERE user_id=?");
        $update->bind_param("ssss", $username, $phone, $role, $user_id);
        $update->execute();

        // Set sesi login normal
        $_SESSION['is_login'] = true;
        $_SESSION['user_id'] = $user_id;
        $_SESSION['username'] = $username;
        $_SESSION['role'] = $role;

        if ($_SESSION["role"] == "admin") {
            header("location: ../admin_dashboard/index.php");
        } elseif ($_SESSION["role"] == "partner") {
            header("location: ../partner_dashboard/dashboard_partner.php");
        } else {
            header("location: ../market/index.php");
        }
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Lengkapi Profil</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-white flex items-center justify-center h-screen">
    <div class="bg-gray-800 p-6 rounded shadow w-full max-w-md">
        <h2 class="text-2xl font-bold mb-4">Lengkapi Profil Anda</h2>
        <?php if (isset($error)) echo "<p class='text-red-400 mb-3'>$error</p>"; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required class="w-full p-2 mb-3 bg-gray-700 rounded">
            <input type="text" name="phone" placeholder="Nomor Telepon" required class="w-full p-2 mb-3 bg-gray-700 rounded">
            <label class="block mb-1">Daftar sebagai:</label>
            <select name="role" required class="w-full p-2 mb-3 bg-gray-700 rounded">
                <option value="client">Client (Pembeli Jasa & Desain)</option>
                <option value="partner">Partner (Menjual Jasa & Desain Digital)</option>
            </select>
            <button type="submit" name="submit" class="w-full bg-green-600 py-2 rounded hover:bg-green-700">Simpan & Lanjut</button>
        </form>
    </div>
</body>
</html>
