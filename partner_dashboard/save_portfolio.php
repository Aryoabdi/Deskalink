<?php
include("../service/config.php");
session_start();

if (!isset($_SESSION['is_login']) || $_SESSION['role'] !== 'partner') {
    http_response_code(403);
    exit("Akses ditolak");
}

$partner_id = $_SESSION['user_id'];
$title = mysqli_real_escape_string($conn, $_POST['title']);
$description = mysqli_real_escape_string($conn, $_POST['description']);
$image_url = !empty($_POST['image_url']) ? mysqli_real_escape_string($conn, $_POST['image_url']) : null;
$document_url = !empty($_POST['document_url']) ? mysqli_real_escape_string($conn, $_POST['document_url']) : null;
$type = in_array($_POST['type'], ['karya', 'sertifikat', 'penghargaan', 'lainnya']) ? $_POST['type'] : 'karya';

$sql = "INSERT INTO portfolios (partner_id, title, description, image_url, document_url, type)
        VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssss", $partner_id, $title, $description, $image_url, $document_url, $type);

if ($stmt->execute()) {
    echo "Portofolio berhasil ditambahkan.";
} else {
    echo "Gagal menambahkan portofolio.";
}

$conn->close();
?>
