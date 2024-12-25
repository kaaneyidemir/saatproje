<?php
session_start(); // Oturumu başlat

// Veritabanı bağlantısı
$host = "localhost";
$dbname = "test";
$user = "root";
$password = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Veritabanı bağlantı hatası: " . $e->getMessage());
}

// Sepet kontrolü
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    echo "<h1>Sepetiniz boş!</h1>";
    exit;
}

// Kart bilgilerini doğrulama (Temel Kontrol)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $cardnumber = $_POST['cardnumber'];
    $expiration = $_POST['expiration'];
    $cvv = $_POST['cvv'];

    if (empty($name) || empty($cardnumber) || empty($expiration) || empty($cvv)) {
        echo "<h1>Eksik bilgi girdiniz!</h1>";
        exit;
    }
} else {
    echo "Geçersiz istek!";
    exit;
}

// Sepetteki ürünleri kontrol et ve stoktan düş
foreach ($_SESSION['cart'] as $urun_id => $adet) {
    $adet = (int)$adet;

    // Ürünün stok bilgisini al
    $stmt = $pdo->prepare("SELECT stok, urun_adi FROM urunler WHERE id = ?");
    $stmt->execute([$urun_id]);
    $urun = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$urun) {
        echo "<p>Ürün bulunamadı. ID: $urun_id</p>";
        exit;
    }

    // Stok kontrolü
    if ($urun['stok'] >= $adet) {
        // Stok güncelle
        $yeni_stok = $urun['stok'] - $adet;
        $update_stmt = $pdo->prepare("UPDATE urunler SET stok = ? WHERE id = ?");
        $update_stmt->execute([$yeni_stok, $urun_id]);
    } else {
        echo "<p>Yetersiz stok: " . htmlspecialchars($urun['urun_adi']) . "</p>";
        exit;
    }
}

// Sepeti temizle
unset($_SESSION['cart']); // Sepeti temizle
session_write_close(); // Oturum yazmayı kapat

// Başarı mesajı
echo "<h1>Ödeme başarılı!</h1>";
echo "<p>Siparişiniz onaylandı ve stoklar güncellendi.</p>";
echo "<a href='products.php'>Alışverişe Devam Et</a>";
?>
