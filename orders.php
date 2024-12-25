<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include('db.php');

// Kullanıcı kontrolü
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Sipariş geçmişini sorgulama
$sql = "SELECT o.*, u.urun_adi, u.fiyat FROM orders o JOIN urunler u ON o.product_id = u.id WHERE o.user_id = :user_id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Siparişlerim</title>
    <link rel="stylesheet" href="style.css">
    <style>
        main {
            padding: 20px;
            max-width: 1200px;
            margin: 20px auto;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 0 auto;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        th, td {
            padding: 12px 15px;
            text-align: left;
            border: 1px solid #ddd;
            font-size: 16px;
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

        td {
            font-size: 14px;
        }

        p {
            text-align: center;
            font-size: 18px;
            color: #777;
        }

    </style>
</head>
<body>
    <header>
        <h1>Siparişlerim</h1>
        <nav>
            <a href="index.php">Ana Sayfa</a>
            <a href="logout.php">Çıkış</a>
        </nav>
    </header>

    <main>
        <h2>Geçmiş Siparişleriniz</h2>
        <?php if (!empty($orders)): ?>
            <table>
                <tr>
                    <th>Sipariş Numarası</th>
                    <th>Ürün Adı</th>
                    <th>Adet</th>
                    <th>Toplam Fiyat</th>
                    <th>Durum</th>
                </tr>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($order['order_number']); ?></td>
                        <td><?php echo htmlspecialchars($order['urun_adi']); ?></td>
                        <td><?php echo htmlspecialchars($order['quantity']); ?></td>
                        <td><?php echo number_format($order['fiyat'] * $order['quantity'], 2); ?>₺</td>
                        <td>
                            <?php 
                            if ($order['order_status'] == 'onaylandi') {
                                echo "Onaylandı, Hazırlanıyor";
                            } elseif ($order['order_status'] == 'pending') {
                                echo "Beklemede";
                            } else {
                                echo "Tamamlandı";
                            }
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>Henüz siparişiniz bulunmamaktadır.</p>
        <?php endif; ?>
    </main>
</body>
</html>
