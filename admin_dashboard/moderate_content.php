<?php
include("../service/config.php");
session_start();

if (!isset($_SESSION['is_login']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    exit("Akses ditolak");
}

$id = $_POST['id'] ?? '';
$type = $_POST['type'] ?? '';
$action = $_POST['action'] ?? '';
$reason = $_POST['reason'] ?? null;

$validTypes = ['service', 'design'];
$validActions = ['approved', 'rejected', 'banned', 'delete']; // Tambahkan 'delete' ke aksi valid

if (!$id || !in_array($type, $validTypes) || !in_array($action, $validActions)) {
    http_response_code(400);
    exit("Data tidak valid");
}

$table = $type === 'design' ? 'designs' : 'services';
$id_column = $type === 'design' ? 'design_id' : 'service_id';

// Jika aksinya delete, hapus konten, jika tidak update status
if ($action === 'delete') {
    // Simpan informasi konten sebelum dihapus untuk log
    $getContent = $conn->prepare("SELECT partner_id, title FROM $table WHERE $id_column = ?");
    $getContent->bind_param("s", $id);
    $getContent->execute();
    $contentInfo = $getContent->get_result()->fetch_assoc();
    
    // Hapus konten dari database
    $delete = $conn->prepare("DELETE FROM $table WHERE $id_column = ?");
    $delete->bind_param("s", $id);
    
    if ($delete->execute()) {
        // Catat penghapusan di log moderasi
        $moderator_id = $_SESSION['user_id'];
        $log = $conn->prepare("INSERT INTO moderation_logs (content_id, content_type, moderator_id, action, reason) VALUES (?, ?, ?, ?, ?)");
        $log->bind_param("sssss", $id, $type, $moderator_id, $action, $reason);
        $log->execute();
        
        echo "Konten '{$contentInfo['title']}' berhasil dihapus";
    } else {
        http_response_code(500);
        echo "Gagal menghapus konten: " . $conn->error;
    }
} else {
    // Kode yang sudah ada untuk update status konten
    $update = $conn->prepare("UPDATE $table SET status = ? WHERE $id_column = ?");
    $update->bind_param("ss", $action, $id);
    $update->execute();

    // Simpan log moderasi
    $moderator_id = $_SESSION['user_id'];
    $log = $conn->prepare("INSERT INTO moderation_logs (content_id, content_type, moderator_id, action, reason) VALUES (?, ?, ?, ?, ?)");
    $log->bind_param("sssss", $id, $type, $moderator_id, $action, $reason);
    $log->execute();

    echo "Status konten berhasil diperbarui menjadi " . ucfirst($action);
}
?>