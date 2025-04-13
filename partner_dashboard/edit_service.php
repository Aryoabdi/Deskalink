<?php
include("../service/config.php");
session_start();

if (!isset($_SESSION['is_login']) || $_SESSION['role'] !== 'partner') {
    header("location: ../users/login.php");
    exit();
}

$partner_id = $_SESSION['user_id'];
$id = $_GET['id'];

$result = $conn->query("SELECT * FROM services WHERE service_id = '$id' AND partner_id = '$partner_id'");
$data = $result->fetch_assoc();

if (!$data) {
    echo "<script>alert('Data tidak ditemukan'); window.location.href='my_services.php';</script>";
    exit();
}

if (isset($_POST['submit'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $price = (int)$_POST['price'];
    $category = !empty($_POST['category']) ? mysqli_real_escape_string($conn, $_POST['category']) : null;
    $thumbnail = !empty($_POST['thumbnail']) ? mysqli_real_escape_string($conn, $_POST['thumbnail']) : null;

    if (empty($_POST['title']) || empty($_POST['description']) || empty($_POST['price']) || empty($_POST['thumbnail'])) {
        echo "<script>alert('Semua field harus diisi.'); window.history.back();</script>";
        exit();
    }
    
    $stmt = $conn->prepare("UPDATE services SET title=?, description=?, price=?, category=?, thumbnail=?, status='pending' WHERE service_id=? AND partner_id=?");
    $stmt->bind_param("ssissss", $title, $description, $price, $category, $thumbnail, $id, $partner_id);

    if ($stmt->execute()) {
        // Tambahkan ke moderation_logs
        $moderator_id = $_SESSION['user_id'];
        $content_type = 'service';
        $action = 'pending';
        $reason = 'Konten telah di-edit.';
    
        $stmt_log = $conn->prepare("INSERT INTO moderation_logs (content_id, content_type, moderator_id, action, reason) VALUES (?, ?, ?, ?, ?)");
        $stmt_log->bind_param("sssss", $id, $content_type, $moderator_id, $action, $reason);
        $stmt_log->execute();
    
        echo "<script>alert('Jasa berhasil diperbarui. Menunggu moderasi ulang.'); window.location.href='my_services.php';</script>";
        exit();
    } else {
        echo "<script>alert('Gagal memperbarui jasa.'); window.history.back();</script>";
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Jasa</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-white flex">
<?php include("sidebar_partner.php"); ?>
<main class="flex-1 p-6 ml-64">
    <h1 class="text-3xl font-bold mb-6">Edit Jasa</h1>
    <form method="POST" class="max-w-xl space-y-4">
        <input type="text" name="title" placeholder="Judul Jasa" value="<?= htmlspecialchars($data['title']) ?>" required class="w-full p-2 bg-gray-700 rounded">
        <textarea name="description" placeholder="Deskripsi Jasa" required class="w-full p-2 bg-gray-700 rounded"><?= htmlspecialchars($data['description']) ?></textarea>
        <input type="number" name="price" placeholder="Harga (Rp)" value="<?= $data['price'] ?>" required class="w-full p-2 bg-gray-700 rounded">
        <input type="text" name="category" placeholder="Kategori (opsional)" value="<?= htmlspecialchars($data['category']) ?>" class="w-full p-2 bg-gray-700 rounded">
        <input type="url" name="thumbnail" placeholder="URL Gambar Thumbnail, gunakan direct link" value="<?= htmlspecialchars($data['thumbnail']) ?>" placeholder="URL Gambar Thumbnail" required class="w-full p-2 bg-gray-700 rounded">
        <button type="submit" name="submit" class="bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded">Update</button>
        <button type="button" onclick="window.location.href='my_services.php'" class="bg-gray-600 hover:bg-gray-700 px-4 py-2 ml-2 rounded text-white">Batal</button>
    </form>
</main>
</body>
</html>
