<?php
include("../service/config.php");
session_start();

if (!isset($_SESSION['is_login']) || $_SESSION['role'] !== 'partner') {
    header("location: ../users/login.php");
    exit();
}

$partner_id = $_SESSION['user_id'];

// Ambil data portofolio
$result = $conn->query("SELECT * FROM portfolios WHERE partner_id = '$partner_id' ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Portofolio Saya</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="bg-gray-900 text-white flex">
<?php include("sidebar_partner.php"); ?>
<main class="flex-1 p-6 ml-64">
    <h1 class="text-3xl font-bold mb-6">Portofolio Saya</h1>

    <!-- Tombol Tambah Portofolio -->
    <button onclick="$('#portfolioModal').show()" class="bg-green-600 px-4 py-2 rounded hover:bg-green-700 mb-4">
        + Tambah Portofolio
    </button>

    <!-- Daftar Portofolio -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php while ($row = $result->fetch_assoc()) : ?>
        <div class="bg-gray-800 rounded shadow p-4">
            <h2 class="text-lg font-semibold text-green-400 mb-2"><?= htmlspecialchars($row['title']) ?></h2>
            <?php if ($row['image_url']) : ?>
                <img src="<?= $row['image_url'] ?>" alt="Thumbnail" class="w-full h-48 object-cover rounded mb-3">
            <?php endif; ?>
            <p class="text-sm text-gray-300 mb-2"><?= nl2br(htmlspecialchars($row['description'])) ?></p>
            <?php if ($row['document_url']) : ?>
                <a href="<?= $row['document_url'] ?>" target="_blank" class="text-blue-400 text-sm underline">Lihat Dokumen</a>
            <?php endif; ?>
        </div>
        <?php endwhile; ?>
    </div>

    <!-- Modal Tambah Portofolio -->
    <div id="portfolioModal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center hidden z-50">
        <div class="bg-gray-800 rounded-lg p-6 w-full max-w-lg">
            <h2 class="text-xl font-bold mb-4">Tambah Portofolio</h2>
            <form id="addPortfolioForm">
                <input type="text" name="title" placeholder="Judul" required class="w-full p-2 mb-3 bg-gray-700 rounded">
                <label class="block mb-1">Tipe Portofolio:</label>
                <select name="type" required class="w-full p-2 mb-3 bg-gray-700 rounded">
                    <option value="karya">Karya</option>
                    <option value="sertifikat">Sertifikat / Ijazah</option>
                    <option value="penghargaan">Penghargaan</option>
                    <option value="lainnya">Lainnya</option>
                </select>
                <textarea name="description" placeholder="Deskripsi" required class="w-full p-2 mb-3 bg-gray-700 rounded"></textarea>
                <input type="url" name="image_url" placeholder="URL Gambar (Opsional)" class="w-full p-2 mb-3 bg-gray-700 rounded">
                <input type="url" name="document_url" placeholder="URL Dokumen Sertifikat/Ijazah (Opsional)" class="w-full p-2 mb-3 bg-gray-700 rounded">
                <button type="submit" class="bg-green-600 px-4 py-2 rounded">Simpan</button>
                <button type="button" onclick="$('#portfolioModal').hide()" class="bg-gray-600 px-4 py-2 rounded ml-2">Batal</button>
            </form>
        </div>
    </div>
</main>

<script>
    $("#addPortfolioForm").submit(function(e) {
        e.preventDefault();
        $.post("save_portfolio.php", $(this).serialize(), function(res) {
            alert(res);
            location.reload();
        });
    });
</script>
</body>
</html>
