<?php
include("../service/config.php");
session_start();

if (!isset($_SESSION["is_login"]) || $_SESSION["role"] !== "admin") {
    header("location: ../users/login.php");
    exit();
}

$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'jasa';
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';

$table = $active_tab === 'desain' ? 'designs' : 'services';
$id_column = $active_tab === 'desain' ? 'design_id' : 'service_id';

$query = "SELECT c.*, u.username FROM $table c JOIN users u ON c.partner_id = u.user_id WHERE 1=1";
if ($status_filter) {
    $query .= " AND c.status = '$status_filter'";
}
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen Konten - DeskaLink</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="bg-gray-900 text-white flex">
<?php include("sidebar.php"); ?>
<main class="flex-1 p-6 ml-64">
    <h1 class="text-3xl font-bold mb-6">Manajemen <?= $active_tab === 'desain' ? 'Desain Digital' : 'Jasa' ?></h1>

    <!-- Tombol Riwayat Moderasi -->
    <button onclick="$('#logModal').show()" class="bg-blue-600 px-4 py-2 rounded mb-4 hover:bg-blue-700">
        Lihat Riwayat Moderasi
    </button>

    <!-- Tab Navigasi -->
    <div class="mb-4 space-x-2">
        <a href="?tab=jasa" class="px-4 py-2 rounded <?= $active_tab === 'jasa' ? 'bg-green-500' : 'bg-gray-700' ?>">Jasa</a>
        <a href="?tab=desain" class="px-4 py-2 rounded <?= $active_tab === 'desain' ? 'bg-green-500' : 'bg-gray-700' ?>">Desain Digital</a>
    </div>

    <!-- Filter Status -->
    <div class="mb-4">
        <label class="mr-2">Filter Status:</label>
        <select onchange="location = this.value" class="bg-gray-700 p-2 rounded">
            <option value="?tab=<?= $active_tab ?>">Semua</option>
            <?php
            $statusList = ['pending', 'approved', 'rejected', 'banned'];
            foreach ($statusList as $status) {
                $selected = $status_filter === $status ? 'selected' : '';
                echo "<option value='?tab=$active_tab&status=$status' $selected>" . ucfirst($status) . "</option>";
            }
            ?>
        </select>
    </div>

    <!-- Tabel Konten -->
    <table class="w-full bg-gray-800 rounded-lg">
        <thead class="bg-gray-700">
        <tr>
            <th class="p-2">No</th>
            <th class="p-2">ID</th>
            <th class="p-2">Judul</th>
            <th class="p-2">Partner</th>
            <th class="p-2">Kategori</th>
            <th class="p-2">Harga</th>
            <th class="p-2">Thumbnail</th>
            <th class="p-2">Status</th>
            <th class="p-2">Aksi</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $no = 1;
        while ($row = $result->fetch_assoc()) {
            $kategori = $row['category'] ?: 'Uncategorized';
            $thumbnail = $row['thumbnail'] ? "<img src='{$row['thumbnail']}' alt='Preview' class='w-12 h-12 rounded'>" : '-';
        ?>
            <tr class="border-b border-gray-700">
                <td class="p-2"><?= $no ?></td>
                <td class="p-2"><?= $row[$id_column] ?></td>
                <td class="p-2"><?= $row['title'] ?></td>
                <td class="p-2"><?= $row['username'] ?></td>
                <td class="p-2"><?= $kategori ?></td>
                <td class="p-2">Rp <?= number_format($row['price'], 0, ',', '.') ?></td>
                <td class="p-2"><?= $thumbnail ?></td>
                <td class="p-2"><?= ucfirst($row['status']) ?></td>
                <td class="p-2 space-y-1">
                    <button class="bg-blue-500 px-2 py-1 rounded text-sm w-full" onclick="showDetail('<?= $row[$id_column] ?>', '<?= $active_tab === 'desain' ? 'design' : 'service' ?>')">Detail</button>
                    <button class="bg-green-500 px-2 py-1 rounded text-sm w-full" onclick="showApproveModal('<?= $row[$id_column] ?>', '<?= $active_tab === 'desain' ? 'design' : 'service' ?>')">Approve</button>
                    <button class="bg-yellow-500 px-2 py-1 rounded text-sm w-full" onclick="showRejectModal('<?= $row[$id_column] ?>', '<?= $active_tab === 'desain' ? 'design' : 'service' ?>')">Tolak</button>
                    <button class="bg-red-600 px-2 py-1 rounded text-sm w-full" onclick="showDeleteModal('<?= $row[$id_column] ?>', '<?= $active_tab === 'desain' ? 'design' : 'service' ?>')">Hapus</button>
                </td>
            </tr>
        <?php
            $no++;
        }
        ?>
        </tbody>
    </table>

    <!-- Modal Riwayat Moderasi -->
    <div id="logModal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center hidden z-50">
        <div class="bg-gray-800 rounded-lg w-[800px] max-h-[90vh] overflow-y-auto p-6">
            <h2 class="text-xl font-bold mb-4 text-blue-400">Riwayat Moderasi</h2>
            <table class="w-full bg-gray-700 rounded">
                <thead>
                    <tr>
                        <th class="p-2">Waktu</th>
                        <th class="p-2">Konten</th>
                        <th class="p-2">Tipe</th>
                        <th class="p-2">moderator</th>
                        <th class="p-2">Aksi</th>
                        <th class="p-2">Alasan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $log_query = "
                        SELECT l.*, u.username AS moderator_name 
                        FROM moderation_logs l 
                        JOIN users u ON l.moderator_id = u.user_id 
                        ORDER BY l.created_at DESC
                    ";
                    $log_result = $conn->query($log_query);
                    while ($log = $log_result->fetch_assoc()) {
                        echo "<tr class='border-b border-gray-600'>
                            <td class='p-2'>" . $log['created_at'] . "</td>
                            <td class='p-2'>" . $log['content_id'] . "</td>
                            <td class='p-2'>" . ucfirst($log['content_type']) . "</td>
                            <td class='p-2'>" . $log['moderator_name'] . "</td>
                            <td class='p-2 text-yellow-300'>" . ucfirst($log['action']) . "</td>
                            <td class='p-2'>" . ($log['reason'] ?: '-') . "</td>
                        </tr>";
                    }
                    ?>
                </tbody>
            </table>
            <div class="text-right mt-4">
                <button onclick="$('#logModal').hide()" class="bg-gray-600 px-4 py-2 rounded hover:bg-gray-700">Tutup</button>
            </div>
        </div>
    </div>

    <!-- Modal Detail Konten -->
    <div id="detailModal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center hidden z-50">
        <div class="bg-gray-800 rounded-lg w-[600px] max-h-[90vh] overflow-y-auto p-6">
            <h2 class="text-xl font-bold mb-4 text-green-400">Detail Konten</h2>
            <div id="detailContent" class="text-sm space-y-2">
                <!-- Diisi oleh JavaScript -->
            </div>
            <div class="mt-4 flex justify-between">
                <button class="bg-green-600 px-4 py-2 rounded" onclick="approveContent()">Approve</button>
                <button class="bg-yellow-500 px-4 py-2 rounded" onclick="showRejectForm()">Tolak</button>
                <button class="bg-red-600 px-4 py-2 rounded" onclick="banContent()">Banned</button>
                <button class="bg-gray-600 px-4 py-2 rounded" onclick="closeModal()">Tutup</button>
            </div>
            <div id="rejectForm" class="mt-4 hidden">
                <textarea id="rejectReason" class="w-full bg-gray-700 rounded p-2" placeholder="Alasan penolakan..."></textarea>
                <button class="mt-2 bg-yellow-500 px-4 py-2 rounded" onclick="rejectContent()">Kirim Penolakan</button>
            </div>
            <!-- Form alasan banned -->
            <div id="banForm" class="mt-4 hidden">
                <textarea id="banReason" class="w-full bg-gray-700 rounded p-2" placeholder="Alasan banned..."></textarea>
                <button class="mt-2 bg-red-500 px-4 py-2 rounded" onclick="submitBan()">Kirim Banned</button>
            </div>
        </div>
    </div>
</main>

<script>
    let currentContentId = "";
    let currentType = "";

    function showApproveModal(id, type) {
        currentContentId = id;
        currentType = type;
        if (confirm("Yakin ingin menyetujui konten ini?")) {
            approveContent(currentContentId, currentType);
        }
    }
    function approveContent() {
        $.post("moderate_content.php", { id: currentContentId, type: currentType, action: 'approved' }, function(msg) {
            alert(msg);
            location.reload();
        });
    }

    function showRejectModal(id, type) {
        currentContentId = id;
        currentType = type;
        $("#detailModal").show();
        showRejectForm();
    }
    function showRejectForm() {
        $("#rejectForm").show();
    }
    function rejectContent() {
        let reason = $("#rejectReason").val();
        if (!reason) return alert("Alasan penolakan wajib diisi.");
        $.post("moderate_content.php", { id: currentContentId, type: currentType, action: 'rejected', reason: reason }, function(msg) {
            alert(msg);
            location.reload();
        });
    }

    function showDeleteModal(id, type) {
        if (confirm("Yakin ingin menghapus konten ini?")) {
            $.post("moderate_content.php", { id: id, type: type, action: 'delete' }, function(msg) {
                alert(msg);
                location.reload();
            });
        }
    }
    
    function showDetail(id, type) {
        currentContentId = id;
        currentType = type;
        $.post("get_content_detail.php", { id: id, type: type }, function(data) {
            $("#detailContent").html(data);
            $("#detailModal").show();
        });
    }

    function closeModal() {
        $("#detailModal").hide();
        $("#rejectForm").hide();
        $("#rejectReason").val("");
    }

    function banContent() {
    $("#banForm").show(); // tampilkan form banned
    }

    function submitBan() {
        let reason = $("#banReason").val();
        if (!reason) return alert("Alasan banned wajib diisi.");
        $.post("moderate_content.php", {
            id: currentContentId,
            type: currentType,
            action: 'banned',
            reason: reason
        }, function(msg) {
            alert(msg);
            location.reload();
        });
    }

</script>
</body>
</html>