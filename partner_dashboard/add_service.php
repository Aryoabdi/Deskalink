<?php
include("../service/config.php");
session_start();

if (!isset($_SESSION['is_login']) || $_SESSION['role'] !== 'partner') {
    header("location: ../users/login.php");
    exit();
}

if (isset($_POST['submit'])) {
    $partner_id = $_SESSION['user_id'];
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $price = (int)$_POST['price'];
    $category = !empty($_POST['category']) ? mysqli_real_escape_string($conn, $_POST['category']) : null;
    $status = 'pending';
    $thumbnail = !empty($_POST['thumbnail']) ? mysqli_real_escape_string($conn, $_POST['thumbnail']) : null;

    if (empty($_POST['title']) || empty($_POST['description']) || empty($_POST['price']) || empty($_POST['thumbnail'])) {
        echo "<script>alert('Semua field harus diisi.'); window.history.back();</script>";
        exit();
    }

    // Ambil service_id terakhir
    $result = mysqli_query($conn, "SELECT service_id FROM services ORDER BY service_id DESC LIMIT 1");
    $lastId = 'srv0000000'; // default jika belum ada data
    if ($row = mysqli_fetch_assoc($result)) {
        $lastId = $row['service_id'];
    }

    // Ambil angka dari ID terakhir dan tambah 1
    $number = intval(substr($lastId, 3)) + 1;
    $newId = 'srv' . str_pad($number, 7, '0', STR_PAD_LEFT); // hasil: srv0000001, srv0000002, ...

    // Insert data dengan ID baru
    $stmt = $conn->prepare("INSERT INTO services (service_id, partner_id, title, description, price, category, status, thumbnail) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssisss", $newId, $partner_id, $title, $description, $price, $category, $status, $thumbnail);

    if ($stmt->execute()) {
        echo "<script>alert('Jasa berhasil ditambahkan.'); window.location.href='my_services.php';</script>";
    } else {
        echo "<script>alert('Gagal menambahkan jasa.'); window.history.back();</script>";
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Jasa</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-white flex">
<?php include("sidebar_partner.php"); ?>
<main class="flex-1 p-6 ml-64">
    <h1 class="text-3xl font-bold mb-6">Tambah Jasa Baru</h1>
    <form method="POST" class="max-w-xl space-y-4">
        <input type="text" name="title" placeholder="Judul Jasa" required class="w-full p-2 bg-gray-700 rounded">
        <textarea name="description" placeholder="Deskripsi Jasa" required class="w-full p-2 bg-gray-700 rounded"></textarea>
        <input type="number" name="price" placeholder="Harga (Rp)" required class="w-full p-2 bg-gray-700 rounded">
        <input type="text" name="category" placeholder="Kategori (opsional)" class="w-full p-2 bg-gray-700 rounded">
        <input type="url" name="thumbnail" placeholder="URL Gambar Thumbnail, gunakan direct link" required class="w-full p-2 bg-gray-700 rounded">
        <button type="submit" name="submit" class="bg-green-600 hover:bg-green-700 px-4 py-2 rounded">Tambah</button>
        <button type="button" onclick="window.location.href='my_services.php'" class="bg-gray-600 hover:bg-gray-700 px-4 py-2 ml-2 rounded text-white">Batal</button>
    </form>
</main>
</body>
</html>
