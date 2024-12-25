

<?php
$host = "localhost"; // Sunucu adresi (genellikle localhost)
$dbname = "test"; // Veritabanı adı (kendi veritabanı adınızı yazın)
$username = "root"; // Veritabanı kullanıcı adı
$password = ""; // Veritabanı şifresi

try {
    // PDO ile veritabanı bağlantısı
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    
    // Hata yönetimini etkinleştirme
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Bağlantı hatası durumunda mesaj
    die("Veritabanı bağlantısı başarısız: " . $e->getMessage());
}
?>

