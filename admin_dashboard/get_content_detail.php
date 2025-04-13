<?php
include("../service/config.php");
session_start();

if (!isset($_SESSION['is_login']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    exit("Akses ditolak");
}

$id = $_POST['id'] ?? '';
$type = $_POST['type'] ?? ''; // 'service' atau 'design'

if (!$id || !in_array($type, ['service', 'design'])) {
    http_response_code(400);
    exit("Permintaan tidak valid");
}

$table = $type === 'design' ? 'designs' : 'services';
$id_column = $type === 'design' ? 'design_id' : 'service_id';

// Ambil data konten
$sql = "SELECT c.*, u.username, u.full_name FROM $table c JOIN users u ON c.partner_id = u.user_id WHERE c.$id_column = '$id'";
$result = $conn->query($sql);

if ($result->num_rows === 0) {
    exit("Konten tidak ditemukan.");
}

$data = $result->fetch_assoc();

// Tampilkan detail konten
?>
<div>
    <p><strong>Judul:</strong> <?= htmlspecialchars($data['title']) ?></p>
    <p><strong>Partner:</strong> <?= htmlspecialchars($data['full_name']) ?> (@<?= $data['username'] ?>)</p>
    <p><strong>Kategori:</strong> <?= $data['category'] ?: 'Uncategorized' ?></p>
    <p><strong>Harga:</strong> Rp <?= number_format($data['price'], 0, ',', '.') ?></p>
    <p><strong>Status:</strong> <?= ucfirst($data['status']) ?></p>
    <p><strong>Deskripsi:</strong></p>
    <p class="bg-gray-700 p-2 rounded"><?= nl2br(htmlspecialchars($data['description'])) ?></p>
    <?php if (!empty($data['thumbnail'])): ?>
        <p class="mt-3"><strong>Preview:</strong><br><img src="<?= $data['thumbnail'] ?>" alt="Preview" class="max-w-full h-auto rounded"></p>
    <?php endif; ?>
    <?php if ($type === 'design' && !empty($data['file_url'])): ?>
        <p class="mt-3"><strong>File Desain:</strong> <a href="<?= $data['file_url'] ?>" class="text-blue-400 underline" target="_blank">Lihat / Unduh</a></p>
    <?php endif; ?>
</div>
