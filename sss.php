<?php
// Sıkça Sorulan Sorular verilerini burada tanımlıyoruz.
$faq = [
    [
        "question" => "Sitenizde nasıl alışveriş yapabilirim?",
        "answer" => "Sitemizden alışveriş yapmak için ürünleri seçip sepete ekleyin, ardından ödeme işlemini tamamlayın."
    ],
    [
        "question" => "Siparişimi nasıl takip edebilirim?",
        "answer" => "Siparişinizi, kullanıcı panelinizden veya sipariş onay mailindeki bağlantıyı takip ederek izleyebilirsiniz."
    ],
    [
        "question" => "Kargo ücreti nedir?",
        "answer" => "Kargo ücreti, siparişinizin tutarına ve teslimat adresine göre değişiklik göstermektedir."
    ],
    [
        "question" => "Ürün iade koşullarınız nelerdir?",
        "answer" => "Ürünleri, teslim alındığı tarihten itibaren 14 gün içinde iade edebilirsiniz. İade koşulları hakkında detaylı bilgiye site üzerinden ulaşabilirsiniz."
    ],
    [
        "question" => "Hangi ödeme yöntemlerini kabul ediyorsunuz?",
        "answer" => "Kredi kartı, banka kartı, havale ve kapıda ödeme yöntemlerini kabul ediyoruz."
    ],
    [
        "question" => "Hangi ülkelerde teslimat yapıyorsunuz?",
        "answer" => "Türkiye'de tüm illere teslimat yapıyoruz. Uluslararası gönderi seçenekleri için lütfen bizimle iletişime geçin."
    ]
];

?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sıkça Sorulan Sorular</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }

        header {
            background-color: #4CAF50;
            color: white;
            text-align: center;
            padding: 20px 0;
        }

        .faq-container {
            width: 80%;
            margin: 20px auto;
            padding: 10px;
        }

        .faq-item {
            background-color: white;
            border: 1px solid #ddd;
            margin-bottom: 10px;
            padding: 15px;
            border-radius: 5px;
        }

        .question {
            font-size: 18px;
            color: #333;
            margin: 0;
        }

        .answer {
            font-size: 16px;
            color: #555;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <header>
        <h1>Sıkça Sorulan Sorular</h1>
    </header>

    <div class="faq-container">
        <?php foreach ($faq as $item): ?>
            <div class="faq-item">
                <h2 class="question"><?= htmlspecialchars($item['question']); ?></h2>
                <p class="answer"><?= nl2br(htmlspecialchars($item['answer'])); ?></p>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>
