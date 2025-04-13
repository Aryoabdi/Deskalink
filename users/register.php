<?php
include("../service/config.php");
session_start();

if (isset($_SESSION["is_login"])) {
    $username = $_SESSION["username"]; // pastikan username disimpan di session

    // Buat alert dan redirect dengan JavaScript
    echo "<script>
        alert('Anda telah terdaftar dan masuk sebagai \"$username\"');
    ";

    if ($_SESSION["role"] == "admin") {
        echo "window.location.href = '../admin_dashboard/index.php';";
    } elseif ($_SESSION["role"] == "partner") {
        echo "window.location.href = '../partner_dashboard/dashboard_partner.php';";
    } else {
        echo "window.location.href = '../market/index.php';";
    }

    echo "</script>";
    exit();
}

if (isset($_POST['register'])) {
    // Validasi Terms & Conditions
    if (!isset($_POST['terms'])) {
        echo "<script>alert('Anda harus menyetujui Terms & Conditions.'); window.location.href='register.php';</script>";
        exit();
    }

    // Mengamankan input dari SQL Injection
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone_number = mysqli_real_escape_string($conn, $_POST['phone_number']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $hash_password = hash('sha256', $password);
    $role = mysqli_real_escape_string($conn, $_POST['role']);

    // Cek apakah username atau email sudah digunakan
    $check_user = "SELECT * FROM users WHERE username='$username' OR email='$email'";
    $result = $conn->query($check_user);

    if ($result->num_rows > 0) {
        echo "<script>alert('Username atau email sudah digunakan, silakan ganti yang lain.'); window.location.href='register.php';</script>";
        exit();
    }

    // Generate user_id otomatis
    $query_max_id = "SELECT MAX(SUBSTRING(user_id, 5)) AS max_id FROM users";
    $result_max_id = $conn->query($query_max_id);
    $row = $result_max_id->fetch_assoc();
    $new_id_number = $row['max_id'] ? intval($row['max_id']) + 1 : 1;
    $user_id = 'user' . str_pad($new_id_number, 8, '0', STR_PAD_LEFT);

    // Masukkan data ke database
    $sql = "INSERT INTO users (user_id, full_name, username, email, phone_number, password, role) 
            VALUES ('$user_id', '$full_name', '$username', '$email', '$phone_number', '$hash_password', '$role')";

    if ($conn->query($sql)) {
        echo "<script>alert('Daftar akun berhasil, silakan login.'); window.location.href='login.php';</script>";
        exit();
    } else {
        echo "<script>alert('Daftar akun gagal, silakan coba lagi.'); window.location.href='register.php';</script>";
        exit();
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
    <title>Register</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 flex justify-center items-center h-screen">
    <div class="flex bg-gray-800 rounded-xl shadow-lg overflow-hidden max-w-4xl w-full">
        <!-- Kiri: Gambar dan Slogan -->
        <div class="w-1/2 bg-cover bg-center" style="background-image: url('<?php echo "images/gambarregis.jpg"; ?>');"></div>
        <!--
        <div class="w-1/2 bg-cover bg-center p-6 text-white flex flex-col justify-between" style="background-image: url('<?php echo "images/gambarregis.jpg"; ?>');">
            <h1 class="text-2xl font-bold">AMU</h1>
            <div class="mt-auto">
                <p class="text-lg font-semibold">Capturing Moments, Creating Memories</p>
            </div>
        </div>
        -->

        <!-- Kanan: Form Registrasi -->
        <div class="w-1/2 p-8 text-white">
            <h2 class="text-3xl font-bold text-white mb-4">Create an account</h2>
            <p class="text-gray-400 mb-4">Already have an account? 
                <a href="login.php" class="text-blue-400 hover:underline">Log in</a>
            </p>
            <form action="" method="POST">
                <input type="text" name="full_name" placeholder="Nama Lengkap" class="w-full px-4 py-2 mb-3 bg-gray-700 border border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required />
                <input type="text" name="username" placeholder="Username" class="w-full px-4 py-2 mb-3 bg-gray-700 border border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required />
                <input type="email" name="email" placeholder="Email" class="w-full px-4 py-2 mt-3 bg-gray-700 border border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required />
                <input type="text" name="phone_number" placeholder="Nomor Telepon" class="w-full px-4 py-2 mt-3 bg-gray-700 border border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required />
                <input type="password" name="password" placeholder="Password" class="w-full px-4 py-2 mt-3 bg-gray-700 border border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required />
                
                <!-- Pilihan Role -->
                <label class="block text-gray-300 mt-3">Daftar sebagai:</label>
                <select name="role" class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    <option value="client">Client (Pembeli Jasa & Desain)</option>
                    <option value="partner">Partner (Menjual Jasa & Desain Digital)</option>
                </select>
                
                <div class="flex items-center mt-4">
                <input type="checkbox" name="terms" required class="mr-2"> 
                <label class="text-gray-300">I agree to the 
                    <a href="terms.php" class="text-blue-400 hover:underline">Terms & Conditions</a>
                </label>
                </div>
                
                <button type="submit" name="register" class="w-full bg-green-500 text-white py-2 rounded-lg mt-4 hover:bg-green-600">Create account</button>
            </form>

            <!-- Tombol Lanjutkan dengan Google -->
            <div class="flex items-center my-4">
                <div class="flex-1 border-t border-gray-300"></div>
                <span class="px-4 text-gray-500">or</span>
                <div class="flex-1 border-t border-gray-300"></div>
            </div>

            <a href="<?= $google_login_url ?>" class="w-full flex items-center justify-center border border-gray-300 bg-white text-gray-700 py-2 rounded-lg hover:bg-gray-100">
                <img src="images/google-icon.png" alt="Google" class="w-5 h-5 mr-2">
                Continue with Google
            </a>
            <!--
            <div class="mt-6 flex justify-between gap-4">
                <button class="w-full flex items-center justify-center bg-gray-700 py-2 rounded-lg hover:bg-gray-600">
                    <img src="https://www.svgrepo.com/show/355037/google.svg" class="w-5 mr-2"> Google
                </button>
                <button class="w-full flex items-center justify-center bg-gray-700 py-2 rounded-lg hover:bg-gray-600">
                    <img src="https://www.svgrepo.com/show/303139/apple-black-logo.svg" class="w-5 mr-2"> Apple
                </button>
            </div>
            -->
        </div>
    </div>
</body>
</html>
