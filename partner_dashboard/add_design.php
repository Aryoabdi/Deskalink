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
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $thumbnail = mysqli_real_escape_string($conn, $_POST['thumbnail']);
    $status = 'pending';
    $file_url = mysqli_real_escape_string($conn, $_POST['file_url']);

    if (!$title || !$description || !$price || !$category || !$thumbnail || !$file_url) {
        echo "<script>alert('Semua field wajib diisi.'); window.history.back();</script>";
        exit();
    }

    // Generate design_id otomatis
    $result = mysqli_query($conn, "SELECT design_id FROM designs ORDER BY design_id DESC LIMIT 1");
    $lastId = 'dsg0000000'; // Default jika belum ada data
    if ($row = mysqli_fetch_assoc($result)) {
        $lastId = $row['design_id'];
    }

    // Ekstrak angka dari design_id terakhir dan tambahkan 1
    $number = intval(substr($lastId, 3)) + 1;
    $newId = 'dsg' . str_pad($number, 7, '0', STR_PAD_LEFT); // hasil: dsg0000001, dsg0000002, ...

    // Insert data dengan design_id baru
    $stmt = $conn->prepare("INSERT INTO designs (design_id, partner_id, title, description, price, category, thumbnail, file_url, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssissss", $newId, $partner_id, $title, $description, $price, $category, $thumbnail, $file_url, $status);

    if ($stmt->execute()) {
        // Setelah desain berhasil disimpan, tambahkan preview URLs
        $preview_urls = array_map('trim', explode(',', $_POST['preview_urls']));

        foreach ($preview_urls as $url) {
            if (!empty($url)) {
                $stmt_preview = $conn->prepare("INSERT INTO design_previews (design_id, image_url) VALUES (?, ?)");
                $stmt_preview->bind_param("ss", $newId, $url);
                $stmt_preview->execute();
            }
        }
        
        echo "<script>alert('Desain berhasil diunggah.'); window.location.href='my_designs.php';</script>";
    } else {
        echo "<script>alert('Gagal menambahkan desain.'); window.history.back();</script>";
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Desain</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-white flex">
<?php include("sidebar_partner.php"); ?>
<main class="flex-1 p-6 ml-64">
    <h1 class="text-3xl font-bold mb-6">Unggah Desain Digital</h1>
    <form method="POST" class="max-w-xl space-y-4">
        <input type="text" name="title" placeholder="Judul Desain" required class="w-full p-2 bg-gray-700 rounded">
        <textarea name="description" placeholder="Deskripsi Desain" required class="w-full p-2 bg-gray-700 rounded"></textarea>
        <input type="number" name="price" placeholder="Harga (Rp)" required class="w-full p-2 bg-gray-700 rounded">
        <input type="text" name="category" placeholder="Kategori" required class="w-full p-2 bg-gray-700 rounded">
        <input type="url" name="thumbnail" placeholder="URL Thumbnail, gunakan direct link" required class="w-full p-2 bg-gray-700 rounded">
        <textarea name="preview_urls" placeholder="Gunakan direct link, pisahkan URL gambar preview dengan koma" required class="w-full p-2 bg-gray-700 rounded"></textarea>
        <small class="text-gray-400">Contoh: https://i.postimg.cc/a.jpg, https://i.postimg.cc/b.jpg</small>
        <input type="url" name="file_url" placeholder="URL File Desain (Wajib)" required class="w-full p-2 bg-gray-700 rounded">
        <button type="submit" name="submit" class="bg-green-600 hover:bg-green-700 px-4 py-2 rounded">Unggah</button>
        <button type="button" onclick="window.location.href='my_designs.php'" class="bg-gray-600 hover:bg-gray-700 px-4 py-2 ml-2 rounded text-white">Batal</button>
    </form>
</main>
</body>
</html>
