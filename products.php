<?php
session_start(); // Oturum başlatılıyor

// SEPETİ HER SAYFA AÇILIŞINDA SIFIRLA (İsteğe bağlı)
if (!isset($_SESSION['cart_initialized'])) {
    $_SESSION['cart'] = [];
    $_SESSION['cart_initialized'] = true;
}

// Sepet oturumunu kontrol et ve başlat
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = []; // Boş bir sepet başlat
}

// Veritabanı bağlantısı
$host = "localhost";
$kullanici = "root"; // phpMyAdmin kullanıcı adınız
$sifre = ""; // phpMyAdmin şifreniz (boşsa boş bırakın)
$veritabani = "test";

$baglanti = new mysqli($host, $kullanici, $sifre, $veritabani);
if ($baglanti->connect_error) {
    die("Bağlantı hatası: " . $baglanti->connect_error);
}

// Ürünleri çekme
$sql = "SELECT * FROM urunler";
$sonuc = $baglanti->query($sql);

// Admin kontrolü
$is_admin = isset($_SESSION['is_admin']) ? $_SESSION['is_admin'] : false;

// Oturumda username var mı kontrol et
$username = isset($_SESSION['username']) ? $_SESSION['username'] : null;
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Ürünler</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        /* Kartların düzeni */
        .card-deck {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: space-between;
        }

        .card {
            width: 20%;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
            display: flex;
            flex-direction: column;
        }

        .card:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
        }

        .card img {
            width: 100%;
            height: 300px;
            object-fit: cover;
            border-bottom: 1px solid #ddd;
        }

        .card-body {
            flex-grow: 1;
            padding: 25px;
        }

        .card-title {
            font-size: 1.2rem;
            font-weight: bold;
        }

        .card-text {
            font-size: 1rem;
            margin: 10px 0;
        }

        .price {
            font-size: 1.1rem;
            color: #28a745;
            font-weight: bold;
        }

        .card-footer {
            text-align: center;
            margin-bottom: 10px;
        }

        .btn-add-to-cart {
            display: inline-block;
            padding: 8px 15px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.3s;
        }

        .btn-add-to-cart:hover {
            background-color: #218838;
        }

        /* Responsive düzenlemeler */
        @media (max-width: 1200px) {
            .card {
                width: 30%;
            }
        }

        @media (max-width: 768px) {
            .card {
                width: 45%;
            }
        }

        @media (max-width: 480px) {
            .card {
                width: 100%;
            }
        }

        /* Hoşgeldiniz Bildirimi */
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
    </style>
</head>
<body>
<header>
     <h1><a href="index.php"><img src="./images/logo.png" alt="" style="width:50px;"></a></h1>
    <nav>
        <a href="index.php">Ana Sayfa</a>
        <a href="products.php">Ürünler</a>
        <a href="contact.php">İletişim</a>

        <?php if ($username): ?>
            <a href="logout.php"><i class="fa-regular fa-user"></i></a>
            
            <?php if ($is_admin): ?>
                <a href="addproducts.php"><button>Ürün Ekle</button></a>
                <a href="orders.php"><button>Sipariş Onayla</button></a> <!-- Admin'e özel sipariş onay butonu -->
            <?php else: ?>
                <a href="orders.php"><button>Siparişlerim</button></a> <!-- Kullanıcıya özel siparişlerim -->
            <?php endif; ?>
        <?php else: ?>
            <a href="login.php"><i class="fa-regular fa-user"></i></a>
        <?php endif; ?>

        <!-- Sepet ikonu ve ürün sayısı -->
        <a href="cart_view.php" style="position: relative; text-decoration: none; color: inherit;">
            <i class="fa-solid fa-cart-shopping" style="font-size: 1.5rem;"></i>
            <span id="cart-count" style="position: absolute; top: -5px; right: -10px; background-color: red; color: white; font-size: 0.9rem; font-weight: bold; border-radius: 50%; padding: 2px 6px;">
                <?php echo array_sum($_SESSION['cart']); ?>
            </span>
        </a>
    </nav>
</header>

<main>
    <!-- Hoşgeldiniz Bildirimi -->
    <?php if ($username): ?>
        <div class="welcome-notification" id="welcomeNotification">
            Hoşgeldiniz, <?php echo htmlspecialchars($username); ?>!
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

    <h1>LionLustrous NEW!</h1>
    <div class="container">
        <div class="card-deck">
            <?php
            if ($sonuc->num_rows > 0) {
                while ($satir = $sonuc->fetch_assoc()) {
                    $urun_id = $satir['id'];
                    $urun_adi = htmlspecialchars($satir['urun_adi']);
                    $urun_aciklama = htmlspecialchars($satir['urun_aciklama']);
                    $fiyat = number_format($satir['fiyat'], 2);
                    $stok = $satir['stok'];
                    $fotoğraf = $satir['fotoğraf'];

                    $fotoğraf_goruntule = $fotoğraf ? "<img src='" . htmlspecialchars($fotoğraf) . "' alt='Ürün Fotoğrafı'>" : "<img src='default-image.jpg' alt='Varsayılan Fotoğraf'>";

                    echo "<div class='card'>
                            {$fotoğraf_goruntule}
                            <div class='card-body'>
                                <h5 class='card-title'>{$urun_adi}</h5>
                                <p class='card-text'>{$urun_aciklama}</p>
                                <p class='price'>{$fiyat}₺</p>
                            </div>
                            <div class='card-footer'>
                                <button class='btn-add-to-cart' onclick='addToCart($urun_id)'>Sepete Ekle</button>
                                <small class='text-muted'>{$stok} adet stokta</small>
                            </div>
                          </div>";
                }
            } else {
                echo "<p>Hiç ürün bulunamadı.</p>";
            }
            ?>
        </div>
    </div>
</main>

<footer>
    <p>© 2024 Saat Satış Sitesi Tüm Haklar Saklidir.</p>
</footer>

<script>
function addToCart(productId) {
    fetch('cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: 'urun_id=' + productId
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            alert("Ürün sepete eklendi!");
            document.getElementById("cart-count").innerText = data.count;
        } else {
            alert("Ürün sepete eklenirken bir hata oluştu!");
        }
    })
    .catch(error => console.error('Hata:', error));
}
</script>

</body>
</html>
