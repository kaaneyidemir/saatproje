
<?php
session_start(); // Oturumu başlat



// Kullanıcıyı oturumdan çıkar
session_unset(); // Oturum verilerini temizle
session_destroy(); // Oturumu sonlandır

// Ana sayfaya yönlendir
header("Location: index.php");
exit();
?>
