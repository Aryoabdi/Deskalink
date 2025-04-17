<?php
session_start();
require '../service/config.php';
require_once '../vendor/autoload.php';

// Set zona waktu agar konsisten dengan database
date_default_timezone_set('Asia/Jakarta'); // Sesuaikan dengan zona waktu Anda

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Cek jika user belum meminta reset password
if (!isset($_SESSION['reset_email'])) {
    $_SESSION['error'] = "Harap meminta reset password terlebih dahulu.";
    header('Location: forgot_password.php');
    exit;
}

$email = $_SESSION['reset_email'];
$username = isset($_SESSION['reset_username']) ? $_SESSION['reset_username'] : '';
$resend = false;

// Handling untuk kirim ulang OTP
if (isset($_GET['resend']) && $_GET['resend'] == '1') {
    // Cek apakah cooldown sudah habis
    $canResend = true;
    if (isset($_SESSION['last_otp_sent']) && time() - $_SESSION['last_otp_sent'] < 60) {
        $timeLeft = 60 - (time() - $_SESSION['last_otp_sent']);
        $_SESSION['error'] = "Harap tunggu {$timeLeft} detik lagi sebelum mengirim ulang kode OTP.";
        $canResend = false;
    }
    
    if ($canResend) {
        // Hapus token OTP lama jika ada
        $deleteStmt = $conn->prepare("DELETE FROM password_resets WHERE email = ?");
        if (!$deleteStmt) {
            $_SESSION['error'] = "Gagal menyiapkan query untuk menghapus OTP lama: " . $conn->error;
            header('Location: verify_otp.php');
            exit;
        }
        $deleteStmt->bind_param("s", $email);
        $deleteStmt->execute();
        $deleteStmt->close();
        
        // Generate OTP kode 6 digit baru
        $otp = sprintf("%06d", mt_rand(1, 999999));
        $expires = date('Y-m-d H:i:s', strtotime('+15 minutes'));
        
        // Simpan OTP ke database
        $stmt = $conn->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)");
        if (!$stmt) {
            $_SESSION['error'] = "Gagal menyiapkan query untuk menyimpan OTP: " . $conn->error;
            header('Location: verify_otp.php');
            exit;
        }
        
        $stmt->bind_param("sss", $email, $otp, $expires);
        
        if ($stmt->execute()) {
            // Catat OTP ke log untuk debugging
            error_log("OTP baru dibuat untuk $email: $otp, berlaku hingga: $expires");
            
            // Jika berhasil simpan OTP, kirim email menggunakan PHPMailer
            $to = $email;
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
                
                // Catat waktu pengiriman OTP
                $_SESSION['last_otp_sent'] = time();
                // Simpan OTP dalam session untuk debugging jika diperlukan
                $_SESSION['current_otp'] = $otp;
                
                $_SESSION['success'] = "Kode OTP baru telah dikirim ke email Anda.";
                $resend = true;
                
            } catch (Exception $e) {
                $_SESSION['error'] = "Gagal mengirim email kode OTP: " . $mail->ErrorInfo;
                error_log("Error PHPMailer: " . $mail->ErrorInfo);
            }
        } else {
            $_SESSION['error'] = "Gagal menyimpan data kode OTP: " . $stmt->error;
            error_log("Error SQL: " . $stmt->error);
        }
        $stmt->close();
    }
}

// Proses verifikasi OTP
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $otp = trim($_POST['otp']);
    
    if (empty($otp)) {
        $_SESSION['error'] = "Kode OTP wajib diisi.";
        header('Location: verify_otp.php');
        exit;
    }
    
    // Log untuk debug
    error_log("Verifikasi OTP untuk $email, Input OTP: $otp");
    
    // Query database untuk OTP yang valid
    $stmt = $conn->prepare("SELECT * FROM password_resets WHERE email = ? AND token = ? AND expires_at > NOW()");
    if (!$stmt) {
        $_SESSION['error'] = "Gagal menyiapkan query untuk verifikasi OTP: " . $conn->error;
        header('Location: verify_otp.php');
        exit;
    }
    
    $stmt->bind_param("ss", $email, $otp);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Log hasil query untuk debug
    error_log("Hasil query verifikasi OTP: " . $result->num_rows . " baris ditemukan");
    
    if ($result->num_rows > 0) {
        // OTP valid, hapus token agar tidak bisa digunakan lagi
        $deleteStmt = $conn->prepare("DELETE FROM password_resets WHERE email = ? AND token = ?");
        $deleteStmt->bind_param("ss", $email, $otp);
        $deleteStmt->execute();
        $deleteStmt->close();
        
        // Simpan data untuk reset password
        $_SESSION['reset_verified'] = true;
        
        // Log sukses
        error_log("OTP berhasil diverifikasi untuk $email");
        
        header('Location: new_password.php'); // Redirect ke halaman input password baru
        exit;
    } else {
        // Debug: cek token yang ada di database untuk email ini
        $debugStmt = $conn->prepare("SELECT token, expires_at FROM password_resets WHERE email = ?");
        $debugStmt->bind_param("s", $email);
        $debugStmt->execute();
        $debugResult = $debugStmt->get_result();
        
        if ($debugResult->num_rows > 0) {
            $row = $debugResult->fetch_assoc();
            error_log("Token di database: " . $row['token'] . ", Expired: " . $row['expires_at'] . ", Input: " . $otp);
        } else {
            error_log("Tidak ada token yang ditemukan untuk email: $email");
        }
        
        $debugStmt->close();
        
        $_SESSION['error'] = "Kode OTP tidak valid atau sudah kedaluwarsa.";
        header('Location: verify_otp.php');
        exit;
    }
    
    $stmt->close();
}

// Hitung sisa waktu cooldown untuk kirim ulang OTP
$cooldownTimeLeft = 0;
if (isset($_SESSION['last_otp_sent'])) {
    $timeSinceLastSent = time() - $_SESSION['last_otp_sent'];
    if ($timeSinceLastSent < 60) {
        $cooldownTimeLeft = 60 - $timeSinceLastSent;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi OTP - DeskaLink</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        // Script untuk countdown timer
        document.addEventListener('DOMContentLoaded', function() {
            let cooldownTime = <?php echo $cooldownTimeLeft; ?>;
            const resendBtn = document.getElementById('resendBtn');
            const timerSpan = document.getElementById('timer');
            
            function updateTimer() {
                if (cooldownTime <= 0) {
                    timerSpan.style.display = 'none';
                    resendBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                    resendBtn.classList.add('hover:underline');
                    resendBtn.removeAttribute('disabled');
                } else {
                    timerSpan.style.display = 'inline';
                    timerSpan.textContent = `(${cooldownTime})`;
                    cooldownTime--;
                    setTimeout(updateTimer, 1000);
                }
            }
            
            if (cooldownTime > 0) {
                resendBtn.classList.add('opacity-50', 'cursor-not-allowed');
                resendBtn.setAttribute('disabled', 'disabled');
                updateTimer();
            }
            
            // Fungsi untuk mengatur autofocus pada input OTP
            const otpInput = document.getElementById('otp');
            if (otpInput) {
                otpInput.focus();
            }
        });
    </script>
</head>
<body class="bg-gray-900 flex justify-center items-center h-screen">
    <div class="bg-gray-800 text-white p-6 rounded-lg shadow-lg max-w-md w-full">
        <h2 class="text-2xl font-bold mb-4">Verifikasi Kode OTP</h2>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="bg-red-500 text-white p-3 rounded-lg mb-4">
                <?php 
                    echo $_SESSION['error']; 
                    unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="bg-green-500 text-white p-3 rounded-lg mb-4">
                <?php 
                    echo $_SESSION['success']; 
                    unset($_SESSION['success']);
                ?>
            </div>
        <?php endif; ?>
        
        <p class="mb-4">Masukkan kode OTP yang telah dikirim ke email <strong><?php echo htmlspecialchars($email); ?></strong>:</p>
        
        <form action="verify_otp.php" method="post">
            <input type="text" name="otp" id="otp" class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 mb-4" placeholder="Masukkan kode OTP 6 digit" maxlength="6" inputmode="numeric" pattern="\d{6}" title="Masukkan 6 digit angka" required autofocus>
            
            <button type="submit" class="w-full bg-green-500 text-white py-2 rounded-lg hover:bg-green-600 mb-3">Verifikasi OTP</button>
        </form>
        
        <p class="text-center text-sm">
            Belum menerima kode? 
            <a id="resendBtn" href="verify_otp.php?resend=1" class="text-blue-400 <?php echo $cooldownTimeLeft > 0 ? 'opacity-50 cursor-not-allowed' : 'hover:underline'; ?>" <?php echo $cooldownTimeLeft > 0 ? 'disabled' : ''; ?>>
                Kirim ulang
            </a>
            <span id="timer" class="text-gray-400" <?php echo $cooldownTimeLeft > 0 ? '' : 'style="display:none;"'; ?>>
                (<?php echo $cooldownTimeLeft; ?>)
            </span>
        </p>
    </div>
</body>
</html>