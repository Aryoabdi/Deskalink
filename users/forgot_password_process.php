<?php session_start(); 
require '../service/config.php'; // Koneksi ke database
require_once '../vendor/autoload.php';

// Set zona waktu agar konsisten dengan database
date_default_timezone_set('Asia/Jakarta'); // Sesuaikan dengan zona waktu Anda

// Cek sinkronisasi waktu server dan database
$serverTime = date('Y-m-d H:i:s');
$result = $conn->query("SELECT NOW() as db_time");
$dbTime = $result->fetch_assoc()['db_time'];
error_log("Server time: $serverTime, DB time: $dbTime");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use League\OAuth2\Client\Provider\Google;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    
    if (empty($username)) {
        $_SESSION['error'] = "Username wajib diisi.";
        header('Location: forgot_password.php');
        exit;
    }
    
    // Cek apakah username ada
    $stmt = $conn->prepare("SELECT email FROM users WHERE username = ?");
    if (!$stmt) {
        $_SESSION['error'] = "Gagal menyiapkan query untuk mencari user.";
        header('Location: forgot_password.php');
        exit;
    }
    
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    
    if ($user) {
        // Hapus token OTP lama jika ada
        $deleteStmt = $conn->prepare("DELETE FROM password_resets WHERE email = ?");
        $deleteStmt->bind_param("s", $user['email']);
        $deleteStmt->execute();
        $deleteStmt->close();
        
        // Generate OTP kode 6 digit
        $otp = sprintf("%06d", mt_rand(1, 999999));
        $expires = date('Y-m-d H:i:s', strtotime('+15 minutes'));
        
        // Simpan OTP ke database
        $stmt = $conn->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)");
        if (!$stmt) {
            $_SESSION['error'] = "Gagal menyiapkan query untuk menyimpan OTP.";
            header('Location: forgot_password.php');
            exit;
        }
        
        $stmt->bind_param("sss", $user['email'], $otp, $expires);
        
        if ($stmt->execute()) {
            // Jika berhasil simpan OTP, kirim email menggunakan PHPMailer
            $to = $user['email'];
            $subject = "Kode OTP Reset Password Deskalink";
            $message = "Kode OTP untuk reset password akun Deskalink Anda adalah: <b>$otp</b><br><br>Kode ini berlaku selama 15 menit.";
            
            // Konfigurasi email
            $mail = new PHPMailer(true);
            try {
                // Server settings
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;
                
                // Gunakan metode autentikasi password biasa
                $mail->Username = 'aryoabdi39@gmail.com'; // Email Anda
                $mail->Password = 'esjlniodfcsnjddn'; // Gunakan App Password dari Google
                
                // Recipients
                $mail->setFrom('aryoabdi39@gmail.com', 'Deskalink');
                $mail->addAddress($to);
                
                // Content
                $mail->isHTML(true);
                $mail->Subject = $subject;
                $mail->Body = $message;
                $mail->AltBody = strip_tags(str_replace("<br>", "\n", $message)); // Plain text version
                
                // Send email
                $mail->send();
                
                // Simpan email dan username di session untuk verifikasi
                $_SESSION['reset_email'] = $user['email'];
                $_SESSION['reset_username'] = $username;
                $_SESSION['last_otp_sent'] = time(); // Catat waktu pengiriman OTP
                
                $_SESSION['success'] = "Kode OTP telah dikirim ke email Anda.";
                header('Location: verify_otp.php'); // Redirect ke halaman verifikasi OTP
                exit;
            } catch (Exception $e) {
                $_SESSION['error'] = "Gagal mengirim email kode OTP: " . $mail->ErrorInfo;
                header('Location: forgot_password.php');
                exit;
            }
        } else {
            $_SESSION['error'] = "Gagal menyimpan data kode OTP.";
            header('Location: forgot_password.php');
            exit;
        }
        $stmt->close();
    } else {
        $_SESSION['error'] = "Username tidak ditemukan.";
        header('Location: forgot_password.php');
        exit;
    }
}
?>