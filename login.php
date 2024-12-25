
<?php
session_start(); // Oturum başlatma


// Kullanıcı giriş yaptıysa, oturum bilgilerini al
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
    $is_admin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1; // Admin kontrolü
} else {
    $username = null;
    $is_admin = false;
}



require 'vendor/autoload.php'; 
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Veritabanı bağlantısı
$conn = new mysqli("localhost", "root", "", "test");

if ($conn->connect_error) {
    die("Veritabanı bağlantısı başarısız: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Kullanıcıyı veritabanında ara
    $query = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Şifre kontrolü
        if (password_verify($password, $user['password'])) {
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['is_admin'] = ($user['is_admin'] == 1) ? 1 : 0; // Admin kontrolü
            // Eğer doğrulama kodu beklenmiyorsa oluştur
            if (!isset($_SESSION['verification_code'])) {

                // Doğrulama kodu oluştur ve oturuma kaydet
                $verification_code = rand(100000, 999999); // 6 haneli rastgele kod
                $_SESSION['verification_code'] = $verification_code;
                $_SESSION['username'] = $user['username'];
                $_SESSION['user_id'] = $user['id'];
                
                $_SESSION['verification_time'] = time(); // Şu anki zamanı oturuma kaydet

                // E-posta gönderimi
                $mail = new PHPMailer(true);
                try {
                    // PHPMailer SMTP ayarları
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'eyidemirkaan@gmail.com'; // Gmail adresiniz
                    $mail->Password = 'ylhi rvxr dmrx rsjs';    // Gmail uygulama şifreniz
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

                    // E-posta bilgileri
                    $mail->setFrom('eyidemirkaan@gmail.com', 'SAAT2M?'); // Gönderen adresi
                    $mail->addAddress($user['email']); // Kullanıcının e-posta adresi
                    $mail->Subject = 'Dogrulama Kodunuz';
                    $mail->Body = "Merhaba, giriş yapabilmeniz için doğrulama kodunuz: $verification_code";

                    // E-posta gönderme
                    $mail->send();
                    
                    // Kullanıcıyı doğrulama sayfasına yönlendir
                    $_SESSION['email_sent'] = true;
                    header("Location: verify.php");
                    exit();
                } catch (Exception $e) {
                    // E-posta gönderim hatası
                    $error = "E-posta gönderilemedi. Hata: " . $mail->ErrorInfo;
                    echo "<p style='color: red;'>$error</p>";
                }
            } else {
                // Doğrulama kodu zaten gönderildiyse hata mesajı
                echo "<p>Doğrulama kodu zaten gönderildi. Lütfen e-postanızı kontrol edin.</p>";
            }
        } else {
            // Şifre yanlış
            $error = "Şifre hatalı.";
            echo "<p style='color: red;'>$error</p>";
        }
    } else {
        // Kullanıcı bulunamadı
        $error = "Kullanıcı bulunamadı.";
        echo "<p style='color: red;'>$error</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giriş Yap</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <form action="login.php" method="POST">
        <h2>Giriş Yap</h2>
        <label for="username">Kullanıcı Adı:</label>
        <input type="text" id="username" name="username" required>

        <label for="password">Şifre:</label>
        <input type="password" id="password" name="password" required>

        <button type="submit">Giriş Yap</button>
        <p>Hesabınız yok mu? <a href="register.php">Kayıt Ol</a></p>

        <!-- Şifremi Unuttum Bağlantısı -->
        <p><a href="forgot_password.php">Şifremi Unuttum</a></p>

        <?php if (isset($error)) { echo "<p style='color: red;'>$error</p>"; } ?>
    </form>
</body>
</html>
