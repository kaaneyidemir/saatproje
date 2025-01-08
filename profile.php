<?php
session_start();
include('db.php'); // Veritabanı bağlantısı

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

$username = $_SESSION['username'];

// Veritabanından mevcut kullanıcı bilgilerini alalım
$query = "SELECT name, email FROM users WHERE username = :username";
$stmt = $conn->prepare($query);
$stmt->bindValue(':username', $username, PDO::PARAM_STR);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['updateEmail'])) {
        // E-posta güncelleme işlemi
        $old_email = $_POST['old_email'];
        $new_email = $_POST['new_email'];
        $confirm_email = $_POST['confirm_email'];

        if ($old_email !== $user['email']) {
            $message = "Eski e-posta adresi hatalı!";
        } elseif ($new_email !== $confirm_email) {
            $message = "Yeni e-posta adresleri eşleşmiyor!";
        } else {
            // E-posta doğru ise güncelleme
            $update_query = "UPDATE users SET email = :email WHERE username = :username";
            $stmt = $conn->prepare($update_query);
            $stmt->bindValue(':email', $new_email, PDO::PARAM_STR);
            $stmt->bindValue(':username', $username, PDO::PARAM_STR);

            if ($stmt->execute()) {
                $message = "E-posta başarıyla güncellendi!";
            } else {
                $message = "E-posta güncellenirken bir hata oluştu!";
            }
        }
    } elseif (isset($_POST['updatePassword'])) {
        // Şifre güncelleme işlemi
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];

        // Şifreyi kontrol et
        $query = "SELECT password FROM users WHERE username = :username";
        $stmt = $conn->prepare($query);
        $stmt->bindValue(':username', $username, PDO::PARAM_STR);
        $stmt->execute();
        $user_data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (password_verify($current_password, $user_data['password'])) {
            $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
            $update_query = "UPDATE users SET password = :password WHERE username = :username";
            $stmt = $conn->prepare($update_query);
            $stmt->bindValue(':password', $new_password_hash, PDO::PARAM_STR);
            $stmt->bindValue(':username', $username, PDO::PARAM_STR);

            if ($stmt->execute()) {
                $message = "Şifre başarıyla güncellendi!";
            } else {
                $message = "Şifre güncellenirken bir hata oluştu!";
            }
        } else {
            $message = "Mevcut şifreniz hatalı!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil - SAAT2M</title>
    <link rel="stylesheet" href="style.css">
    <script>
        function togglePassword() {
            var passwordField = document.getElementById("new_password");
            var checkbox = document.getElementById("showPassword");
            if (checkbox.checked) {
                passwordField.type = "text";
            } else {
                passwordField.type = "password";
            }
        }

        function validateForm() {
            var name = document.getElementById("name").value;
            var email = document.getElementById("email").value;
            if (name == "" || email == "") {
                alert("Ad ve e-posta alanları boş olamaz.");
                return false;
            }
            return true;
        }
    </script>
</head>
<body>
    <header>
        <h1><a href="index.php"><img src="./images/logo2.png" alt="" style="width:50px"></a></h1>
        <nav>
            <a href="index.php">Ana Sayfa</a>
            <a href="products.php">Ürünler</a>
            <a href="contact.php">İletişim</a>
            <a href="profile.php">Profil</a>
            <a href="logout.php">Çıkış</a>
        </nav>
    </header>

    <main>
        <h2>Profil Bilgileriniz</h2>

        <?php if (isset($message)) { echo "<p>$message</p>"; } ?>

        <form method="POST">
            <h3>E-posta Değiştir</h3>
            <label for="old_email">Eski E-posta:</label>
            <input type="email" id="old_email" name="old_email" required>

            <label for="new_email">Yeni E-posta:</label>
            <input type="email" id="new_email" name="new_email" required>

            <label for="confirm_email">Yeni E-posta Doğrulama:</label>
            <input type="email" id="confirm_email" name="confirm_email" required>

            <button type="submit" name="updateEmail">E-posta Değiştir</button>
        </form>

        <form method="POST">
            <h3>Şifre Değiştir</h3>
            <label for="current_password">Mevcut Şifre:</label>
            <input type="password" id="current_password" name="current_password" required>

            <label for="new_password">Yeni Şifre:</label>
            <input type="password" id="new_password" name="new_password" required>

            <label><input type="checkbox" id="showPassword" onclick="togglePassword()"> Şifreyi Göster</label>

            <button type="submit" name="updatePassword">Şifre Değiştir</button>
        </form>
    </main>

    <footer>
        
    </footer>
</body>
</html>
