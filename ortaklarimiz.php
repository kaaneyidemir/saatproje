<?php
// Dinamik olarak eklemek istediğiniz yazıları burada belirleyebilirsiniz.
$messages = [
    "İlk mesajınız buraya gelecek.",
    "İkinci mesajınız burada görünecek.",
    "Üçüncü mesajınız burada kayacak.",
];
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kayan Yazı Sayfası</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #004d00; /* Koyu yeşil arka plan */
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            overflow: hidden;
        }

        .marquee-container {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
        }

        .marquee {
            display: block;
            white-space: nowrap;
            animation: marquee-animation 5s linear infinite; /* Tekrar eden animasyon */
            font-size: 24px;
            color: white;
            font-weight: bold;
            text-align: center;
        }

        @keyframes marquee-animation {
            0% {
                transform: translateY(-100%); /* Başlangıçta ekranın dışında */
            }
            100% {
                transform: translateY(550%); /* Ekranın alt kısmına kayacak */
            }
        }

        .marquee img {
            display: block;
            margin: 10px auto; /* Resimleri ortalar */
            width: 50px; /* Resim boyutunu ayarlayabilirsiniz */
            height: 50px;
        }
    </style>
</head>
<body>
    <div class="marquee-container">
        <div class="marquee">
            <?php
            // Kayan yazı mesajlarını ve resimleri PHP ile dinamik olarak ekleyin
            foreach ($messages as $message) {
                echo "<p>" . $message . "</p>"; // Yazıları ekler
                // Resim eklemek isterseniz:
                // echo "<img src='your-image-url.jpg' alt='Resim'>";
            }
            ?>
        </div>
    </div>
</body>
</html>
