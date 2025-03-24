<?php
include("../service/config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = mysqli_real_escape_string($conn, $_POST["full_name"]);
    $username = mysqli_real_escape_string($conn, $_POST["username"]);
    $email = mysqli_real_escape_string($conn, $_POST["email"]);
    $phone_number = mysqli_real_escape_string($conn, $_POST["phone_number"]);
    $password = mysqli_real_escape_string($conn, $_POST["password"]);
    $hash_password = hash('sha256', $password); // Hash password sebelum disimpan
    $role = mysqli_real_escape_string($conn, $_POST["role"]);
    $status = 'active'; // Default status saat ditambahkan

    // Generate user_id otomatis
    $query_max_id = "SELECT MAX(SUBSTRING(user_id, 5)) AS max_id FROM users";
    $result_max_id = $conn->query($query_max_id);
    $row = $result_max_id->fetch_assoc();
    $new_id_number = $row['max_id'] ? intval($row['max_id']) + 1 : 1;
    $user_id = 'user' . str_pad($new_id_number, 8, '0', STR_PAD_LEFT);

    // Cek apakah username atau email sudah digunakan
    $check_user = "SELECT * FROM users WHERE username='$username' OR email='$email'";
    $result = $conn->query($check_user);

    if ($result->num_rows > 0) {
        echo "Username atau email sudah digunakan, silakan pilih yang lain.";
    } else {
        $sql = "INSERT INTO users (user_id, full_name, username, email, phone_number, password, role, status) 
                VALUES ('$user_id', '$full_name', '$username', '$email', '$phone_number', '$hash_password', '$role', '$status')";

        if ($conn->query($sql)) {
            echo "Pengguna berhasil ditambahkan.";
        } else {
            echo "Gagal menambahkan pengguna.";
        }
    }
}
$conn->close();
?>
