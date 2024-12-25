<?php
session_start(); // Oturum başlatma

// Eğer kullanıcı zaten giriş yapmışsa, onları ana sayfaya yönlendir
if (isset($_SESSION['username'])) {
    header('Location: index.php');
    exit();
}

// Veritabanı bağlantısı
$host = "localhost"; // Sunucu adresi (genellikle localhost)
$dbname = "test"; // Veritabanı adı
$username = "root"; // Veritabanı kullanıcı adı
$password = ""; // Veritabanı şifresi

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Veritabanı bağlantı hatası: " . $e->getMessage());
}

$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Form verilerini al
    $username = htmlspecialchars(trim($_POST['username']));
    $email = htmlspecialchars(trim($_POST['email']));
    $phone = htmlspecialchars(trim($_POST['phone'])); // Telefon numarasını al
    $password = trim($_POST['password']);
    
    // Şifreyi hash'le
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Alanların dolu olup olmadığını kontrol et
    if (!empty($username) && !empty($email) && !empty($password)) {
        try {
            // Aynı kullanıcı adı veya e-posta zaten var mı kontrol et
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username OR email = :email");
            $stmt->execute(['username' => $username, 'email' => $email]);
            
            if ($stmt->rowCount() > 0) {
                $error_message = "Bu kullanıcı adı veya e-posta zaten kullanılıyor!";
            } else {
                // Yeni kullanıcıyı ekle
                $insert_stmt = $pdo->prepare("INSERT INTO users (username, email, phone, password) VALUES (:username, :email, :phone, :password)");
                $insert_stmt->execute(['username' => $username, 'email' => $email, 'phone' => $phone, 'password' => $hashed_password]);
                
                $success_message = "Kayıt başarılı! Giriş yapmak için <a href='login.php'>buraya tıklayın</a>.";
            }
        } catch (PDOException $e) {
            $error_message = "Kayıt sırasında bir hata oluştu: " . $e->getMessage();
        }
    } else {
        $error_message = "Lütfen tüm alanları doldurunuz!";
    }
}

?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kayıt Ol</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <form action="" method="POST">
        <h2>Kayıt Ol</h2>

        <?php if ($success_message): ?>
            <p style="color: green;"><?php echo $success_message; ?></p>
        <?php endif; ?>

        <?php if ($error_message): ?>
            <p style="color: red;"><?php echo $error_message; ?></p>
        <?php endif; ?>

        <label for="username">Kullanıcı Adı:</label>
        <input type="text" id="username" name="username" required>

        <label for="email">E-posta:</label>
        <input type="email" id="email" name="email" required>

        <label for="phone">Telefon Numarası:</label>
        <input type="text" id="phone" name="phone">

        <label for="password">Şifre:</label>
        <input type="password" id="password" name="password" required>

        <button type="submit">Kayıt Ol</button>
        <p>Zaten bir hesabınız var mı? <a href="login.php">Giriş Yap</a></p>
    </form>
</body>
</html>
