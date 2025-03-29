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

// Konfigurasi Google OAuth
$client = new Google_Client();
$client->setClientId('993924704396-dplj8haf2uqvih4i1cnutr2rk0ugohke.apps.googleusercontent.com');
$client->setClientSecret('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');
$client->setRedirectUri('http://localhost/Deskalink/users/callback.php'); // Sesuaikan dengan folder proyek
$client->addScope("email");
$client->addScope("profile");
?>
