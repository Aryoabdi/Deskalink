<?php
include("../service/config.php");

// Ambil total pengguna
$query_users = "SELECT COUNT(*) AS total_users FROM users WHERE role IN ('client', 'partner')";
$total_users = $conn->query($query_users)->fetch_assoc()["total_users"];

// Ambil total transaksi
$query_transactions = "SELECT COUNT(*) AS total_transactions FROM transactions";
$total_transactions = $conn->query($query_transactions)->fetch_assoc()["total_transactions"];

// Ambil total penghasilan platform
$query_earnings = "SELECT SUM(platform_fee) AS total_earnings FROM transactions WHERE status='completed'";
$total_earnings = $conn->query($query_earnings)->fetch_assoc()["total_earnings"] ?? 0;

// Ambil total laporan
$query_reports = "SELECT COUNT(*) AS total_reports FROM reports";
$total_reports = $conn->query($query_reports)->fetch_assoc()["total_reports"];

// Ambil data transaksi untuk grafik
$query_chart = "SELECT DATE(created_at) AS date, COUNT(*) AS count FROM transactions GROUP BY DATE(created_at)";
$result_chart = $conn->query($query_chart);
$chart_data = ["dates" => [], "counts" => []];
while ($row = $result_chart->fetch_assoc()) {
    $chart_data["dates"][] = $row["date"];
    $chart_data["counts"][] = $row["count"];
}

// Ambil jumlah pengguna berdasarkan role
$query_user_roles = "SELECT role, COUNT(*) AS count FROM users WHERE role IN ('client', 'partner') GROUP BY role";
$result_user_roles = $conn->query($query_user_roles);
$user_data = ["client" => 0, "partner" => 0];
while ($row = $result_user_roles->fetch_assoc()) {
    $user_data[$row["role"]] = $row["count"];
}

$conn->close();

// Kirim data dalam format JSON
echo json_encode([
    "total_users" => $total_users,
    "total_transactions" => $total_transactions,
    "total_earnings" => $total_earnings,
    "total_reports" => $total_reports,
    "transactions" => $chart_data,
    "users" => $user_data
]);
?>
