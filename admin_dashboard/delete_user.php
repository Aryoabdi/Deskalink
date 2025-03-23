<?php
include("../service/config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_POST["user_id"];

    $sql = "DELETE FROM users WHERE user_id='$user_id'";
    if ($conn->query($sql)) {
        echo "Pengguna berhasil dihapus.";
    } else {
        echo "Gagal menghapus pengguna.";
    }
}
$conn->close();
?>
