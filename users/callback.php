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
    
    // Cek apakah user sudah ada
    $query = "SELECT * FROM users WHERE google_id='$google_id'";
    $result = $conn->query($query);
    
    if ($result->num_rows == 0) {
        // Generate user_id otomatis
        $result_max = $conn->query("SELECT MAX(CAST(SUBSTRING(user_id, 5) AS UNSIGNED)) AS max_id FROM users");
        $row = $result_max->fetch_assoc();
        $new_id_number = $row['max_id'] ? intval($row['max_id']) + 1 : 1;
        $user_id = 'user' . str_pad($new_id_number, 8, '0', STR_PAD_LEFT);
        
        // Simpan user baru (belum lengkap)
        $stmt = $conn->prepare("INSERT INTO users (user_id, google_id, full_name, email, profile_image) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $user_id, $google_id, $name, $email, $picture);
        $stmt->execute();
        $stmt->close();
        
        // Simpan data sementara di sesi
        // $_SESSION["is_login"] = true;
        // $_SESSION["user_id"] = $user_id;
        // $_SESSION["google_id"] = $google_id;
        // $_SESSION["full_name"] = $name;
        $_SESSION['google_login'] = [
            'user_id' => $user_id,
            'google_id' => $google_id,
            'full_name' => $name,
            'email' => $email,
            'profile_image' => $picture
        ];
        
        // Arahkan ke complete-profile.php jika user baru
        header("Location: complete-profile.php");
        exit();
    } else {
        // User sudah ada
        $user = $result->fetch_assoc();
        
        // Simpan data di sesi
        $_SESSION["is_login"] = true;
        $_SESSION["user_id"] = $user["user_id"];
        $_SESSION["google_id"] = $user["google_id"];
        $_SESSION["username"] = $user["username"];
        $_SESSION["role"] = $user["role"];
        $_SESSION["full_name"] = $user["full_name"];
        // $_SESSION['google_login'] = [
        //     'user_id' => $user_id,
        //     'google_id' => $google_id,
        //     'full_name' => $name,
        //     'email' => $email,
        //     'profile_image' => $picture
        // ];

        if ($_SESSION["role"] == "admin") {
            header("location: ../admin_dashboard/index.php");
        } elseif ($_SESSION["role"] == "partner") {
            header("location: ../partner_dashboard/dashboard_partner.php");
        } else {
            header("location: ../market/index.php");
        }
        exit();
    }
}
?>