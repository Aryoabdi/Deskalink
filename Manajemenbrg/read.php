<?php
include("../service/config.php");

$sql = "SELECT * FROM produk";
$result = $conn->query($sql);

$produkList = [];

while ($row = $result->fetch_assoc()) {
    $produkList[] = $row;
}

// Pastikan hanya JSON yang dikirim
header('Content-Type: application/json');
echo json_encode($produkList, JSON_PRETTY_PRINT);

$conn->close();
?>