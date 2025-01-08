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
$sql = "SELECT o.*, u.urun_adi, u.fiyat, u.id as product_id, o.comment FROM orders o JOIN urunler u ON o.product_id = u.id WHERE o.user_id = :user_id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Yorum ekleme işlemi
$commentAdded = false;
$commentFormHidden = false;  // Yeni değişken ekledik
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment']) && isset($_POST['order_id'])) {
    $comment = $_POST['comment'];
    $orderId = $_POST['order_id'];

    // Kullanıcı daha önce yorum yapmış mı kontrol et
    $stmt = $conn->prepare("SELECT comment FROM orders WHERE order_id = :order_id AND user_id = :user_id");
    $stmt->execute([':order_id' => $orderId, ':user_id' => $user_id]);
    $existingComment = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$existingComment['comment']) {
        // Yorumun veritabanına kaydedilmesi
        $stmt = $conn->prepare("UPDATE orders SET comment = :comment WHERE order_id = :order_id");
        $stmt->execute([':comment' => $comment, ':order_id' => $orderId]);

        $commentAdded = true;
        $commentFormHidden = true; // Yorum yapıldıysa formu gizleyelim
    }
}
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

        .comment-form {
            margin-top: 10px;
            background-color: #f9f9f9;
            padding: 10px;
            border-radius: 5px;
        }

        .comment-form textarea {
            width: 100%;
            padding: 10px;
            font-size: 14px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }

        .comment-form button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 10px;
        }

        .comment-form button:hover {
            background-color: #45a049;
        }

        .notification {
            display: none;
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: #4CAF50;
            color: white;
            padding: 15px;
            border-radius: 5px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            z-index: 1000;
        }

        .notification.show {
            display: block;
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
                    <th>Yorum</th>
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
                                echo "Hazırlanıyor, Teslim Edildi";
                            } elseif ($order['order_status'] == 'pending') {
                                echo "Beklemede";
                            } else {
                                echo "Tamamlandı";
                            }
                            ?>
                        </td>
                        <td>
                            <?php if (!$order['comment'] && !$commentFormHidden): ?>
                                <?php if ($order['order_status'] == 'onaylandi'): ?>
                                    <div class="comment-form">
                                        <form action="" method="POST">
                                            <textarea name="comment" placeholder="Yorumunuzu buraya yazın..." required></textarea>
                                            <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                            <button type="submit">Yorum Yap</button>
                                        </form>
                                    </div>
                                <?php endif; ?>
                            <?php elseif ($order['comment']): ?>
                                <p><?php echo htmlspecialchars($order['comment']); ?></p>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>Henüz siparişiniz bulunmamaktadır.</p>
        <?php endif; ?>
    </main>

    <div id="notification" class="notification">Yorumunuz başarıyla kaydedildi!</div>

    <script>
        <?php if ($commentAdded): ?>
        const notification = document.getElementById('notification');
        notification.classList.add('show');
        setTimeout(() => {
            notification.classList.remove('show');
        }, 3000);
        <?php endif; ?>
    </script>
</body>
</html>
