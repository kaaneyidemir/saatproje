<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include('db.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: index.php");
    exit();
}

if (isset($_GET['approve'])) {
    $order_id = (int)$_GET['approve'];
    $sql = "SELECT o.*, u.urun_adi, u.fiyat FROM orders o 
            JOIN urunler u ON o.product_id = u.id 
            WHERE o.order_id = :id AND o.order_status = 'pending'";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $order_id, PDO::PARAM_INT);
    $stmt->execute();
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($order) {
        $sql_update = "UPDATE orders SET order_status = 'onaylandi' WHERE order_id = :id";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bindParam(':id', $order_id, PDO::PARAM_INT);

        if ($stmt_update->execute()) {
            $product_id = $order['product_id'];
            $quantity = $order['quantity'];
            $sql_product = "SELECT * FROM urunler WHERE id = :product_id";
            $stmt_product = $conn->prepare($sql_product);
            $stmt_product->bindParam(':product_id', $product_id, PDO::PARAM_INT);
            $stmt_product->execute();
            $product = $stmt_product->fetch(PDO::FETCH_ASSOC);

            if ($product) {
                $new_stock = $product['stok'] - $quantity;
                if ($new_stock >= 0) {
                    $sql_update_stock = "UPDATE urunler SET stok = :stok WHERE id = :product_id";
                    $stmt_update_stock = $conn->prepare($sql_update_stock);
                    $stmt_update_stock->bindParam(':stok', $new_stock, PDO::PARAM_INT);
                    $stmt_update_stock->bindParam(':product_id', $product_id, PDO::PARAM_INT);
                    if ($stmt_update_stock->execute()) {
                        $user_id = $order['user_id'];
                        $sql_user = "SELECT * FROM users WHERE id = :user_id";
                        $stmt_user = $conn->prepare($sql_user);
                        $stmt_user->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                        $stmt_user->execute();
                        $user = $stmt_user->fetch(PDO::FETCH_ASSOC);

                        if ($user) {
                            $email = $user['email'];
                            $username = $user['username'];
                            $subject = "Siparis Onaylandi";
                            $total_price = $order['quantity'] * $order['fiyat'];
                            $message = "Merhaba $username,\n\n"
                                     . "Siparişiniz başarıyla onaylanmıştır. Ürününüz kısa süre içerisinde gönderilecektir.\n\n"
                                     . "Sipariş Detayları:\n"
                                     . "Ürün: " . $order['urun_adi'] . "\n"
                                     . "Adet: " . $order['quantity'] . "\n"
                                     . "Toplam Fiyat: " . number_format($total_price, 2) . "₺\n\n"
                                     . "Teşekkürler!";

                            $mail = new PHPMailer(true);
                            try {
                                $mail->isSMTP();
                                $mail->Host = 'smtp.gmail.com';
                                $mail->SMTPAuth = true;
                                $mail->Username = 'eyidemirkaan@gmail.com';
                                $mail->Password = 'xlse rbgr koso ahyx';
                                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                                $mail->Port = 587;
                                $mail->setFrom('youremail@example.com', 'Saat Satis');
                                $mail->addAddress($email, $username);
                                $mail->Subject = $subject;
                                $mail->Body    = $message;
                                $mail->send();

                                $_SESSION['message'] = "Sipariş başarıyla onaylandı, e-posta gönderildi ve stok güncellenmiştir.";
                                header("Location: admin_approve.php");
                                exit();
                            } catch (Exception $e) {
                                $_SESSION['message'] = "E-posta gönderilemedi. Hata: {$mail->ErrorInfo}";
                                header("Location: admin_approve.php");
                                exit();
                            }
                        } else {
                            $_SESSION['message'] = "Kullanıcı bilgileri alınamadı.";
                            header("Location: admin_approve.php");
                            exit();
                        }
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

$sql = "SELECT o.*, u.urun_adi, u.fiyat, us.username FROM orders o 
        JOIN urunler u ON o.product_id = u.id 
        JOIN users us ON o.user_id = us.id 
        WHERE o.order_status = 'pending'";
$stmt = $conn->prepare($sql);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Paneli</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
            color: #333;
        }
        header {
            background: #4CAF50;
            color: #fff;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        header h1 {
            margin: 0;
        }
        header nav a {
            color: #fff;
            text-decoration: none;
            margin-left: 15px;
        }
        main {
            padding: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background: #fff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        table th, table td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }
        table th {
            background: #4CAF50;
            color: #fff;
        }
        .message {
            padding: 10px;
            margin-bottom: 20px;
            background: #e7f3e7;
            color: #3c763d;
            border: 1px solid #d6e9c6;
            border-radius: 5px;
        }
        a {
            color: #4CAF50;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
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
                <th>Kullanıcı Adı</th>
                <th>Ürün Adı</th>
                <th>Adet</th>
                <th>Toplam Fiyat</th>
                <th>Onayla</th>
            </tr>
            <?php if (!empty($results)): ?>
                <?php foreach ($results as $row): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['order_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                        <td><?php echo htmlspecialchars($row['urun_adi']); ?></td>
                        <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                        <td><?php echo number_format($row['fiyat'] * $row['quantity'], 2); ?>₺</td>
                        <td>
                            <a href="admin_approve.php?approve=<?php echo htmlspecialchars($row['order_id']); ?>">Onayla</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">Henüz onaylanmamış sipariş bulunmamaktadır.</td>
                </tr>
            <?php endif; ?>
        </table>
    </main>
</body>
</html>
