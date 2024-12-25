<?php
session_start(); // Oturum başlatma

// Kullanıcı giriş yaptıysa, oturum bilgilerini al
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username']; // Oturumda kayıtlı kullanıcı adını al
    $is_admin = ($_SESSION['username'] == 'admin' && isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1); // Admin kontrolü
} else {
    $username = null;
    $is_admin = false;
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SAAT2M</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <script src="scripts.js"></script>
</head>
<body>
    <header>
        <h1><a href="index.php"><i class="fa-solid fa-hippo"></i></a></h1>
        <nav>
            <a href="index.php">Ana Sayfa</a>
            <a href="products.php">Ürünler</a>
            <a href="contact.php">İletişim</a>

            <?php if ($username): ?>
                <a href="logout.php"><i class="fa-regular fa-user"></i> Çıkış</a>
                
                <!-- Admin için Ürün Ekleme butonu -->
                <?php if ($is_admin): ?>
                    <a href="addproducts.php">
                        <button>Ürün Ekle</button>
                    </a>
                    <!-- Admin için Sipariş Onay butonu -->
                    <a href="admin_approve.php">
                        <button>Sipariş Onay</button>
                    </a>
                <?php endif; ?>
                
                <!-- Kullanıcı için Siparişlerim butonu -->
                <a href="orders.php">Siparişlerim</a>
            <?php else: ?>
                <a href="login.php"><i class="fa-regular fa-user"></i> Giriş Yap</a>
            <?php endif; ?>
        </nav>
    </header>
</body>
</html>
