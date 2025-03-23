<?php
include("../service/config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_POST["user_id"];
    $full_name = $_POST["full_name"];
    $username = $_POST["username"];
    $email = $_POST["email"];
    $phone_number = $_POST["phone_number"] ?: NULL;
    $role = $_POST["role"];

    $sql = "UPDATE users SET full_name='$full_name', username='$username', email='$email', phone_number='$phone_number', role='$role' WHERE user_id='$user_id'";
    if ($conn->query($sql)) {
        echo "Data pengguna berhasil diperbarui.";
    } else {
        echo "Gagal memperbarui data pengguna.";
    }
}
$conn->close();
?>
