<?php
// Dinamik olarak eklemek istediğiniz yazıları burada belirleyebilirsiniz.
$messages = [
    "Ortaklarimiz",
    ];

// Dinamik olarak resim URL'lerini ekleyebilirsiniz.
$images = [
    "images/EternalGlimmer(logo).jpg", // Resim 1 (Yerel resim)
    "images/EclipseFlux.jpg", // Resim 1 (Yerel resim)
    "images/TemporalPhantom.jpg", // Resim 1 (Yerel resim)
    
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
            background-color: rgb(0, 0, 0); /* Siyah arka plan */
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            overflow: hidden;
            position: relative; /* Efektler için gerekli */
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
                transform: translateY(100%); /* Ekranın alt kısmına kayacak */
            }
        }

        .marquee img {
            display: block;
            margin: 10px auto; /* Resimleri ortalar */
            width: 200px; /* Resim boyutunu ayarlayabilirsiniz */
            height: 150px;
        }

        /* Havai fişek efektleri */
        .firework {
            position: absolute;
            width: 10px;
            height: 10px;
            background-color: yellow;
            border-radius: 50%;
            animation: firework-animation 1.5s ease-out infinite;
            opacity: 0;
        }

        @keyframes firework-animation {
            0% {
                transform: scale(0.5) translate(0, 0);
                opacity: 1;
            }
            50% {
                opacity: 1;
            }
            100% {
                transform: scale(1.5) translate(100px, -100px);
                opacity: 0;
            }
        }

        /* Farklı renkler için */
        .firework:nth-child(odd) {
            background-color: red;
        }

        .firework:nth-child(even) {
            background-color: blue;
        }
    </style>
</head>
<body>
    <!-- Havai fişek efektleri için divler -->
    <div class="firework" style="top: 10%; left: 50%;"></div>
    <div class="firework" style="top: 10%; left: 50%;"></div>
    <div class="firework" style="top: 20%; left: 30%;"></div>
    <div class="firework" style="top: 30%; left: 70%;"></div>
    <div class="firework" style="top: 40%; left: 50%;"></div>
    <div class="firework" style="top: 50%; left: 30%;"></div>
    <div class="firework" style="top: 60%; left: 70%;"></div>
    <div class="firework" style="top: 70%; left: 50%;"></div>
    <div class="firework" style="top: 80%; left: 30%;"></div>
    <div class="firework" style="top: 90%; left: 70%;"></div>
    <div class="firework" style="top: 100%; left: 50%;"></div>
    <div class="firework" style="top: 110%; left: 30%;"></div>
    <div class="firework" style="top: 120%; left: 70%;"></div>

    <div class="marquee-container">
        <div class="marquee">
            <?php
            // Kayan yazı mesajlarını PHP ile ekleyin
            foreach ($messages as $message) {
                echo "<p>" . $message . "</p>"; // Yazıları ekler
            }

            // Resimlerinizi PHP ile ekleyin
            foreach ($images as $image) {
                echo "<img src='" . $image . "' alt='Resim'>"; // Yerel resimleri ekler
            }
            ?>
        </div>
    </div>
</body>
</html>
