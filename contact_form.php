<?php
// PHPMailer sınıflarını dahil et
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Composer autoloader'ı dahil et
require 'vendor/autoload.php'; // PHPMailer autoloader'ı

// Form verileri kontrolü ve işleme
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Form verilerini al
    $name = $_POST['name'];
    $email = $_POST['email'];
    $message = $_POST['message'];

    // Boş alan kontrolü
    if (empty($name) || empty($email) || empty($message)) {
        echo "Lütfen tüm alanları doldurduğunuzdan emin olun.";
    } else {
        // PHPMailer ile e-posta gönderme işlemi
        $mail = new PHPMailer(true);

        try {
            // SMTP kullanımı
            $mail->isSMTP(); // SMTP üzerinden gönderim
            $mail->Host = 'smtp.gmail.com'; // Gmail SMTP sunucusu
            $mail->SMTPAuth = true; // SMTP kimlik doğrulaması
            $mail->Username = 'eyidemirkaan@gmail.com'; // Gönderen e-posta adresi
            $mail->Password = 'xlse rbgr koso ahyx'; // Uygulama şifresi veya normal şifre
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // STARTTLS şifrelemesi
            $mail->Port = 587; // Gmail SMTP için port

            // Gönderen ve alıcı ayarları
            $mail->setFrom($email, $name); // Gönderen e-posta
            $mail->addAddress('eyidemirkaan@gmail.com'); // Alıcı e-posta adresi
            $mail->addReplyTo($email, $name); // Yanıt adresi

            // E-posta içeriği
            $mail->isHTML(true); // HTML formatında e-posta
            $mail->Subject = 'Yeni Haber Formu Mesaji';
            $mail->Body = "<h3>Mesaj Detayları:</h3><p><b>Ad:</b> $name</p><p><b>E-posta:</b> $email</p><p><b>Mesaj:</b><br>$message</p>";

            // Mesajı gönder
            $mail->send();
            echo "Mesajınız başarıyla gönderildi. Teşekkür ederiz!";
            // Ana sayfaya dönme butonunu ekle
            echo '<br><a href="index.php"><button>Ana Sayfaya Dön</button></a>';
        } catch (Exception $e) {
            echo "Mesaj gönderilemedi. Hata: {$mail->ErrorInfo}";
        }
    }
} else {
    // Form GET methodu ile erişiliyorsa, kullanıcıyı yönlendirme
    header("Location: contact.php");
    exit();
}
?>
