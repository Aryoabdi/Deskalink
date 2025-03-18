<?php
include("../service/config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $nama = $_POST['nama'];
    $harga = $_POST['harga'];

    $sql = "UPDATE produk SET nama='$nama', harga='$harga' WHERE id=$id";
    echo ($conn->query($sql) === TRUE) ? "Produk berhasil diperbarui" : "Gagal memperbarui produk: " . $conn->error;
}

$conn->close();
?>
