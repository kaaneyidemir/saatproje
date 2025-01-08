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
    // Formdan gelen yeni bilgilerle güncelleme işlemi
    $new_name = $_POST['name'];
    $new_email = $_POST['email'];
    $new_password = $_POST['password'];
    
    // E-posta değiştirilmişse, doğrulama yapılmalı
    if ($new_email !== $user['email']) {
        // Burada e-posta doğrulama işlemi yapılabilir, örneğin doğrulama e-postası gönderilebilir
    }

    // Eğer şifre değiştirilmişse
    if ($new_password) {
        $new_password = password_hash($new_password, PASSWORD_DEFAULT); // Şifreyi hash'le
        $update_query = "UPDATE users SET name = :name, email = :email, password = :password WHERE username = :username";
        $stmt = $conn->prepare($update_query);
        $stmt->bindValue(':name', $new_name, PDO::PARAM_STR);
        $stmt->bindValue(':email', $new_email, PDO::PARAM_STR);
        $stmt->bindValue(':password', $new_password, PDO::PARAM_STR);
        $stmt->bindValue(':username', $username, PDO::PARAM_STR);
    } else {
        $update_query = "UPDATE users SET name = :name, email = :email WHERE username = :username";
        $stmt = $conn->prepare($update_query);
        $stmt->bindValue(':name', $new_name, PDO::PARAM_STR);
        $stmt->bindValue(':email', $new_email, PDO::PARAM_STR);
        $stmt->bindValue(':username', $username, PDO::PARAM_STR);
    }

    if ($stmt->execute()) {
        $message = "Bilgiler başarıyla güncellendi!";
    } else {
        $message = "Güncelleme sırasında bir hata oluştu!";
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
            var passwordField = document.getElementById("password");
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

        <form method="POST" onsubmit="return validateForm()">
            <label for="name">Adınız:</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>

            <label for="email">E-posta:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

            <label for="password">Yeni Şifre (isteğe bağlı):</label>
            <input type="password" id="password" name="password">
            <label><input type="checkbox" id="showPassword" onclick="togglePassword()"> Şifreyi Göster</label>

            <button type="submit">Bilgileri Güncelle</button>
        </form>
    </main>

    <footer>
        <p>&copy; 2025 SAAT2M</p>
    </footer>
</body>
</html>
