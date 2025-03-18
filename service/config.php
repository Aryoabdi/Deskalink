<?php

$hostname = "localhost";
$username = "root";
$password = "";
$database_name = "deskalink";

$conn = mysqli_connect($hostname, $username, $password, $database_name);

if($conn->connect_error) {
    echo "koneksi database rusak";
    die("error!");
}

?>