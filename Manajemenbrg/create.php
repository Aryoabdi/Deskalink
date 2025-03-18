<?php
include("../service/config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['nama']) || !isset($_POST['harga'])) {
        die("Data tidak lengkap!");
    }

    $nama = $_POST['nama'];
    $harga = $_POST['harga'];

    // Cek apakah data benar-benar terkirim
    if (empty($nama) || empty($harga)) {
        die("Nama produk atau harga tidak boleh kosong!");
    }

    $sql = "INSERT INTO produk (nama, harga) VALUES ('$nama', '$harga')";

    if ($conn->query($sql) === TRUE) {
        echo "Produk berhasil ditambahkan";
    } else {
        echo "Gagal menambah produk: " . $conn->error;
    }
}

$conn->close();
?>
