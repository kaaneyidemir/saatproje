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

        @keyframes marquee {
            from {
                transform: translateX(100%);
            }
            to {
                transform: translateX(-100%);
            }
        }

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
        .faq-toggle {
        background-color: #f1f1f1;
        color: #333;
        padding: 15px;
        width: 100%;
        text-align: left;
        font-size: 18px;
        cursor: pointer;
        border: 1px solid #ccc;
        margin-bottom: 5px;
        border-radius: 5px;
    }

    .faq-toggle:hover {
        background-color: #ddd;
    }

    .faq-content {
        display: none;
        padding: 10px 15px;
        background-color: #f9f9f9;
        margin-top: 5px;
        border: 1px solid #ccc;
        border-radius: 5px;
    }

    </style>
</head>
<body>
<header>

<h1><a href="index.php"><img src="./images/logo2.png" alt="" style="width:50px"></a></h1>
    <nav style="display: flex; justify-content: space-between; width: 100%; align-items: center;">
        <div style="display: flex; gap:70px; justify-content: center; flex-grow: 1; ">
            <a href="index.php">Ana Sayfa</a>
            <a href="products.php">Ürünler</a>
            <a href="contact.php">İletişim</a>
        </div>
        <div style="display: flex; gap: 30px;">
            <?php if ($username): ?>
              

                <?php if ($is_admin): ?>
                    <a href="addproducts.php">Ürün Ekle</a>
                    <a href="admin_approve.php">Sipariş Onay</a>
                    <a href="discount_code_page.php">İndirim Kodu Sayfası</a>
                <?php endif; ?>
                
                <a href="orders.php">Siparişlerim</a>
                <a href="profile.php">Profil</a> <!-- Profil butonu eklendi -->
                <a href="logout.php"><i class="fa-solid fa-door-open"></i> Çıkış</a>
            <?php else: ?>
                <a href="login.php"><i class="fa-solid fa-door-open"></i> Giriş Yap</a>
            <?php endif; ?>
        </div>
    </nav>

    
</header>



    <section class="chatbot-container" id="chatbotContainer">
        <div id="chatbox" class="chatbox">
            <div class="chatbox-messages" id="messages"></div>
            <input type="text" id="userInput" placeholder="Mesajınızı yazın..." />
            <button onclick="sendMessage()">Gönder</button>
        </div>
    </section>

    <button id="chatbotToggle" class="chatbot-toggle" onclick="toggleChatbot()">💬</button>

    <?php if ($username): ?>
        <div class="welcome-notification" id="welcomeNotification">
            Hoşgeldiniz, <?php echo $username; ?>!
        </div>
        <script>
    // Kullanıcı mesaj gönderme işlemi
    function sendMessage() {
        const userInput = document.getElementById('userInput').value.trim();
        if (userInput === '') return;

        appendMessage('user', userInput);

        // Kullanıcının yazdığı mesaja göre cevaplar
        const botReplies = {
            "merhaba": "Merhaba! Size nasıl yardımcı olabilirim?",
            "ürünler": "Ürünlerimizi görmek için <a href='products.php'>buraya tıklayın</a>.",
            "iletişim": "Bizimle iletişime geçmek için <a href='contact.php'>İletişim Sayfası</a>'nı ziyaret edebilirsiniz.",
            "indirim": "İndirimlerimiz için <a href='suprize.php'>buraya göz atın</a>.",
            "hakkımızda": "Hakkımızda bilgi almak için <a href='about.php'>buraya tıklayın</a>.",
            "yardım": "Sıkça sorulan sorulara göz atmak için <a href='sss.php'>burayı ziyaret edin</a>.",
            "giriş": "Hesabınıza giriş yapmak için <a href='login.php'>Giriş Yap</a> sayfasına gidin.",
            "kayıt": "Yeni hesap oluşturmak için <a href='register.php'>buradan kayıt olun</a>.",
            "sepete_ekle": "Ürünü sepete eklemek için <a href='cart.php'>buraya tıklayın</a>.",
            "sipariş_takip": "Siparişinizi takip etmek için <a href='order_tracking.php'>takip sayfası</a>'nı ziyaret edin.",
           "blog": "Yazılarımızı okumak için <a href='blog.php'>Blog Sayfası</a>'na göz atabilirsiniz.",
             "kampanyalar": "Güncel kampanyalarımız için <a href='campaigns.php'>kampanya sayfasını</a> ziyaret edin.",
             "hesabım": "Hesap bilgilerinizi görmek için <a href='account.php'>Hesabım</a> sayfasına gidin.",
            "çıkış": "Hesabınızdan çıkış yapmak için <a href='logout.php'>buraya tıklayın</a>.",
                    "şifre_sıfırla": "Şifrenizi sıfırlamak için <a href='reset_password.php'>şifre sıfırlama sayfası</a>'na gidin.",
              "destek": "Canlı destek almak için <a href='support.php'>Destek Merkezi</a>'ne ulaşabilirsiniz.",
          "fırsatlar": "Özel fırsatlar için <a href='offers.php'>Fırsatlar Sayfası</a>'na göz atın.",
                 "kategori": "Tüm kategorilerimize bakmak için <a href='categories.php'>Kategori Sayfası</a>'na gidin.",
             "bize_yazın": "Görüş ve önerilerinizi paylaşmak için <a href='feedback.php'>Bize Yazın</a> sayfasını kullanabilirsiniz."
        };

        let botReply = botReplies[userInput.toLowerCase()] || "Üzgünüm, bunu anlayamadım. Daha fazla detay verebilir misiniz?";
        appendMessage('bot', botReply);

        // Temizle
        document.getElementById('userInput').value = '';
    }

    // Mesajları ekrana yazdırma
    function appendMessage(sender, message) {
        const messageElement = document.createElement('div');
        messageElement.classList.add('message', sender, 'fade-in');
        messageElement.innerHTML = `<div class="message-bubble">${message}</div>`;
        const chatMessages = document.getElementById('messages');
        chatMessages.appendChild(messageElement);

        // En alta kaydır
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    // Chatbot görünümünü aç/kapa
    function toggleChatbot() {
        const chatbotContainer = document.getElementById('chatbotContainer');
        chatbotContainer.style.display = chatbotContainer.style.display === 'block' ? 'none' : 'block';
    }

    // Enter tuşuyla mesaj gönderme
    document.getElementById('userInput').addEventListener('keyup', function(event) {
        if (event.key === 'Enter') {
            sendMessage();
        }
    });
    function toggleFAQ(index) {
        const faqContent = document.getElementById('faq-content-' + index);
        faqContent.style.display = faqContent.style.display === 'block' ? 'none' : 'block';
    }
</script>

<style>
    /* Chatbot kutusunun modern görünümü */
    .chatbox {
        background: linear-gradient(145deg, #ffffff, #dcdcdc);
        box-shadow: 5px 5px 15px #bababa, -5px -5px 15px #ffffff;
        font-family: Arial, sans-serif;
    }

    .chatbox-messages {
        padding: 10px;
        font-size: 14px;
        font-family: 'Roboto', sans-serif;
    }

    .chatbox input {
        margin-top: 10px;
        background-color: #e8e8e8;
        box-shadow: inset 2px 2px 5px #bababa, inset -2px -2px 5px #ffffff;
    }

    .chatbox button {
        background: linear-gradient(145deg, #56c057, #48a94c);
        box-shadow: 2px 2px 5px #3d7e40, -2px -2px 5px #6ad76a;
    }

    .chatbox button:hover {
        background: #48a94c;
    }

    .message-bubble {
        border-radius: 15px;
        padding: 10px 15px;
    }

    .message.bot .message-bubble {
        background-color: #f0f0f0;
        color: #000;
    }

    .message.user .message-bubble {
        background-color: #4CAF50;
        color: white;
    }

    .chatbot-toggle {
        background: linear-gradient(145deg, #48a94c, #56c057);
        box-shadow: 3px 3px 10px #3d7e40, -3px -3px 10px #6ad76a;
    }
</style>

    <?php endif; ?>

    <main>
        <section class="large-slider">
            <div class="slides"><a href="products.php"><img src="images/watch2.jpg" alt="Büyük Saat 1"></a></div>
            <div class="slides"><a href="products.php"><img src="images/saat5.jpg" alt="Büyük Saat 2"></a></div>
            <div class="slides"><a href="products.php"><img src="images/saat9.jpg" alt="Büyük Saat 3"></a></div>
        </section>

        <style>
            .large-slider {
                display: flex;
                justify-content: center;
                gap: 20px;
            }

            .slides {
                flex: 1;
                max-width: 30%;
                text-align: center;
            }

            .slides img {
                width: 100%;
                height: auto;
                border-radius: 10px;
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
                <div class="product" onclick="window.location.href='products.php';">
                    <img src="images/saat9.jpg" alt="Saat 3">
                    <h3>Farklı Tasarım</h3>
                </div>
                <div class="product" onclick="window.location.href='products.php';">
                    <img src="images/saat9.jpg" alt="Saat 3">
                    <h3>Farklı Tasarım</h3>
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
    <div class="faq-container">
    <button class="faq-toggle" onclick="toggleFAQ(0)">Saatlerinizin garantisi var mı?</button>
    <div id="faq-content-0" class="faq-content">
        <p>Her saat 2 yıl garanti kapsamındadır.</p>
    </div>
    
    <button class="faq-toggle" onclick="toggleFAQ(1)">Ürünlerinizin teslimat süresi ne kadar?</button>
    <div id="faq-content-1" class="faq-content">
        <p>Ürünler 3-5 iş günü içinde teslim edilir.</p>
    </div>
    
    <button class="faq-toggle" onclick="toggleFAQ(2)"> Hangi ödeme yöntemlerini kabul ediyorsunuz?</button>
    <div id="faq-content-2" class="faq-content">
        <p>Kredi kartı, banka transferi ve PayPal ödeme yöntemlerini kabul ediyoruz.</p>
    </div>
</div>
<button onclick="window.location.href='ortaklarimiz.php';">Ortaklarımız</button>
    
    

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
    
</body>
</html>