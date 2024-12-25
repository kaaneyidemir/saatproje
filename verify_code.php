<?php
session_start();

// Veritabanı bağlantısı
$conn = new mysqli("localhost", "root", "", "test");

if ($conn->connect_error) {
    die("Veritabanı bağlantısı başarısız: " . $conn->connect_error);
}

// Eğer kullanıcı oturum açmamışsa, giriş sayfasına yönlendir
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $entered_code = $_POST["verification_code"];

    // Kullanıcının doğrulama kodunu veritabanından al
    $query = "SELECT verification_code FROM users WHERE id = " . $_SESSION['user_id'];
    $result = $conn->query($query);
    $user = $result->fetch_assoc();

    // Kodu karşılaştır
    if ($user['verification_code'] == $entered_code) {
        // Başarılı doğrulama
        $_SESSION['verified'] = true;
        header("Location: index.php"); // Ana sayfaya yönlendir
        exit();
    } else {
        $error = "Doğrulama kodu yanlış.";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doğrulama Kodu</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <form action="verify_code.php" method="POST">
        <h2>Doğrulama Kodu</h2>
        <label for="verification_code">Doğrulama Kodu:</label>
        <input type="text" id="verification_code" name="verification_code" required>

        <button type="submit">Doğrula</button>

        <?php if (isset($error)) { echo "<p style='color: red;'>$error</p>"; } ?>
    </form>
</body>
</html>
