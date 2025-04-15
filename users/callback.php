<?php
require '../service/config.php';
session_start();

if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    $client->setAccessToken($token['access_token']);
    
    // Ambil data pengguna dari Google
    $google_oauth = new Google_Service_Oauth2($client);
    $google_info = $google_oauth->userinfo->get();
    $google_id = $google_info->id;
    $name = $google_info->name;
    $email = $google_info->email;
    $picture = $google_info->picture;
    
    // Generate username from email (before @)
    $username = strtolower(explode('@', $email)[0]);
    
    // Cek apakah username sudah ada
    $check_username = $conn->prepare("SELECT username FROM users WHERE username = ?");
    $check_username->bind_param("s", $username);
    $check_username->execute();
    $username_result = $check_username->get_result();
    
    // Jika username sudah ada, tambahkan angka random
    if ($username_result->num_rows > 0) {
        $username = $username . rand(100, 999);
    }
    $check_username->close();
    
    // Cek apakah user sudah ada
    $query = "SELECT * FROM users WHERE google_id=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $google_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 0) {
        // Generate user_id otomatis
        $result_max = $conn->query("SELECT MAX(CAST(SUBSTRING(user_id, 5) AS UNSIGNED)) AS max_id FROM users");
        $row = $result_max->fetch_assoc();
        $new_id_number = $row['max_id'] ? intval($row['max_id']) + 1 : 1;
        $user_id = 'user' . str_pad($new_id_number, 8, '0', STR_PAD_LEFT);
        
        // Set nilai default
        $default_phone = '';
        $status = 'active';
        $is_profile_completed = 0;
        
        // Simpan user baru
        $stmt = $conn->prepare("INSERT INTO users (user_id, google_id, username, full_name, email, profile_image, phone_number, status, is_profile_completed) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssssi", $user_id, $google_id, $username, $name, $email, $picture, $default_phone, $status, $is_profile_completed);
        $stmt->execute();
        $stmt->close();
        
        // Simpan data sementara di sesi
        $_SESSION["is_login"] = true;
        $_SESSION["user_id"] = $user_id;
        $_SESSION["google_id"] = $google_id;
        $_SESSION["username"] = $username;
        $_SESSION["full_name"] = $name;
        $_SESSION["email"] = $email;
        $_SESSION["profile_image"] = $picture;
        $_SESSION["is_profile_completed"] = $is_profile_completed;
        
        // Arahkan ke halaman melengkapi profil
        header("Location: complete-profile.php");
        exit();
    } else {
        // User sudah ada
        $user = $result->fetch_assoc();
        
        // Update profile image jika berubah
        if ($user['profile_image'] !== $picture) {
            $update_picture = $conn->prepare("UPDATE users SET profile_image = ? WHERE user_id = ?");
            $update_picture->bind_param("ss", $picture, $user["user_id"]);
            $update_picture->execute();
            $update_picture->close();
        }
        
        // Simpan data di sesi
        $_SESSION["is_login"] = true;
        $_SESSION["user_id"] = $user["user_id"];
        $_SESSION["google_id"] = $user["google_id"];
        $_SESSION["username"] = $user["username"];
        $_SESSION["role"] = $user["role"];
        $_SESSION["full_name"] = $user["full_name"];
        $_SESSION["profile_image"] = $picture;
        $_SESSION["is_profile_completed"] = $user["is_profile_completed"];

        // Redirect berdasarkan status profil dan role
        if (!$user["is_profile_completed"]) {
            // Jika profil belum lengkap
            header("Location: complete-profile.php");
        } else if ($user["role"] === null) {
            // Jika belum memilih role
            header("Location: select-role.php");
        } else if ($user["role"] == "admin") {
            header("location: ../admin_dashboard/index.php");
        } else if ($user["role"] == "partner") {
            header("location: ../partner_dashboard/dashboard_partner.php");
        } else {
            header("location: ../market/index.php");
        }
        exit();
    }
}
?>