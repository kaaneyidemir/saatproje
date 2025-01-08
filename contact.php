<?php
session_start(); // Oturum başlatma

// Kullanıcı giriş yaptıysa, oturum bilgilerini al
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
    $is_admin = ($_SESSION['username'] == 'admin' && isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1);
} else {
    $username = null;
    $is_admin = false;
}

include('db.php');

// Yorumları veritabanından çekme ve kullanıcı adı bilgilerini almak için 'users' tablosunu ekleme
$sql = "SELECT u.username, p.urun_adi, o.comment 
        FROM orders o 
        JOIN urunler p ON o.product_id = p.id
        JOIN users u ON o.user_id = u.id 
        WHERE o.comment IS NOT NULL";
$stmt = $conn->prepare($sql);
$stmt->execute();
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>İletişim - Yorumlar</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }

        header {
            background-color: #4CAF50;
            color: white;
            padding: 10px 0;
            text-align: center;
        }

        nav {
            text-align: center;
            margin-top: 10px;
        }

        nav a {
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            font-size: 16px;
            margin: 0 10px;
            border-radius: 5px;
        }

        nav a:hover {
            background-color: #45a049;
        }

        main {
            padding: 20px;
            max-width: 800px;
            margin: 20px auto;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 24px;
            color: #333;
        }

        .comment-container {
            margin: 15px 0;
            padding: 15px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .comment-container:hover {
            background-color: #f1f1f1;
        }

        .comment-container strong {
            display: block;
            font-size: 16px;
            margin-bottom: 5px;
            color: #333;
        }

        .comment-container .product-name {
            font-style: italic;
            font-size: 14px;
            color: #555;
        }

        .comment-container .comment-text {
            margin-top: 10px;
            font-size: 14px;
            line-height: 1.6;
            color: #444;
            padding: 10px;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 5px;
            position: relative;
        }

        .comment-container .comment-text:before {
            content: "";
            position: absolute;
            top: -10px;
            left: 20px;
            width: 0;
            height: 0;
            border-width: 10px;
            border-style: solid;
            border-color: transparent transparent #fff transparent;
        }

        .no-comments {
            text-align: center;
            font-size: 18px;
            color: #777;
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
                    <a href="addproducts.php"><button>Ürün Ekle</button></a>
                    <a href="admin_approve.php"><button>Sipariş Onay</button></a>
                <?php endif; ?>
                <a href="orders.php">Siparişlerim</a>
            <?php else: ?>
                <a href="login.php"><i class="fa-regular fa-user"></i> Giriş Yap</a>
            <?php endif; ?>
        </nav>
    </header>

    <main>
        <h2>Tüm Yorumlar</h2>
        <?php if (!empty($comments)): ?>
            <?php foreach ($comments as $comment): ?>
                <div class="comment-container">
                    <strong><?php echo htmlspecialchars($comment['username']); ?></strong>
                    <span class="product-name"><?php echo htmlspecialchars($comment['urun_adi']); ?></span>
                    <div class="comment-text">
                        <?php echo nl2br(htmlspecialchars($comment['comment'])); ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="no-comments">Henüz yorum yapılmamıştır.</p>
        <?php endif; ?>
    </main>
</body>
</html>
