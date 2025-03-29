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

    // Simpan ke database jika belum ada
    $query = "SELECT * FROM users WHERE google_id='$google_id'";
    $result = $conn->query($query);

    if ($result->num_rows == 0) {
        $stmt = $conn->prepare("INSERT INTO users (google_id, name, email, picture) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $google_id, $name, $email, $picture);
        $stmt->execute();
        $stmt->close();
    }

    // Simpan sesi login
    $_SESSION['user'] = [
        'google_id' => $google_id,
        'name' => $name,
        'email' => $email,
        'picture' => $picture
    ];

    header("Location: dashboard.php");
    exit();
}
?>
