<?php
include("../service/config.php");
session_start();

if (!isset($_SESSION['is_login']) || $_SESSION['role'] !== 'partner') {
    header("location: ../users/login.php");
    exit();
}

$partner_id = $_SESSION['user_id'];

// Ambil data statistik
$count_services = $conn->query("SELECT COUNT(*) FROM services WHERE partner_id = '$partner_id'")->fetch_row()[0];
$count_designs = $conn->query("SELECT COUNT(*) FROM designs WHERE partner_id = '$partner_id'")->fetch_row()[0];
$count_pending = $conn->query("SELECT COUNT(*) FROM (
    SELECT service_id FROM services WHERE partner_id = '$partner_id' AND status = 'pending'
    UNION ALL
    SELECT design_id FROM designs WHERE partner_id = '$partner_id' AND status = 'pending'
) AS pending_contents")->fetch_row()[0];
$count_rejected = $conn->query("SELECT COUNT(*) FROM (
    SELECT service_id FROM services WHERE partner_id = '$partner_id' AND status = 'rejected'
    UNION ALL
    SELECT design_id FROM designs WHERE partner_id = '$partner_id' AND status = 'rejected'
) AS rejected_contents")->fetch_row()[0];
$count_approved = $conn->query("SELECT COUNT(*) FROM (
    SELECT service_id FROM services WHERE partner_id = '$partner_id' AND status = 'approved'
    UNION ALL
    SELECT design_id FROM designs WHERE partner_id = '$partner_id' AND status = 'approved'
) AS approved_contents")->fetch_row()[0];

// Riwayat moderasi terbaru
$logs = $conn->query("SELECT * FROM moderation_logs WHERE content_type IN ('service', 'design') AND content_id IN (
    SELECT service_id FROM services WHERE partner_id = '$partner_id'
    UNION
    SELECT design_id FROM designs WHERE partner_id = '$partner_id'
) ORDER BY created_at DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Partner</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-white flex">
<?php include("sidebar_partner.php"); ?>
<main class="flex-1 p-6 ml-64">
    <h1 class="text-3xl font-bold mb-6">Dashboard Partner</h1>

    <!-- Statistik -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
        <div class="bg-gray-800 p-4 rounded shadow">
            <h2 class="text-xl font-semibold text-green-400">Jasa Saya</h2>
            <p class="text-3xl mt-2"><?= $count_services ?></p>
        </div>
        <div class="bg-gray-800 p-4 rounded shadow">
            <h2 class="text-xl font-semibold text-green-400">Desain Digital</h2>
            <p class="text-3xl mt-2"><?= $count_designs ?></p>
        </div>
        <div class="bg-gray-800 p-4 rounded shadow">
            <h2 class="text-xl font-semibold text-yellow-400">Pending Moderasi</h2>
            <p class="text-3xl mt-2"><?= $count_pending ?></p>
        </div>
        <div class="bg-gray-800 p-4 rounded shadow">
            <h2 class="text-xl font-semibold text-red-400">Ditolak</h2>
            <p class="text-3xl mt-2"><?= $count_rejected ?></p>
        </div>
        <div class="bg-gray-800 p-4 rounded shadow">
            <h2 class="text-xl font-semibold text-blue-400">Disetujui</h2>
            <p class="text-3xl mt-2"><?= $count_approved ?></p>
        </div>
    </div>

    <!-- Akses Cepat -->
    <div class="flex flex-wrap gap-4 mb-8">
        <a href="my_services.php" class="bg-green-600 hover:bg-green-700 px-6 py-3 rounded text-white">+ Tambah Jasa Baru</a>
        <a href="my_designs.php" class="bg-blue-600 hover:bg-blue-700 px-6 py-3 rounded text-white">+ Unggah Desain Digital</a>
    </div>

    <!-- Riwayat Moderasi Terbaru -->
    <div class="bg-gray-800 rounded p-4">
        <h2 class="text-xl font-semibold mb-4">Riwayat Moderasi Terbaru</h2>
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-700">
                    <th class="p-2">Tanggal</th>
                    <th class="p-2">Konten</th>
                    <th class="p-2">Tipe</th>
                    <th class="p-2">Aksi</th>
                    <th class="p-2">Alasan</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($log = $logs->fetch_assoc()) : ?>
                    <tr class="border-b border-gray-700">
                        <td class="p-2"><?= $log['created_at'] ?></td>
                        <td class="p-2">
                        <?php
                            if ($log['content_type'] === 'service') {
                                $title_result = $conn->query("SELECT title FROM services WHERE service_id = '{$log['content_id']}'")->fetch_assoc();
                            } else {
                                $title_result = $conn->query("SELECT title FROM designs WHERE design_id = '{$log['content_id']}'")->fetch_assoc();
                            }
                            echo htmlspecialchars($title_result['title'] ?? 'Tidak ditemukan');
                        ?>
                        </td>
                        <td class="p-2"><?= ucfirst($log['content_type']) ?></td>
                        <td class="p-2 text-yellow-300"><?= ucfirst($log['action']) ?></td>
                        <td class="p-2"><?= $log['reason'] ?: '-' ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</main>
</body>
</html>
