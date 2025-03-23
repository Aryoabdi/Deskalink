<?php
session_start();
if (!isset($_SESSION["is_login"]) || $_SESSION["role"] !== "admin") {
    header("location: ../users/login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - DeskaLink</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="bg-gray-900 text-white">
    <!-- Sidebar -->
    <?php include("sidebar.php"); ?>
    <div class="flex h-screen">

        <!-- Main Content -->
        <main class="flex-1 p-6 ml-64">
            <h1 class="text-3xl font-bold">Dashboard Admin</h1>

            <!-- Statistik -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">
                <div class="bg-gray-800 p-4 rounded-lg">
                    <h2 class="text-xl font-semibold">Total Pengguna</h2>
                    <p class="text-2xl font-bold text-green-400" id="total_users">0</p>
                </div>
                <div class="bg-gray-800 p-4 rounded-lg">
                    <h2 class="text-xl font-semibold">Total Transaksi</h2>
                    <p class="text-2xl font-bold text-blue-400" id="total_transactions">0</p>
                </div>
                <div class="bg-gray-800 p-4 rounded-lg">
                    <h2 class="text-xl font-semibold">Penghasilan Platform</h2>
                    <p class="text-2xl font-bold text-yellow-400" id="total_earnings">Rp 0</p>
                </div>
                <div class="bg-gray-800 p-4 rounded-lg">
                    <h2 class="text-xl font-semibold">Laporan Pelanggaran</h2>
                    <p class="text-2xl font-bold text-red-400" id="total_reports">0</p>
                </div>
            </div>

            <!-- Grafik dalam Grid -->
            <div class="grid grid-cols-4 gap-4 mt-6">
                <!-- Grafik Transaksi (3 bagian) -->
                <div class="col-span-3 bg-gray-800 p-6 rounded-lg">
                    <h2 class="text-xl font-semibold mb-4">Grafik Transaksi</h2>
                    <canvas id="transactionChart"></canvas>
                </div>

                <!-- Grafik Pengguna (1 bagian) -->
                <div class="col-span-1 bg-gray-800 p-6 rounded-lg">
                    <h2 class="text-xl font-semibold mb-4">Pengguna Terdaftar</h2>
                    <canvas id="userChart"></canvas>
                </div>
            </div>
        </main>

    </div>

    <script>
        $(document).ready(function() {
            // Ambil data statistik dari fetch_data.php
            function fetchData() {
                $.ajax({
                    url: "fetch_data.php",
                    method: "GET",
                    dataType: "json",
                    success: function(data) {
                        $("#total_users").text(data.total_users);
                        $("#total_transactions").text(data.total_transactions);
                        $("#total_earnings").text("Rp " + data.total_earnings);
                        $("#total_reports").text(data.total_reports);
                        updateTransactionChart(data.transactions);
                        updateUserChart(data.users);
                    }
                });
            }

            // Panggil fetchData saat halaman dimuat
            fetchData();

            // Update chart transaksi
            function updateTransactionChart(transactions) {
                let ctx = document.getElementById('transactionChart').getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: transactions.dates,
                        datasets: [{
                            label: 'Jumlah Transaksi',
                            data: transactions.counts,
                            borderColor: 'rgb(75, 192, 192)',
                            borderWidth: 2
                        }]
                    },
                    options: { responsive: true }
                });
            }

            // Update chart pengguna (Client vs Partner)
            function updateUserChart(users) {
                let ctx = document.getElementById('userChart').getContext('2d');
                new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: ['Client', 'Partner'],
                        datasets: [{
                            data: [users.client, users.partner],
                            backgroundColor: ['#4CAF50', '#2196F3']
                        }]
                    },
                    options: { responsive: true }
                });
            }
        });
    </script>
</body>
</html>
