<?php
session_start();
require_once 'db_connection.php'; // Veritabanı bağlantısını sağla

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment']) && isset($_POST['order_id'])) {
    $comment = $_POST['comment'];
    $orderId = $_POST['order_id'];

    // Yorumun veritabanına kaydedilmesi
    $stmt = $pdo->prepare("UPDATE orders SET comment = :comment WHERE id = :order_id");
    $stmt->execute([':comment' => $comment, ':order_id' => $orderId]);

    // Başarıyla kaydedildiyse kullanıcıyı iletişim sayfasına yönlendir
    header("Location: contact.php");
    exit;
}
?>
