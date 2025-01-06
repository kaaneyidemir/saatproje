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
        /* Sohbet Botu Stili */
        .chatbot-container {
            position: fixed;
            bottom: 10px;
            right: 10px;
            width: 300px;
            z-index: 1000;
            display: none; /* Başlangıçta gizle */
        }

        .chatbox {
            background-color: #f1f1f1;
            border-radius: 10px;
            padding: 10px;
            display: flex;
            flex-direction: column;
            height: 400px;
            width: 100%;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .chatbox-messages {
            overflow-y: scroll;
            flex: 1;
            margin-bottom: 10px;
        }

        .chatbox input {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            width: 100%;
        }

        .chatbox button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
        }

        .chatbox button:hover {
            background-color: #45a049;
        }

        /* Sohbet Mesajı Stili */
        .message {
            margin-bottom: 10px;
            display: flex;
            justify-content: flex-start;
            align-items: flex-start;
        }

        .message.user {
            justify-content: flex-end;
        }

        .message .message-bubble {
            padding: 10px;
            border-radius: 20px;
            max-width: 70%;
        }

        .message.bot .message-bubble {
            background-color: #ddd;
            color: #000;
        }

        .message.user .message-bubble {
            background-color: #25d366;
            color: white;
        }

        /* Fade-in animasyonu */
        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        .fade-in {
            animation: fadeIn 0.3s ease-out;
        }

        /* Sohbet butonu stili */
        .chatbot-toggle {
            position: fixed;
            bottom: 10px;
            right: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 50%;
            padding: 15px;
            font-size: 20px;
            cursor: pointer;
            z-index: 1001; /* Sohbet butonunun ön planda olmasını sağla */
        }

        .chatbot-toggle:hover {
            background-color: #45a049;
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
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
        }

        .location-image {
            flex: 1;
            max-width: 45%;
        }

        .location-image img {
            width: 100%;
            height: auto;
        }

        .contact-form {
            flex: 1;
            max-width: 45%;
            padding-left: 20px;
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
            gap: 20px;
        }

        .flex-container .flex-item {
            flex: 1;
            max-width: 45%;
        }

        .product-grid .product {
            cursor: pointer;
        }

        .snowflake {
            position: absolute;
            top: -10px;
            pointer-events: none;
            color: #fff;
            font-size: 20px;
            opacity: 0.8;
            user-select: none;
            z-index: 9999;
            animation: fall linear infinite;
        }

        @keyframes fall {
            to {
                transform: translateY(100vh);
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

                <?php if ($is_admin): ?>
                    <a href="addproducts.php">
                        <button>Ürün Ekle</button>
                    </a>
                    <a href="admin_approve.php">
                        <button>Sipariş Onay</button>
                    </a>
                <?php endif; ?>
                
                <a href="orders.php">Siparişlerim</a>
            <?php else: ?>
                <a href="login.php"><i class="fa-regular fa-user"></i> Giriş Yap</a>
            <?php endif; ?>
        </nav>
    </header>

    <section class="chatbot-container" id="chatbotContainer">
        <div id="chatbox" class="chatbox">
            <div class="chatbox-messages" id="messages"></div>
            <input type="text" id="userInput" placeholder="Mesajınızı yazın..." />
            <button onclick="sendMessage()">Gönder</button>
        </div>
    </section>

    <!-- Sohbet Botu Toggle Butonu -->
    <button id="chatbotToggle" class="chatbot-toggle" onclick="toggleChatbot()">💬</button>

    <?php if ($username): ?>
        <div class="welcome-notification" id="welcomeNotification">
            Hoşgeldiniz, <?php echo $username; ?>!
        </div>
        <script>
            window.onload = function() {
                const notification = document.getElementById('welcomeNotification');
                notification.classList.add('show');
                setTimeout(function() {
                    notification.classList.remove('show');
                }, 5000);
            };
        </script>
    <?php endif; ?>

    <main>
    <section class="large-slider">
    <div class="slides"><a href="products.php"><img src="images/watch2.jpg" alt="Büyük Saat 1"></a></div>
    <div class="slides"><a href="products.php"><img src="images/saat5.jpg" alt="Büyük Saat 2"></a></div>
    <div class="slides"><a href="products.php"><img src="images/saat9.jpg" alt="Büyük Saat 3"></a></div>
</section>

<style>
    .large-slider {
        display: flex; /* Flexbox düzenini etkinleştir */
        justify-content: center; /* Öğeleri yatayda ortala */
        gap: 20px; /* Öğeler arasına boşluk ekle */
    }

    .slides {
        flex: 1; /* Öğelerin eşit genişlikte olmasını sağla */
        max-width: 30%; /* Her bir slide için maksimum genişlik belirle */
        text-align: center; /* Görselleri ortala */
    }

    .slides img {
        width: 100%; /* Görsellerin tamamen slide içinde görünmesini sağla */
        height: auto; /* Oranları koru */
        border-radius: 10px; /* Köşeleri yuvarla */
    }
</style>


        <section class="featured">
            <h2>Öne Çıkan Saatler</h2>
            <div class="product-grid">
                <div class="product" onclick="window.location.href='products.php';">
                    <img src="images/saat5.jpg" alt="Saat 1">
                    <h3>Klasik Saat</h3>
                </div>
                <div class="product" onclick="window.location.href='products.php';">
                    <img src="images/saat2.jpg" alt="Saat 2">
                    <h3>Modern Saat</h3>
                </div>
                <div class="product" onclick="window.location.href='products.php';">
                    <img src="images/saat9.jpg" alt="Saat 3">
                    <h3>Farklı Tasarım</h3>
                </div>
                <div class="product" onclick="window.location.href='products.php';">
                    <img src="images/saat9.jpg" alt="Saat 3">
                    <h3>Farklı Tasarım</h3>
                </div>
            </div>
        </section>
    </main>

    <footer>
        <div class="contact-location">
            <div class="location-image">
                <img src="images/harita3.png" alt="Konum">
            </div>
            <div class="contact-form">
                <h3>Bize Ulaşın</h3>
                <form action="contact_form.php" method="POST">
                    <input type="text" name="name" placeholder="Adınız" required>
                    <input type="email" name="email" placeholder="E-posta" required>
                    <textarea name="message" placeholder="Mesajınız" required></textarea>
                    <button type="submit">Gönder</button>
                </form>
            </div>
        </div>
    </footer>

    <script>
    // Kullanıcı ve bot mesajları yazdırılacak fonksiyon
    function sendMessage() {
        const userInput = document.getElementById('userInput').value; // Kullanıcıdan gelen input
        if (userInput.trim() === '') return; // Boş mesajı geç

        // Kullanıcı mesajını ekle
        const userMessage = document.createElement('div');
        userMessage.classList.add('message', 'user', 'fade-in');
        userMessage.innerHTML = `<div class="message-bubble">${userInput}</div>`;
        document.getElementById('messages').appendChild(userMessage);

        // Kullanıcı mesajını temizle
        document.getElementById('userInput').value = '';

        // Botun ilk mesajını ekle
        setTimeout(function () {
            const botMessage1 = document.createElement('div');
            botMessage1.classList.add('message', 'bot', 'fade-in');
            botMessage1.innerHTML = `<div class="message-bubble">Merhaba! Size nasıl yardımcı olabilirim?</div>`;
            document.getElementById('messages').appendChild(botMessage1);

            // Yeni mesajlar eklenince kaydırma işlemi
            document.getElementById('messages').scrollTop = document.getElementById('messages').scrollHeight;

            // İkinci mesaj (butonlar) eklenir
            setTimeout(function () {
                const botMessage2 = document.createElement('div');
                botMessage2.classList.add('message', 'bot', 'fade-in');
                botMessage2.innerHTML = `
                    <div class="bot-options">
                        <button onclick="window.location.href='index.php';">Ana Sayfa</button>
                        <button onclick="window.location.href='products.php';">Ürünler</button>
                        <button onclick="window.location.href='login.php';">Giriş Yap</button>
                    </div>
                `;
                document.getElementById('messages').appendChild(botMessage2);

                // Yeni mesajlar eklenince kaydırma işlemi
                document.getElementById('messages').scrollTop = document.getElementById('messages').scrollHeight;
            }, 1000);
        }, 1000);
    }

    // Sohbet penceresini açıp kapatacak fonksiyon
    function toggleChatbot() {
        const chatbot = document.getElementById('chatbotContainer');
        chatbot.style.display = (chatbot.style.display === 'none' || chatbot.style.display === '') ? 'block' : 'none';
    }

    // Enter tuşuyla mesaj gönderme
    document.getElementById('userInput').addEventListener('keypress', function (event) {
        if (event.key === 'Enter') {
            sendMessage();
        }
    });
</script>

<style>
    .bot-options {
        margin-top: 10px;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .bot-options button {
        background-color: #4CAF50;
        color: white;
        border: none;
        padding: 10px 15px;
        border-radius: 5px;
        cursor: pointer;
        font-size: 14px;
        text-align: center;
    }

    .bot-options button:hover {
        background-color: #45a049;
    }
</style>


</body>
</html>
