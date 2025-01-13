<?php
session_start(); // Oturum başlatma

// Kullanıcı admin değilse ana sayfaya yönlendir
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header('Location: index.php');
    exit;
}

// Veritabanı bağlantısı
$host = "localhost";
$kullanici = "root";
$sifre = "";
$veritabani = "test";
$baglanti = new mysqli($host, $kullanici, $sifre, $veritabani);

if ($baglanti->connect_error) {
    die("Veritabanı bağlantı hatası: " . $baglanti->connect_error);
}

// İndirim kodu oluşturma
$discountCode = '';
$discountPercentage = 0;
$usageLimit = 0;
$customWord = '';
$successMessage = '';
$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customWord = strtoupper(trim($_POST['custom_word'])); // Özel kelime
    $discountPercentage = intval($_POST['discount_percentage']); // İndirim yüzdesi
    $usageLimit = intval($_POST['usage_limit']); // Kullanım limiti

    // Özel kelime boşsa rastgele bir kod oluştur
    if (empty($customWord)) {
        $codeLength = 10;
        $discountCode = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, $codeLength);
    } else {
        $discountCode = $customWord;
    }

    // Benzersizlik kontrolü
    $checkSql = "SELECT COUNT(*) FROM discount_codes WHERE code = ?";
    $checkStmt = $baglanti->prepare($checkSql);
    $checkStmt->bind_param("s", $discountCode);
    $checkStmt->execute();
    $checkStmt->bind_result($count);
    $checkStmt->fetch();
    $checkStmt->close();

    if ($count > 0) {
        $errorMessage = "Bu kod zaten mevcut, lütfen başka bir kod deneyin.";
    } else {
        // Veritabanına kaydetme işlemi
        $sql = "INSERT INTO discount_codes (code, discount_percentage, usage_limit, used) VALUES (?, ?, ?, 0)";
        $stmt = $baglanti->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("sii", $discountCode, $discountPercentage, $usageLimit);

            if ($stmt->execute()) {
                $successMessage = "Yeni indirim kodu oluşturuldu: $discountCode (%$discountPercentage) - Kullanım Limiti: $usageLimit";
            } else {
                $errorMessage = "Kod kaydedilemedi: " . $stmt->error;
            }

            $stmt->close();
        } else {
            $errorMessage = "Sorgu hazırlanamadı: " . $baglanti->error;
        }
    }
}

$baglanti->close();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>İndirim Kodu Oluştur</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .discount-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 400px;
            text-align: center;
        }

        .discount-container h1 {
            font-size: 24px;
            margin-bottom: 20px;
        }

        .discount-container form {
            margin-bottom: 20px;
        }

        .discount-container button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
        }

        .discount-container button:hover {
            background-color: #45a049;
        }

        .success-message {
            background-color: #dff0d8;
            color: #3c763d;
            padding: 10px;
            margin-top: 20px;
            border: 1px solid #d6e9c6;
            border-radius: 5px;
        }

        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            margin-top: 20px;
            border: 1px solid #f5c6cb;
            border-radius: 5px;
        }

        .discount-container input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }
    </style>
</head>
<body>
    <div class="discount-container">
        <h1>İndirim Kodu Oluştur</h1>

        <form method="POST">
            <input type="number" name="discount_percentage" placeholder="İndirim Yüzdesi (1-100)" required min="1" max="100">
            <input type="number" name="usage_limit" placeholder="Kullanım Limiti" required min="1">
            <input type="text" name="custom_word" placeholder="Özel Kelime (Opsiyonel)">
            <button type="submit">Yeni İndirim Kodu Oluştur</button>
        </form>

        <?php if ($successMessage): ?>
            <div class="success-message">
                <?php echo htmlspecialchars($successMessage); ?>
            </div>
        <?php endif; ?>

        <?php if ($errorMessage): ?>
            <div class="error-message">
                <?php echo htmlspecialchars($errorMessage); ?>
            </div>
        <?php endif; ?>

        <a href="index.php" style="display: block; margin-top: 20px; color: #4CAF50;">Anasayfaya Dön</a>
    </div>
</body>
</html>
