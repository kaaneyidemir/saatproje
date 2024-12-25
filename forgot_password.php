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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];

    // Kullanıcıyı e-posta ile ara
    $query = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Şifre sıfırlama bağlantısı oluştur
        $reset_token = bin2hex(random_bytes(16)); // Benzersiz bir token oluştur

        // Veritabanına token'ı kaydet
        $user = $result->fetch_assoc();
        $query = "UPDATE users SET reset_token = ? WHERE email = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $reset_token, $email);
        $stmt->execute();

        // E-posta gönderimi
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'eyidemirkaan@gmail.com'; // Gmail adresiniz
            $mail->Password = 'ylhi rvxr dmrx rsjs';    // Gmail uygulama şifreniz
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('eyidemirkaan@gmail.com', 'SAATİM?'); // Gönderen adresi
            $mail->addAddress($email); // Kullanıcının e-posta adresi
            $mail->Subject = 'Sifre Yenileme';
            $mail->Body = "Sifre Yenilemek İçin Linke Tıklayin;\n\n" .
                          "http://localhost/SAATPROJE/reset_password.php?token=$reset_token";

            $mail->send();
            $success = "Şifre sıfırlama bağlantısı e-posta adresinize gönderildi.";

        } catch (Exception $e) {
            $error = "E-posta gönderilemedi. Hata: " . $mail->ErrorInfo;
        }
    } else {
        $error = "Bu e-posta adresi ile kayıtlı bir kullanıcı bulunamadı.";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Şifremi Unuttum</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        h2 {
            color: #333;
            margin-bottom: 20px;
        }

        label {
            display: block;
            text-align: left;
            margin-bottom: 8px;
            font-weight: bold;
            color: #555;
        }

        input[type="email"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        p {
            font-size: 14px;
            margin-top: 15px;
        }

        .error {
            color: red;
            font-size: 14px;
        }

        .success {
            color: green;
            font-size: 14px;
        }
    </style>
</head>
<body>

    <div class="container">
        <form action="forgot_password.php" method="POST">
            <h2>Şifremi Unuttum</h2>
            <label for="email">E-posta Adresiniz:</label>
            <input type="email" id="email" name="email" required>
            <button type="submit">E-posta Gönder</button>

            <?php if (isset($error)) { echo "<p class='error'>$error</p>"; } ?>
            <?php if (isset($success)) { echo "<p class='success'>$success</p>"; } ?>
        </form>
    </div>

</body>
</html>
