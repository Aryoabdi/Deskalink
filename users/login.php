<?php
require '../service/config.php';
session_start();

$login_message = "";

// Jika pengguna sudah login, arahkan ke halaman utama
if (isset($_SESSION["is_login"])) {
    header("location: index.php");
    exit();
}

// Proses login manual (Username/Email + Password)
if (isset($_POST['login'])) {
    $identifier = $_POST['identifier']; // Bisa username atau email
    $password = $_POST['password'];

    $hash_password = hash('sha256', $password);
    $sql = "SELECT * FROM users WHERE (username='$identifier' OR email='$identifier') AND password='$hash_password'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        
        // Simpan data user ke dalam sesi
        $_SESSION["is_login"] = true;
        $_SESSION["user_id"] = $data["user_id"];
        $_SESSION["username"] = $data["username"];
        $_SESSION["role"] = $data["role"];
        $_SESSION["full_name"] = $data["full_name"];

        // Redirect berdasarkan role
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

// Proses login dengan Google
$google_login_url = $client->createAuthUrl();

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
    <div class="flex bg-gray-800 rounded-xl shadow-lg overflow-hidden max-w-4xl w-full">
        <!-- Kiri: Gambar -->
        <div class="w-1/2 bg-cover bg-center" style="background-image: url('<?php echo "images/gambarlogin.jpg"; ?>');"></div>

        <!-- Kanan: Form Login -->
        <div class="w-1/2 p-8 text-white">
            <h2 class="text-3xl font-bold text-white mb-4">Welcome Back</h2>
            <p class="text-gray-400 mb-4">New here? <a href="register.php" class="text-blue-400 hover:underline">Create an account</a></p>
            
            <i class="text-red-500"><?= $login_message ?></i>

            <form action="" method="POST" class="mt-4">
                <input type="text" name="identifier" placeholder="Username atau Email" class="w-full px-4 py-2 mb-3 bg-gray-700 border border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required />
                <input type="password" name="password" placeholder="Password" class="w-full px-4 py-2 mb-3 bg-gray-700 border border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required />

                <button type="submit" name="login" class="w-full bg-green-500 text-white py-2 rounded-lg mt-4 hover:bg-green-600">Login</button>
            </form>

            <div class="mt-6 text-center">
                <a href="#" class="text-blue-400 hover:underline">Forgot password?</a>
            </div>

            <!-- Tombol Login dengan Google -->
            <div class="flex items-center my-4">
                <div class="flex-1 border-t border-gray-300"></div>
                <span class="px-4 text-gray-500">or</span>
                <div class="flex-1 border-t border-gray-300"></div>
            </div>

            <a href="<?= $google_login_url ?>" class="w-full flex items-center justify-center border border-gray-300 bg-white text-gray-700 py-2 rounded-lg hover:bg-gray-100">
                <img src="images/google-icon.png" alt="Google" class="w-5 h-5 mr-2">
                Login with Google
            </a>
        </div>
    </div>
</body>
</html>
