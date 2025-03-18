<?php
include("../service/config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];

    $sql = "DELETE FROM produk WHERE id='$id'";
    echo ($conn->query($sql) === TRUE) ? "Produk berhasil dihapus" : "Gagal menghapus produk: " . $conn->error;
}

$conn->close();
?>
