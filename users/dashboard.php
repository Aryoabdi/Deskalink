<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: register.php");
    exit();
}
?>

<h2>Selamat datang, <?php echo $_SESSION['username']; ?>!</h2>