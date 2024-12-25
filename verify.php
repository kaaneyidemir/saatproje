<?php
session_start(); // Oturum başlatma

// Zaman kontrolü (1 dakika = 60 saniye)
if (isset($_SESSION['verification_time'])) {
    $elapsed_time = time() - $_SESSION['verification_time']; // Geçen süre
    $remaining_time = 60 - $elapsed_time; // Kalan süre

    if ($remaining_time <= 0) {
        // Süre dolmuşsa oturumdan kodu kaldır
        unset($_SESSION['verification_code']);
        unset($_SESSION['verification_time']);
        $error = "Süre doldu! Lütfen tekrar giriş yapın.";
        $remaining_time = 0; // Kalan süreyi sıfırla
    }
} else {
    $error = "Doğrulama süresi başlatılmadı. Lütfen tekrar giriş yapın.";
    $remaining_time = 0; // Eğer zaman ayarı yoksa süreyi sıfırla
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['verification_code'])) {
    $entered_code = $_POST["verification_code"];

    // Doğrulama kodunu kontrol et
    if ($entered_code == $_SESSION['verification_code']) {
        // Başarılı doğrulama, oturumdan tüm veriyi temizle ve ana sayfaya yönlendir
        unset($_SESSION['verification_code']);
        unset($_SESSION['verification_time']);
        
        // Kullanıcı bilgilerini oturumda sakla
        $_SESSION['username'] = $_SESSION['username']; // Kullanıcı adı
        $_SESSION['user_id'] = $_SESSION['user_id'];   // Kullanıcı ID'si

        // Ana sayfaya yönlendirme
        header("Location: index.php");
        exit();
    } else {
        $error = "Geçersiz doğrulama kodu.";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doğrulama Kodu</title>
    <link rel="stylesheet" href="style.css">
    <script>
        let remainingTime = <?php echo isset($remaining_time) ? $remaining_time : 0; ?>;

        function startCountdown() {
            const timerDisplay = document.getElementById("timer");

            const countdown = setInterval(() => {
                if (remainingTime <= 0) {
                    clearInterval(countdown);
                    timerDisplay.textContent = "Süre doldu! Lütfen tekrar giriş yapın.";
                } else {
                    timerDisplay.textContent = "Kalan süre: " + remainingTime + " saniye";
                    remainingTime--;
                }
            }, 1000);
        }

        window.onload = startCountdown;
    </script>
</head>
<body>
    <form action="verify.php" method="POST">
        <h2>Doğrulama Kodu</h2>
        <label for="verification_code">Doğrulama Kodu:</label>
        <input type="text" id="verification_code" name="verification_code" required>
        <button type="submit">Doğrula</button>

        <p id="timer" style="color: blue; font-size: 16px; font-weight: bold;"></p> <!-- Süre burada görünecek -->

        <?php if (isset($error)) { echo "<p style='color: red;'>$error</p>"; } ?>
    </form>
</body>
</html>
