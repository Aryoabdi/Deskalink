<?php
require '../vendor/autoload.php';

// Konfigurasi Database
$hostname = "localhost";
$username = "root";
$password = "";
$database_name = "deskalink";

$conn = mysqli_connect($hostname, $username, $password, $database_name);

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}
?>
