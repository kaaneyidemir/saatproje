<?php
error_reporting(E_ALL); // Tüm hataları göster
ini_set('display_errors', 1); // Hataları ekran üzerinde göster

session_start(); // Oturum başlatma
include('db.php'); // Veritabanı bağlantısını dahil et

// Admin kontrolü
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: index.php");
    exit();
}

// Sipariş onaylama işlemi
if (isset($_GET['approve'])) {
    $order_id = (int)$_GET['approve']; // Güvenlik için integer dönüşümü
    // Sipariş bilgilerini al
    $sql = "SELECT * FROM orders WHERE order_id = :id AND order_status = 'pending'";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $order_id, PDO::PARAM_INT);
    $stmt->execute();
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($order) {
        // Onaylandı olarak güncelle
        $sql_update = "UPDATE orders SET order_status = 'onaylandi' WHERE order_id = :id"; // Onay durumu güncelleme
        echo "<script>console.log('SQL sorgusu: " . $sql_update . "');</script>"; // Konsola SQL sorgusunu yazdır
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bindParam(':id', $order_id, PDO::PARAM_INT);
        
        if ($stmt_update->execute()) {
            // Ürün stokunu azalt
            $product_id = $order['product_id'];
            $quantity = $order['quantity'];

            // Ürünün mevcut stok bilgisini al
            $sql_product = "SELECT * FROM urunler WHERE id = :product_id";
            $stmt_product = $conn->prepare($sql_product);
            $stmt_product->bindParam(':product_id', $product_id, PDO::PARAM_INT);
            $stmt_product->execute();
            $product = $stmt_product->fetch(PDO::FETCH_ASSOC);

            if ($product) {
                // Stoktan düşür
                $new_stock = $product['stok'] - $quantity;
                if ($new_stock >= 0) {
                    // Stok güncelleme
                    $sql_update_stock = "UPDATE urunler SET stok = :stok WHERE id = :product_id";
                    $stmt_update_stock = $conn->prepare($sql_update_stock);
                    $stmt_update_stock->bindParam(':stok', $new_stock, PDO::PARAM_INT);
                    $stmt_update_stock->bindParam(':product_id', $product_id, PDO::PARAM_INT);
                    if ($stmt_update_stock->execute()) {
                        $_SESSION['message'] = "Sipariş başarıyla onaylandı ve stok güncellenmiştir.";
                        header("Location: admin_approve.php"); // Sayfayı yenileyelim
                        exit();
                    } else {
                        $_SESSION['message'] = "Stok güncelleme sırasında hata oluştu.";
                        header("Location: admin_approve.php");
                        exit();
                    }
                } else {
                    $_SESSION['message'] = "Yeterli stok bulunmamaktadır!";
                    header("Location: admin_approve.php");
                    exit();
                }
            } else {
                $_SESSION['message'] = "Ürün bulunamadı.";
                header("Location: admin_approve.php");
                exit();
            }
        } else {
            $_SESSION['message'] = "Onay işlemi sırasında hata oluştu.";
            header("Location: admin_approve.php");
            exit();
        }
    } else {
        $_SESSION['message'] = "Geçersiz sipariş.";
        header("Location: admin_approve.php");
        exit();
    }
}

// Onaylanmamış ürünleri listele
$sql = "SELECT o.*, u.urun_adi, u.fiyat FROM orders o JOIN urunler u ON o.product_id = u.id WHERE o.order_status = 'pending'";
$stmt = $conn->prepare($sql);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Paneli - Ürün Onaylama</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        header {
            background-color: #004d00;
            color: white;
            padding: 15px;
            text-align: center;
        }
        nav a {
            color: white;
            margin: 0 15px;
            text-decoration: none;
            font-weight: bold;
        }
        nav a:hover {
            text-decoration: underline;
        }
        main {
            padding: 20px;
            max-width: 1200px;
            margin: auto;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1, h2 {
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 12px;
            text-align: center;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        a {
            text-decoration: none;
            color: #fff;
            background-color: #4CAF50;
            padding: 8px 16px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        a:hover {
            background-color: #45a049;
        }
        .message {
            padding: 10px;
            margin: 15px 0;
            background-color: #ffdd57;
            border-left: 5px solid #ff9c00;
            color: #333;
            border-radius: 3px;
        }
    </style>
</head>
<body>
    <header>
        <h1>Admin Paneli</h1>
        <nav>
            <a href="index.php">Ana Sayfa</a>
            <a href="logout.php">Çıkış</a>
        </nav>
    </header>

    <main>
        <h2>Onaylanmamış Siparişler</h2>
        <?php if (isset($_SESSION['message'])): ?>
            <div class="message"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></div>
        <?php endif; ?>
        <table>
            <tr>
                <th>Sipariş Numarası</th>
                <th>Ürün Adı</th>
                <th>Adet</th>
                <th>Toplam Fiyat</th>
                <th>Onayla</th>
            </tr>
            <?php if (!empty($results)): ?>
                <?php foreach ($results as $row): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['order_number']); ?></td>
                        <td><?php echo htmlspecialchars($row['urun_adi']); ?></td>
                        <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                        <td><?php echo number_format($row['fiyat'] * $row['quantity'], 2); ?>₺</td>
                        <td>
                            <?php
                            if (isset($row['order_id'])) {
                                echo '<a href="admin_approve.php?approve=' . htmlspecialchars($row['order_id']) . '">Onayla</a>';
                            } else {
                                echo 'ID bulunamadı';
                            }
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">Henüz onaylanmamış sipariş bulunmamaktadır.</td>
                </tr>
            <?php endif; ?>
        </table>
    </main>
</body>
</html>
