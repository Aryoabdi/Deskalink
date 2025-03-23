<?php
include("../service/config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_POST["user_id"];
    $status = $_POST["status"];

    $sql = "UPDATE users SET status='$status' WHERE user_id='$user_id'";
    if ($conn->query($sql)) {
        echo "Status pengguna berhasil diperbarui menjadi $status.";
    } else {
        echo "Gagal memperbarui status pengguna.";
    }
}
$conn->close();
?>
