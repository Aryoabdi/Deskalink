<?php
session_start();
include("../service/config.php");

// Cek apakah admin sudah login
if (!isset($_SESSION["is_login"]) || $_SESSION["role"] !== "admin") {
    header("location: ../users/login.php");
    exit();
}

// Ambil daftar pengguna
$role_filter = isset($_GET['role']) ? $_GET['role'] : '';
$query = "SELECT user_id, full_name, username, email, phone_number, role, status FROM users WHERE role IN ('client', 'partner')";
if (!empty($role_filter)) {
    $query .= " AND role = '$role_filter'";
}
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Pengguna - DeskaLink</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="bg-gray-900 text-white">

    <!-- Sidebar -->
    <?php include("sidebar.php"); ?>

    <!-- Main Content -->
    <main class="flex-1 p-6 ml-64">
        <h1 class="text-3xl font-bold">Manajemen Pengguna</h1>

        <!-- Filter Role -->
        <div class="mt-4">
            <label class="text-lg">Filter Role:</label>
            <select id="roleFilter" class="bg-gray-700 p-2 rounded">
                <option value="">Semua</option>
                <option value="client" <?= $role_filter == 'client' ? 'selected' : '' ?>>Client</option>
                <option value="partner" <?= $role_filter == 'partner' ? 'selected' : '' ?>>Partner</option>
            </select>
        </div>

        <!-- Tombol Tambah Pengguna -->
        <button onclick="$('#addUserModal').show()" class="mt-4 bg-green-500 px-4 py-2 rounded hover:bg-green-600">
            + Tambah Pengguna
        </button>

        <!-- Tabel Pengguna -->
        <table class="mt-4 w-full bg-gray-800 rounded-lg">
            <thead>
                <tr class="bg-gray-700">
                    <th class="p-2">ID</th>
                    <th class="p-2">Nama</th>
                    <th class="p-2">Username</th>
                    <th class="p-2">Email</th>
                    <th class="p-2">Nomor Telepon</th>
                    <th class="p-2">Role</th>
                    <th class="p-2">Status</th>
                    <th class="p-2">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Using the already filtered result from above
                while ($row = $result->fetch_assoc()) { ?>
                    <tr class="border-b border-gray-700">
                        <td class="p-2"><?= $row["user_id"] ?></td>
                        <td class="p-2"><?= $row["full_name"] ?></td>
                        <td class="p-2"><?= $row["username"] ?></td>
                        <td class="p-2"><?= $row["email"] ?></td>
                        <td class="p-2"><?= $row["phone_number"] ?: '-' ?></td>
                        <td class="p-2"><?= ucfirst($row["role"]) ?></td>
                        <td class="p-2 text-sm <?= $row["status"] == 'active' ? 'text-green-400' : ($row["status"] == 'suspended' ? 'text-yellow-400' : 'text-red-400') ?>">
                            <?= ucfirst($row["status"]) ?>
                        </td>
                        <td class="p-2">
                            <button class="px-2 py-1 bg-green-500 rounded text-sm" onclick="updateStatus('<?= $row['user_id'] ?>', 'active')">Aktif</button>
                            <button class="px-2 py-1 bg-yellow-500 rounded text-sm" onclick="updateStatus('<?= $row['user_id'] ?>', 'suspended')">Suspend</button>
                            <button class="px-2 py-1 bg-red-500 rounded text-sm" onclick="updateStatus('<?= $row['user_id'] ?>', 'banned')">Banned</button>
                            <button class="px-2 py-1 bg-blue-500 rounded text-sm" onclick="editUser('<?= $row['user_id'] ?>')">Edit</button>
                            <button class="px-2 py-1 bg-red-600 rounded text-sm" onclick="deleteUser('<?= $row['user_id'] ?>')">Hapus</button>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </main>

    <!-- Tambah User Modal -->
    <div id="addUserModal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center hidden z-50">
        <div class="bg-gray-800 p-6 rounded-lg w-96">
            <h2 class="text-xl font-bold mb-4">Tambah Pengguna</h2>
            <form id="addUserForm">
                <input type="text" name="full_name" placeholder="Nama Lengkap" class="block w-full mb-2 p-2 bg-gray-700 rounded" required>
                <input type="text" name="username" placeholder="Username" class="block w-full mb-2 p-2 bg-gray-700 rounded" required>
                <input type="email" name="email" placeholder="Email" class="block w-full mb-2 p-2 bg-gray-700 rounded" required>
                <input type="text" name="phone_number" placeholder="Nomor Telepon" class="block w-full mb-2 p-2 bg-gray-700 rounded" required>
                <input type="password" name="password" placeholder="Password" class="block w-full mb-2 p-2 bg-gray-700 rounded" required>
                <select name="role" class="block w-full mb-2 p-2 bg-gray-700 rounded" required>
                    <option value="client">Client</option>
                    <option value="partner">Partner</option>
                </select>
                <button type="submit" class="bg-green-500 px-4 py-2 rounded">Tambah</button>
                <button type="button" onclick="$('#addUserModal').hide()" class="bg-gray-600 px-4 py-2 rounded">Batal</button>
            </form>
        </div>
    </div>

    <!-- Modal Edit Pengguna -->
    <div id="editUserModal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center hidden z-50">
        <div class="bg-gray-800 p-6 rounded-lg w-96">
            <h2 class="text-xl font-bold mb-4">Edit Pengguna</h2>
            <form id="editUserForm">
                <!-- <input type="hidden" name="user_id" id="edit_user_id"> -->
                <input type="text" name="user_id" id="view_user_id" placeholder="User ID" class="block w-full mb-2 p-2 bg-gray-700 rounded" readonly>
                <input type="text" name="full_name" id="edit_full_name" placeholder="Nama Lengkap" class="block w-full mb-2 p-2 bg-gray-700 rounded" required>
                <input type="text" name="username" id="edit_username" placeholder="Username" class="block w-full mb-2 p-2 bg-gray-700 rounded" required>
                <input type="email" name="email" id="edit_email" placeholder="Email" class="block w-full mb-2 p-2 bg-gray-700 rounded" required>
                <input type="text" name="phone_number" id="edit_phone_number" placeholder="Nomor Telepon" class="block w-full mb-2 p-2 bg-gray-700 rounded">
                <select name="role" id="edit_role" class="block w-full mb-2 p-2 bg-gray-700 rounded" required>
                    <option value="client">Client</option>
                    <option value="partner">Partner</option>
                </select>
                <button type="submit" class="bg-blue-500 px-4 py-2 rounded">Simpan</button>
                <button type="button" onclick="$('#editUserModal').hide()" class="bg-gray-600 px-4 py-2 rounded">Batal</button>
            </form>
        </div>
    </div>

    <script>
        // Filter berdasarkan role
        $("#roleFilter").change(function() {
            let selectedRole = $(this).val();
            window.location.href = "manage_users.php?role=" + selectedRole;
        });
        // Update Status Pengguna
        function updateStatus(userId, status) {
            $.post("update_status.php", { user_id: userId, status: status }, function(response) {
                alert(response);
                location.reload();
            });
        }

        // Hapus Pengguna
        function deleteUser(userId) {
            if (confirm("Yakin ingin menghapus pengguna ini?")) {
                $.post("delete_user.php", { user_id: userId }, function(response) {
                    alert(response);
                    location.reload();
                });
            }
        }

        // Tambah Pengguna
        $("#addUserForm").submit(function(e) {
            e.preventDefault();
            $.post("add_user.php", $(this).serialize(), function(response) {
                alert(response);
                location.reload();
            });
        });

        // Buka modal edit pengguna dengan data yang ada
        function editUser(userId) {
            $.post("get_user.php", { user_id: userId }, function(data) {
                let user = JSON.parse(data);
                $("#view_user_id").val(user.user_id);
                $("#edit_full_name").val(user.full_name);
                $("#edit_username").val(user.username);
                $("#edit_email").val(user.email);
                $("#edit_phone_number").val(user.phone_number);
                $("#edit_role").val(user.role);
                $("#editUserModal").show();
            });
        }

        // Proses update data pengguna
        $("#editUserForm").submit(function(e) {
            e.preventDefault();
            $.post("update_user.php", $(this).serialize(), function(response) {
                alert(response);
                location.reload();
            });
        });
    </script>
</body>
</html>