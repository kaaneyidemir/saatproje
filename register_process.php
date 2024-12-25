<?php
session_start();
require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Veritabanı bağlantısı
$conn = new mysqli("localhost", "root", "", "test");

if ($conn->connect_error) {
    die("Veritabanı bağlantısı başarısız: " . $conn->connect_error);
}

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Token'ı veritabanında kontrol et
    $query = "SELECT * FROM users WHERE reset_token = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Token geçerli, şifre yenileme formunu göster
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $new_password = $_POST["new_password"];
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

            // Yeni şifreyi veritabanına kaydet
            $query = "UPDATE users SET password = ?, reset_token = NULL WHERE reset_token = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ss", $hashed_password, $token);
            $stmt->execute();

            // Başarılı mesajı
            $success = "Şifreniz başarıyla değiştirildi.";
        }
    } else {
        $error = "Geçersiz token.";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Şifre Yenile</title>
</head>
<body>
    <form action="reset_password.php?token=<?php echo $token; ?>" method="POST">
        <h2>Yeni Şifre Belirleyin</h2>
        <label for="new_password">Yeni Şifre:</label>
        <input type="password" id="new_password" name="new_password" required>

        <button type="submit">Şifreyi Değiştir</button>

        <?php if (isset($error)) { echo "<p style='color: red;'>$error</p>"; } ?>
        <?php if (isset($success)) { echo "<p style='color: green;'>$success</p>"; } ?>
    </form>
</body>
</html>
