<?php
session_start();
$host = "localhost";
$kullanici = "root";
$sifre = "";
$veritabani = "test";
$baglanti = new mysqli($host, $kullanici, $sifre, $veritabani);

if ($baglanti->connect_error) {
    die("Bağlantı hatası: " . $baglanti->connect_error);
}

// Sepet başlat
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Sepetten ürün kaldırma işlemi
if (isset($_GET['remove_id'])) {
    $remove_id = $_GET['remove_id'];
    if (isset($_SESSION['cart'][$remove_id])) {
        unset($_SESSION['cart'][$remove_id]);
    }
    header("Location: cart_view.php");
    exit;
}

// Satın alma işlemi
if (isset($_POST['purchase'])) {
    $user_id = $_SESSION['user_id'] ?? 0; // Kullanıcı giriş yaptıysa ID'yi alın
    if ($user_id == 0) {
        echo "<p style='color: red; text-align: center;'>Lütfen giriş yapın!</p>";
        exit;
    }

    $cart = $_SESSION['cart'];
    if (!empty($cart)) {
        $is_purchase_successful = true;
        foreach ($cart as $product_id => $quantity) {
            // Ürün bilgilerini al
            $sql = "SELECT * FROM urunler WHERE id = ?";
            $stmt = $baglanti->prepare($sql);
            $stmt->bind_param("i", $product_id);
            $stmt->execute();
            $urun = $stmt->get_result()->fetch_assoc();

            if ($urun && $urun['stok'] >= $quantity) {
                $urun_toplam = $urun['fiyat'] * $quantity; // Toplam fiyat hesapla

                // Siparişi veritabanına ekle
                $sql_insert = "INSERT INTO orders (user_id, product_id, quantity, total_price, status) VALUES (?, ?, ?, ?, 'pending')";
                $stmt_insert = $baglanti->prepare($sql_insert);
                $stmt_insert->bind_param("iiid", $user_id, $product_id, $quantity, $urun_toplam);
                $stmt_insert->execute();

                // Stoktan düşür
                $new_stock = $urun['stok'] - $quantity;
                $sql_update_stock = "UPDATE urunler SET stok = ? WHERE id = ?";
                $stmt_update_stock = $baglanti->prepare($sql_update_stock);
                $stmt_update_stock->bind_param("ii", $new_stock, $product_id);
                $stmt_update_stock->execute();
            } else {
                // Yeterli stok yoksa işlem başarısız
                $is_purchase_successful = false;
                echo "<p style='color: red; text-align: center;'>Ürün: {$urun['urun_adi']} için yeterli stok yok. Satın alma işlemi iptal edildi.</p>";
                break;
            }
        }

        // Eğer her şey başarılıysa, sepeti temizle
        if ($is_purchase_successful) {
            $_SESSION['cart'] = [];
            echo "<p style='color: green; text-align: center;'>Satın alma işlemi başarıyla tamamlandı.</p>";
            echo "<div style='text-align: center; margin-top: 20px;'><a href='index.php' class='checkout-btn' style='padding: 10px 20px; background-color: #28a745; color: #fff; text-decoration: none; border-radius: 5px;'>Ana Sayfaya Dön</a></div>";
        }
    } else {
        echo "<p style='color: red; text-align: center;'>Sepetiniz boş, satın alma işlemi yapılamaz.</p>";
    }
}

$sepet = $_SESSION['cart'];
$toplam_tutar = 0;
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Sepetiniz</title>
    <style>
        /* Tarz ayarları */
        body { font-family: Arial, sans-serif; background-color: #f9f9f9; margin: 0; padding: 20px; }
        h1 { text-align: center; }
        .cart-container { width: 80%; margin: 20px auto; background-color: #fff; padding: 20px; border-radius: 8px; }
        .cart-item { display: flex; justify-content: space-between; align-items: center; padding: 15px 0; border-bottom: 1px solid #ddd; }
        .total, .checkout-btn { text-align: center; margin: 20px 0; }
        .checkout-btn { padding: 10px 20px; background-color: #28a745; color: #fff; border: none; border-radius: 5px; cursor: pointer; }
        .login-btn { padding: 10px 20px; background-color: #007bff; color: #fff; border: none; border-radius: 5px; cursor: pointer; }
        .login-btn a { color: #fff; text-decoration: none; }
    </style>
</head>
<body>
    <h1>Sepetiniz</h1>
    <div class="cart-container">
        <?php if (!isset($_SESSION['user_id'])): ?>
            <p style="text-align: center;">Lütfen giriş yapın!</p>
            <div style="text-align: center;">
                <button class="login-btn"><a href="login.php">Giriş Yap</a></button>
            </div>
        <?php else: ?>
            <?php if (!empty($sepet)): ?>
                <?php
                $id_listesi = implode(',', array_keys($sepet));
                $sql = "SELECT * FROM urunler WHERE id IN ($id_listesi)";
                $sonuc = $baglanti->query($sql);

                while ($urun = $sonuc->fetch_assoc()):
                    $urun_id = $urun['id'];
                    $adet = $sepet[$urun_id];
                    $urun_toplam = $urun['fiyat'] * $adet;
                    $toplam_tutar += $urun_toplam;
                ?>
                    <div class="cart-item">
                        <div>
                            <h3><?php echo htmlspecialchars($urun['urun_adi']); ?></h3>
                            <p>Fiyat: <?php echo number_format($urun['fiyat'], 2); ?>&#8378;</p>
                            <p>Adet: <?php echo $adet; ?></p>
                            <p>Toplam: <?php echo number_format($urun_toplam, 2); ?>&#8378;</p>
                        </div>
                        <a href="cart_view.php?remove_id=<?php echo $urun_id; ?>">X</a>
                    </div>
                <?php endwhile; ?>
                <div class="total">
                    Toplam Tutar: <?php echo number_format($toplam_tutar, 2); ?>&#8378;
                </div>
                <form method="post" action="">
                    <button type="submit" name="purchase" class="checkout-btn">Satın Al</button>
                </form>
            <?php else: ?>
                <p>Sepetiniz boş!</p>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</body>
</html>
