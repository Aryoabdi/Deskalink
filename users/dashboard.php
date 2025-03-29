<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION['user'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
</head>
<body>
    <h1>Selamat Datang, <?php echo $user['name']; ?></h1>
    <p>Email: <?php echo $user['email']; ?></p>
    <img src="<?php echo $user['picture']; ?>" alt="Foto Profil">
    <br>
    <a href="logout.php">Logout</a>
</body>
</html>
