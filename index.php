<?php
session_start(); // Oturum başlatma

// Kullanıcı giriş yaptıysa, oturum bilgilerini al
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username']; // Oturumda kayıtlı kullanıcı adını al
    $is_admin = ($_SESSION['username'] == 'admin' && isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1); // Admin kontrolü
} else {
    $username = null;
    $is_admin = false;
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SAAT2M</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <script src="scripts.js"></script>

    <style>
        /* Slider için temel stiller */
        .slider {
            display: flex;
            justify-content: space-between;
            max-width: 100%;
            margin: auto;
        }
        .slider .slides img {
            width: 33.33%;
            display: block;
        }

        /* Yeni büyük slider */
        .large-slider {
            display: flex;
            justify-content: space-between;
            max-width: 100%;
            margin-top: 20px;
        }
        .large-slider .slides img {
            width: 100%;
            display: block;
        }

        html {
            scroll-behavior: smooth;
        }

        /* Kaydıran yazı bandı */
        .marquee-container {
            width: 100%;
            overflow: hidden;
            background-color: #7777;
            color: white;
            padding: 10px 0;
        }

        .marquee {
            display: inline-block;
            white-space: nowrap;
            animation: marquee 15s linear infinite;
        }

        /* Kaydırma animasyonu */
        @keyframes marquee {
            from {
                transform: translateX(100%);
            }
            to {
                transform: translateX(-100%);
            }
        }

        /* Hoşgeldiniz bildirim stilleri */
        .welcome-notification {
            position: fixed;
            top: 10px;
            right: 0;
            background-color: #4CAF50;
            color: white;
            padding: 10px;
            border-radius: 5px;
            display: none;
            z-index: 1000;
        }

        .welcome-notification.show {
            display: block;
            animation: slideIn 0.5s ease-out;
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
            }
            to {
                transform: translateX(0);
            }
        }

        /* Footer düzenlemeleri */
        footer {
            padding: 20px;
            background-color: #f1f1f1;
            text-align: center;
        }

        .contact-location {
            display: flex; /* Flexbox ile öğeleri yanyana hizalar */
            justify-content: space-between; /* Aralarındaki boşluğu eşitler */
            align-items: center; /* Dikeyde ortalar */
            max-width: 1200px; /* Maksimum genişlik */
            margin: 0 auto; /* Ortalar */
        }

        .location-image {
            flex: 1; /* Fotoğraf alanına alan verir */
            max-width: 45%; /* Fotoğrafın genişliğini sınırlar */
        }

        .location-image img {
            width: 100%; /* Fotoğrafın genişliği konteynerle uyumlu olacak şekilde ayarlanır */
            height: auto; /* Orantılı bir şekilde büyütülür */
        }

        .contact-form {
            flex: 1; /* Form alanına alan verir */
            max-width: 45%; /* Formun genişliğini sınırlar */
            padding-left: 20px; /* Form ile fotoğraf arasına boşluk ekler */
        }

        .contact-form form {
            display: flex;
            flex-direction: column;
        }

        .contact-form input,
        .contact-form textarea,
        .contact-form button {
            margin-bottom: 10px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .contact-form button {
            background-color: #4CAF50;
            color: white;
            cursor: pointer;
        }

        .contact-form button:hover {
            background-color: #45a049;
        }

        /* Yeni Flex düzenlemesi: İki öğe yan yana olacak */
        .flex-container {
            display: flex;
            justify-content: space-between;
            gap: 20px; /* Öğeler arasındaki boşluk */
        }

        .flex-container .flex-item {
            flex: 1;
            max-width: 45%;
        }

        /* Kartlar için tıklanabilir alan */
        .product-grid .product {
            cursor: pointer;
        }

        /* Kar tanelerinin temel stili */
        .snowflake {
            position: absolute;
            top: -10px;
            pointer-events: none; /* Kar tanelerinin üzerine tıklanmasını engeller */
            color: #fff;
            font-size: 20px;
            opacity: 0.8;
            user-select: none;
            z-index: 9999;
            animation: fall linear infinite;
        }

        /* Kar tanelerinin düşme animasyonu */
        @keyframes fall {
            to {
                transform: translateY(100vh); /* Sayfanın yüksekliği kadar aşağıya kayma */
            }
        }
    </style>
</head>
<body>
    <header>
        <h1><a href="index.php"><img src="./images/logo.png" alt="" style="width:50px"></a></h1>
        <nav>
            <a href="index.php">Ana Sayfa</a>
            <a href="products.php">Ürünler</a>
            <a href="contact.php">İletişim</a>

            <?php if ($username): ?>
                <a href="logout.php"><i class="fa-regular fa-user"></i> Çıkış</a>
                
                <!-- Admin için Ürün Ekleme butonu -->
                <?php if ($is_admin): ?>
                    <a href="addproducts.php">
                        <button>Ürün Ekle</button>
                    </a>
                    <!-- Admin için Sipariş Onay butonu -->
                    <a href="admin_approve.php">
                        <button>Sipariş Onay</button>
                    </a>
                <?php endif; ?>
                
                <!-- Kullanıcı için Siparişlerim butonu -->
                <a href="orders.php">Siparişlerim</a>
            <?php else: ?>
                <a href="login.php"><i class="fa-regular fa-user"></i> Giriş Yap</a>
            <?php endif; ?>
        </nav>
    </header>
    
    <!-- Hoşgeldiniz Bildirimi -->
    <?php if ($username): ?>
        <div class="welcome-notification" id="welcomeNotification">
            Hoşgeldiniz, <?php echo $username; ?>!
        </div>
        <script>
            // Bildirimi göstermek için JavaScript
            window.onload = function() {
                const notification = document.getElementById('welcomeNotification');
                notification.classList.add('show'); // Bildirimi göster
                setTimeout(function() {
                    notification.classList.remove('show'); // 5 saniye sonra bildirimi gizle
                }, 5000); // 5 saniye sonra kaybolacak
            };
        </script>
    <?php endif; ?>

    <main>
        <!-- Yeni Büyük Slider -->
        <section class="large-slider">
            <div class="slides">
            <a href="products.php">
                <img src="images/watch2.jpg" alt="Büyük Saat 1">
            </a>
            </div>
            <div class="slides">
            <a href="products.php">
                <img src="images/saat5.jpg" alt="Büyük Saat 2">
            </a>
            </div>
            <div class="slides">
            <a href="products.php">
                <img src="images/saat9.jpg" alt="Büyük Saat 3">
            </a>
            </div>
        </section>

        <!-- Öne Çıkan Saatler -->
        <section class="featured">
            <h2>Öne Çıkan Saatler</h2>
            <div class="product-grid">
                <div class="product" onclick="window.location.href='products.php';">
                    <img src="images/saat5.jpg" alt="Saat 1">
                    <h3>Klasik Saat</h3>
                    <p></p>
                </div>
                <div class="product" onclick="window.location.href='products.php';">
                    <img src="images/saat9.jpg" alt="Saat 2">
                    <h3>Spor Saat</h3>
                    <p></p>
                </div>
                <div class="product" onclick="window.location.href='products.php';">
                    <img src="images/saat7.jpg" alt="Saat 3">
                    <h3>Spor Saat</h3>
                    <p></p>
                </div>
                <div class="product" onclick="window.location.href='products.php';">
                    <img src="images/watch11.png" alt="Saat 4">
                    <h3>Spor Saat</h3>
                    <p></p>
                </div>
            </div>
        </section>

        <!-- Kaydıran Yazı Bandı -->
        <section class="marquee-container">
            <div class="marquee">
                AKLINIZDA DEĞİL KOLUNUZDA OLSUN!
            </div>
        </section>

        <!-- Yeni Büyük Slider (Tekrar) -->
        <section class="large-slider">
            <div class="slides">
            <a href="products.php">
                <img src="images/saat9.jpg" alt="Büyük Saat 1">
            </a>
            </div>
            <div class="slides">
                <a href="products.php">
                <img src="images/saat8.jpg" alt="Büyük Saat 2">
                </a>
            </div>
            <div class="slides">
            <a href="products.php">
                <img src="images/saat5.jpg" alt="Büyük Saat 3">
            </a>
            </div>
        </section>

        <!-- Flexbox ile iki öğe yan yana -->
        <section class="flex-container">
            <div class="flex-item">
            <h2> <p>
  <center>Adresimiz</center>
</p></h2>
                <img src="images/harita3.png" alt="Lokasyon Fotoğrafı" width="100%">
            </div>
            <div class="flex-item">
            <h2> <p>
  <center>Bize Ulaşın</center>
</p></h2>
                <form action="contact_form.php" method="post">
                    <label for="name">Adınız:</label>
                    <input type="text" id="name" name="name" required>

                    <label for="email">E-posta:</label>
                    <input type="email" id="email" name="email" required>

                    <label for="message">Mesajınız:</label>
                    <textarea id="message" name="message" rows="4" required></textarea>

                    <button type="submit">Gönder</button>
                </form>
            </div>
        </section>
    </main>

    <footer>
        <p>© SAAT2M Tüm Haklar Saklidir.</p>
    </footer>

    <!-- Kar Yağdırma Efekti -->
    <script>
        // Kar tanelerini ekleme fonksiyonu
        function createSnowflakes() {
            const numberOfSnowflakes = 250; // Kar tanesi sayısı
            const snowflakeContainer = document.body;

            for (let i = 0; i < numberOfSnowflakes; i++) {
                let snowflake = document.createElement('div');
                snowflake.classList.add('snowflake');
                snowflake.innerText = '❄'; // Kar tanesi simgesi
                snowflake.style.left = Math.random() * 100 + 'vw'; // Rastgele yatay konum
                snowflake.style.animationDuration = Math.random() * 3 + 2 + 's'; // Rastgele düşme süresi
                snowflake.style.fontSize = Math.random() * 10 + 10 + 'px'; // Rastgele boyut
                snowflake.style.animationDelay = Math.random() * 5 + 's'; // Rastgele başlama gecikmesi
                snowflakeContainer.appendChild(snowflake);
            }
        }

        // Kar yağdırma başlatma
        window.onload = createSnowflakes;
    </script>
</body>
</html>
