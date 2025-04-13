<?php
include("../service/config.php");
session_start();

if (!isset($_SESSION['is_login']) || $_SESSION['role'] !== 'partner') {
    header("location: ../users/login.php");
    exit();
}

$partner_id = $_SESSION['user_id'];
$result = $conn->query("SELECT * FROM services WHERE partner_id = '$partner_id' ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jasa Saya</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="bg-gray-900 text-white flex">
<?php include("sidebar_partner.php"); ?>
<main class="flex-1 p-6 ml-0 md:ml-64">
    <div class="container mx-auto px-4">
        <h1 class="text-3xl font-bold mb-6">Jasa Saya</h1>

        <!-- Tombol Tambah Jasa -->
        <a href="add_service.php" class="bg-green-600 px-4 py-2 rounded hover:bg-green-700 mb-4 inline-block">
            + Tambah Jasa Baru
        </a>

        <!-- Daftar Jasa -->
        <div class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-4 gap-3">
            <?php while ($row = $result->fetch_assoc()) : ?>
            <div class="w-full max-w-xs bg-gray-800 rounded shadow p-4">
                <?php if ($row['thumbnail']) : ?>
                    <img src="<?= $row['thumbnail'] ?>" alt="Thumbnail" class="w-full h-40 object-cover rounded mb-3">
                <?php endif; ?>
                <h2 class="text-lg font-semibold text-green-400 mb-2"><?= htmlspecialchars($row['title']) ?></h2>
                <p class="text-sm text-gray-300 mb-1">Kategori: <?= $row['category'] ?: 'Uncategorized' ?></p>
                <p class="text-sm text-gray-400 mb-1">Status: <span class="<?= $row['status'] === 'approved' ? 'text-green-400' : ($row['status'] === 'pending' ? 'text-yellow-400' : 'text-red-400') ?>"><?= ucfirst($row['status']) ?></span></p>
                <p class="text-sm text-white font-bold">Rp<?= number_format($row['price'], 0, ',', '.') ?></p>
                <div class="flex gap-2 mt-3">
                    <a href="edit_service.php?id=<?= $row['service_id'] ?>" class="bg-blue-500 px-3 py-1 rounded text-sm">Edit</a>
                    <button onclick="deleteService(<?= $row['service_id'] ?>)" class="bg-red-500 px-3 py-1 rounded text-sm">Hapus</button>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</main>

<script>
function deleteService(id) {
    if (confirm("Yakin ingin menghapus jasa ini?")) {
        $.post("delete_service.php", { id }, function(res) {
            alert(res);
            location.reload();
        });
    }
}
</script>
</body>
</html>
