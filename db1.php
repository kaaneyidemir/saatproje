<?php
$host = "localhost"; // Sunucu adresi (genellikle localhost)
$dbname = "saat_satis"; // Veritabanı adı
$username = "root"; // Veritabanı kullanıcı adı
$password = ""; // Veritabanı şifresi

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Veritabanı bağlantısı başarısız: " . $e->getMessage());
}
?>
