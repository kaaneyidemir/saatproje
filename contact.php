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

include('db.php');

// Yorumları veritabanından çekme ve kullanıcı adı bilgilerini almak için 'users' tablosunu ekleme
$sql = "SELECT u.username, p.urun_adi, o.comment 
        FROM orders o 
        JOIN urunler p ON o.product_id = p.id
        JOIN users u ON o.user_id = u.id  -- 'user_id' orders tablosunda kullanıcının ID'sini temsil eder
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
    <link rel="stylesheet" href="style.css">
    <script src="scripts.js"></script>
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
            max-width: 1000px;
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

        .comment-table {
            width: 100%;
            border-collapse: collapse;
            margin: 0 auto;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .comment-table th, .comment-table td {
            padding: 12px 15px;
            text-align: left;
            border: 1px solid #ddd;
        }

        .comment-table th {
            background-color: #4CAF50;
            color: white;
        }

        .comment-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .comment-table tr:hover {
            background-color: #f1f1f1;
        }

        .comment-table td {
            font-size: 14px;
        }

        .comment-box {
            padding: 10px;
            font-size: 14px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .comment-box strong {
            display: block;
            font-size: 16px;
            margin-bottom: 5px;
            color: #333;
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

    <main>
        <h2>Tüm Yorumlar</h2>
        <?php if (!empty($comments)): ?>
            <table class="comment-table">
                <tr>
                    <th>Kullanıcı Adı</th>
                    <th>Ürün Adı</th>
                    <th>Yorum</th>
                </tr>
                <?php foreach ($comments as $comment): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($comment['username']); ?></td>
                        <td><?php echo htmlspecialchars($comment['urun_adi']); ?></td>
                        <td class="comment-box"><?php echo nl2br(htmlspecialchars($comment['comment'])); ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p class="no-comments">Henüz yorum yapılmamıştır.</p>
        <?php endif; ?>
    </main>
</body>
</html>
