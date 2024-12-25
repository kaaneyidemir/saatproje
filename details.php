<?php
$id = $_GET['id']; // Basit bir örnek, gerçek uygulamada güvenlik önlemleri alın.
$products = [
    1 => ["Klasik Saat", "₺500", "images/watch1.jpg"],
    2 => ["Spor Saat", "₺750", "images/watch2.jpg"]
];
$product = $products[$id];
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $product[0]; ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Saat Satış Sitesi</h1>
        <nav>
            <a href="index.php">Ana Sayfa</a>
            <a href="products.php">Ürünler</a>
            <a href="contact.php">İletişim</a>
        </nav>
    </header>
    <main>
        <h2><?php echo $product[0]; ?></h2>
        <img src="<?php echo $product[2]; ?>" alt="<?php echo $product[0]; ?>">
        <p>Fiyat: <?php echo $product[1]; ?></p>
    </main>
    <footer>
        <p>© 2024 Saat Satış Sitesi</p>
    </footer>
</body>
</html>
