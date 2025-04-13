<?php
include("../service/config.php");
session_start();

if (!isset($_SESSION['is_login']) || $_SESSION['role'] !== 'partner') {
    http_response_code(403);
    exit("Akses ditolak");
}

$id = $_POST['id'];
$partner_id = $_SESSION['user_id'];

$stmt = $conn->prepare("DELETE FROM designs WHERE design_id = ? AND partner_id = ?");
$stmt->bind_param("is", $id, $partner_id);

if ($stmt->execute()) {
    echo "Desain berhasil dihapus.";
} else {
    echo "Gagal menghapus desain.";
}
