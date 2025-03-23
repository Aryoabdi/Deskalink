<?php
include("../service/config.php");

if (isset($_POST["user_id"])) {
    $user_id = $_POST["user_id"];
    $query = "SELECT user_id, full_name, username, email, phone_number, role FROM users WHERE user_id = '$user_id'";
    $result = $conn->query($query);
    if ($result->num_rows > 0) {
        echo json_encode($result->fetch_assoc());
    } else {
        echo json_encode(["error" => "User tidak ditemukan"]);
    }
}
$conn->close();
?>
