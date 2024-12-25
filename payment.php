<?php
session_start();

// Eğer kullanıcı oturum açmamışsa, login sayfasına yönlendir
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Sepet boş mu kontrolü
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    echo "<h1>Sepetiniz boş!</h1>";
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

// Sepet toplam tutarını hesaplama
$toplamTutar = 0;
$urunler = [];

$id_listesi = implode(',', array_keys($_SESSION['cart']));
$sql = "SELECT * FROM urunler WHERE id IN ($id_listesi)";
$sonuc = $baglanti->query($sql);

while ($urun = $sonuc->fetch_assoc()) {
    $urun_id = $urun['id'];
    $adet = $_SESSION['cart'][$urun_id];
    $urun_toplam = $urun['fiyat'] * $adet;
    $toplamTutar += $urun_toplam;

    $urunler[] = [
        'urun_adi' => $urun['urun_adi'],
        'adet' => $adet,
        'fiyat' => $urun['fiyat'],
        'urun_toplam' => $urun_toplam
    ];
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ödeme Sayfası</title>
    <link rel="stylesheet" href="payment.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .payment-container {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }
        h1, h2 {
            text-align: center;
        }
        .field {
            margin-bottom: 15px;
        }
        .field-row {
            display: flex;
            gap: 10px;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }
        .submit-btn {
            width: 100%;
            background-color: #007BFF;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        .submit-btn:hover {
            background-color: #0056b3;
        }
        .cart-summary ul {
            list-style: none;
            padding: 0;
        }
        .cart-summary ul li {
            margin-bottom: 10px;
        }
        .total {
            text-align: right;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="payment-container">
        <h1>Ödeme Bilgileri</h1>

        <!-- Sepet Özeti -->
        <div class="cart-summary">
            <h2>Sepet Özeti</h2>
            <ul>
                <?php foreach ($urunler as $urun): ?>
                    <li>
                        <?= htmlspecialchars($urun['urun_adi']) ?> - 
                        <?= $urun['adet'] ?> Adet x <?= $urun['fiyat'] ?>₺ = 
                        <strong><?= $urun['urun_toplam'] ?>₺</strong>
                    </li>
                <?php endforeach; ?>
            </ul>
            <p class="total">Toplam: <?= number_format($toplamTutar, 2) ?>₺</p>
        </div>

        <!-- Ödeme Formu -->
        <form method="POST" action="process_payment.php" onsubmit="return validateForm()">
            <div class="field">
                <label for="name">Kart Sahibinin Adı</label>
                <input type="text" id="name" name="name" placeholder="Ad Soyad" required>
            </div>
            <div class="field">
                <label for="cardnumber">Kart Numarası</label>
                <input type="text" id="cardnumber" name="cardnumber" maxlength="19" placeholder="**** **** **** ****" required oninput="formatCardNumber(this)">
            </div>
            <div class="field-row">
                <div class="field">
                    <label for="expiration">Son Kullanma Tarihi</label>
                    <input type="text" id="expiration" name="expiration" maxlength="5" placeholder="MM/YY" required oninput="formatExpiration(this)">
                </div>
                <div class="field">
                    <label for="cvv">CVV</label>
                    <input type="text" id="cvv" name="cvv" maxlength="3" placeholder="CVV" required>
                </div>
            </div>
            <button type="submit" class="submit-btn">Ödemeyi Onayla</button>
        </form>
    </div>

    <script>
        // Kart numarasını 4-4-4-4 formatına dönüştürme
        function formatCardNumber(input) {
            let value = input.value.replace(/\D/g, '').substring(0, 16); // sadece rakamları al
            let formatted = '';
            for (let i = 0; i < value.length; i++) {
                if (i % 4 === 0 && i !== 0) {
                    formatted += ' ';
                }
                formatted += value[i];
            }
            input.value = formatted;
        }

        // Son kullanma tarihini MM/YY formatında düzenleme
        function formatExpiration(input) {
            let value = input.value.replace(/\D/g, '').substring(0, 4); // sadece rakamları al
            let formatted = '';
            
            if (value.length >= 3) {
                formatted += value.substring(0, 2) + '-' + value.substring(2, 4);
            } else {
                formatted += value.substring(0, 2);
                if (value.length >= 2) {
                    formatted += '-';
                }
            }

            // MM kısmının 12'yi aşmaması için kontrol
            if (formatted.length === 2 && parseInt(formatted.substring(0, 2)) > 12) {
                formatted = '12' + formatted.substring(2);
            }

            input.value = formatted;
        }

        // Formu validasyon kontrolü
        function validateForm() {
            const cardNumber = document.getElementById('cardnumber').value;
            const expiration = document.getElementById('expiration').value;
            const cvv = document.getElementById('cvv').value;

            const cardRegex = /^\d{4} \d{4} \d{4} \d{4}$/; // 4-4-4-4 format
            const expRegex = /^(0[1-9]|1[0-2])-(\d{2})$/; // MM-YY format
            const cvvRegex = /^\d{3}$/;

            if (!cardRegex.test(cardNumber)) {
                alert("Kart numarası 4-4-4-4 formatında olmalıdır.");
                return false;
            }
            if (!expRegex.test(expiration)) {
                alert("Son kullanma tarihi 'MM-YY' formatında olmalıdır.");
                return false;
            }
            if (!cvvRegex.test(cvv)) {
                alert("CVV 3 haneli olmalıdır.");
                return false;
            }
            return true;
        }
    </script>
</body>
</html>
