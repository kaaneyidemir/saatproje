<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['urun_id'])) {
    $urun_id = intval($_POST['urun_id']);

    // Eğer sepet dizisi tanımlı değilse başlat
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Ürün ID'sine göre adet artırma
    if (!isset($_SESSION['cart'][$urun_id])) {
        $_SESSION['cart'][$urun_id] = 1; // İlk kez eklenirse 1 adet
    } else {
        $_SESSION['cart'][$urun_id] += 1; // Sepette varsa adet artır
    }

    // Sepetteki toplam ürün sayısını hesapla
    $total_items = array_sum($_SESSION['cart']);
    echo json_encode(['status' => 'success', 'count' => $total_items]);
    exit;
}

echo json_encode(['status' => 'error']);
exit;
?>
