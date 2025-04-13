<?php
include("../service/config.php");
session_start();

if (!isset($_SESSION['is_login']) || $_SESSION['role'] !== 'partner') {
    header("location: ../users/login.php");
    exit();
}

$partner_id = $_SESSION['user_id'];
$id = $_GET['id'];

$result = $conn->query("SELECT * FROM designs WHERE design_id = '$id' AND partner_id = '$partner_id'");
$data = $result->fetch_assoc();

if (!$data) {
    echo "<script>alert('Data tidak ditemukan'); window.location.href='my_designs.php';</script>";
    exit();
}

$preview_res = $conn->query("SELECT image_url FROM design_previews WHERE design_id = '$id'");
$preview_urls = [];
while ($p = $preview_res->fetch_assoc()) {
    $preview_urls[] = $p['image_url'];
}

if (isset($_POST['submit'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $price = (int)$_POST['price'];
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $thumbnail = mysqli_real_escape_string($conn, $_POST['thumbnail']);
    $file_url = mysqli_real_escape_string($conn, $_POST['file_url']);

    if (!$title || !$description || !$price || !$category || !$thumbnail || !$file_url) {
        echo "<script>alert('Semua field wajib diisi.'); window.history.back();</script>";
        exit();
    }

    $stmt = $conn->prepare("UPDATE designs SET title=?, description=?, price=?, category=?, thumbnail=?, file_url=?, status='pending' WHERE design_id=? AND partner_id=?");
    $stmt->bind_param("ssisssss", $title, $description, $price, $category, $thumbnail, $file_url, $id, $partner_id);

    if ($stmt->execute()) {
        // Hapus preview lama
        $conn->query("DELETE FROM design_previews WHERE design_id = '$id'");
    
        // Simpan ulang preview baru
        $preview_urls = array_map('trim', explode(',', $_POST['preview_urls']));
        foreach ($preview_urls as $url) {
            if (!empty($url)) {
                $stmt_preview = $conn->prepare("INSERT INTO design_previews (design_id, image_url) VALUES (?, ?)");
                $stmt_preview->bind_param("ss", $id, $url);
                $stmt_preview->execute();
            }
        }
    
        // Tambahkan ke moderation_logs
        $moderator_id = $_SESSION['user_id'];
        $content_type = 'design';
        $action = 'pending';
        $reason = 'Konten telah di-edit.';

        $stmt_log = $conn->prepare("INSERT INTO moderation_logs (content_id, content_type, moderator_id, action, reason) VALUES (?, ?, ?, ?, ?)");
        $stmt_log->bind_param("sssss", $id, $content_type, $moderator_id, $action, $reason);
        $stmt_log->execute();

        echo "<script>alert('Desain berhasil diperbarui. Menunggu moderasi ulang.'); window.location.href='my_designs.php';</script>";
        exit();
    } else {
        echo "<script>alert('Gagal memperbarui desain.'); window.history.back();</script>";
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Desain</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-white flex">
<?php include("sidebar_partner.php"); ?>
<main class="flex-1 p-6 ml-64">
    <h1 class="text-3xl font-bold mb-6">Edit Desain Digital</h1>
    <form method="POST" class="max-w-xl space-y-4">
        <input type="text" name="title" placeholder="Judul Desain" value="<?= htmlspecialchars($data['title']) ?>" required class="w-full p-2 bg-gray-700 rounded">
        <textarea name="description" placeholder="Deskripsi Desain" required class="w-full p-2 bg-gray-700 rounded"><?= htmlspecialchars($data['description']) ?></textarea>
        <input type="number" name="price" placeholder="Harga (Rp)" value="<?= $data['price'] ?>" required class="w-full p-2 bg-gray-700 rounded">
        <input type="text" name="category" placeholder="Kategori" value="<?= htmlspecialchars($data['category']) ?>" required class="w-full p-2 bg-gray-700 rounded">
        <input type="url" name="thumbnail" placeholder="URL Thumbnail, gunakan direct link" value="<?= htmlspecialchars($data['thumbnail']) ?>" required class="w-full p-2 bg-gray-700 rounded">
        <textarea name="preview_urls" placeholder="Gunakan direct link, pisahkan URL gambar preview dengan koma" required class="w-full p-2 bg-gray-700 rounded"><?= htmlspecialchars(implode(', ', $preview_urls)) ?></textarea>
        <small class="text-gray-400">Contoh: https://i.postimg.cc/a.jpg, https://i.postimg.cc/b.jpg</small>
        <input type="url" name="file_url" placeholder="URL File Desain (Wajib)" value="<?= htmlspecialchars($data['file_url']) ?>" required class="w-full p-2 bg-gray-700 rounded">
        <button type="submit" name="submit" class="bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded">Update</button>
        <button type="button" onclick="window.location.href='my_designs.php'" class="bg-gray-600 hover:bg-gray-700 px-4 py-2 ml-2 rounded text-white">Batal</button>
</main>
</body>
</html>
