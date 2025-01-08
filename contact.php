<?php
session_start(); // Oturum başlatma

// Kullanıcı giriş yaptıysa, oturum bilgilerini al
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
    $user_id = $_SESSION['user_id'];  // Kullanıcı ID'si (veritabanından)
    $is_admin = ($_SESSION['username'] == 'admin' && isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1);
} else {
    $username = null;
    $user_id = null;
    $is_admin = false;
}

include('db.php');

// Yorumları veritabanından çekme ve kullanıcı adı bilgilerini almak için 'users' tablosunu ekleme
$sql = "SELECT u.username, p.urun_adi, o.comment, o.like_count, o.dislike_count, o.order_id, o.admin_response 
        FROM orders o 
        JOIN urunler p ON o.product_id = p.id
        JOIN users u ON o.user_id = u.id 
        WHERE o.comment IS NOT NULL";
$stmt = $conn->prepare($sql);
$stmt->execute();
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Kullanıcının daha önce oy verip vermediğini kontrol et
function hasVoted($order_id, $user_id, $conn) {
    $sql = "SELECT * FROM votes WHERE order_id = :order_id AND user_id = :user_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':order_id', $order_id);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    return $stmt->rowCount() > 0; // Eğer kullanıcı oylama yaptıysa, true döner
}

// Like ve Dislike işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id']) && isset($_POST['action']) && $user_id) {
    $order_id = $_POST['order_id'];
    $action = $_POST['action'];

    // Kullanıcının bu yorumu oylayıp oylamadığını kontrol et
    if (!hasVoted($order_id, $user_id, $conn)) {
        if ($action == 'like') {
            $stmt = $conn->prepare("UPDATE orders SET like_count = like_count + 1 WHERE order_id = :order_id");
            $stmt->bindParam(':order_id', $order_id);
            $stmt->execute();
            $stmt = $conn->prepare("INSERT INTO votes (order_id, user_id, vote) VALUES (:order_id, :user_id, 'like')");
            $stmt->bindParam(':order_id', $order_id);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
        } elseif ($action == 'dislike') {
            $stmt = $conn->prepare("UPDATE orders SET dislike_count = dislike_count + 1 WHERE order_id = :order_id");
            $stmt->bindParam(':order_id', $order_id);
            $stmt->execute();
            $stmt = $conn->prepare("INSERT INTO votes (order_id, user_id, vote) VALUES (:order_id, :user_id, 'dislike')");
            $stmt->bindParam(':order_id', $order_id);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
        }
    }

    header("Location: contact.php"); // Yönlendirme işlemi
    exit();
}

// Admin cevabı işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['response']) && $is_admin) {
    $order_id = $_POST['order_id'];
    $response = $_POST['response'];

    // Admin cevabını veritabanına kaydet
    $stmt = $conn->prepare("UPDATE orders SET admin_response = :response WHERE order_id = :order_id");
    $stmt->bindParam(':response', $response);
    $stmt->bindParam(':order_id', $order_id);
    $stmt->execute();

    header("Location: contact.php"); // Yönlendirme işlemi
    exit();
}
?>

<!-- HTML Kısmı -->
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
            background-color:rgb(9, 65, 11);
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

        .like-dislike {
            margin-top: 10px;
        }

        .like-dislike button {
            padding: 5px 10px;
            font-size: 14px;
            margin-right: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .like-dislike button:hover {
            background-color: #45a049;
        }

        .no-comments {
            text-align: center;
            font-size: 18px;
            color: #777;
        }

        .response-form {
            margin-top: 20px;
        }

        .response-form textarea {
            width: 100%;
            height: 100px;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
            margin-bottom: 10px;
        }

        .response-form button {
            padding: 10px 15px;
            font-size: 14px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .response-form button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
<header>
    <h1><a href="index.php"><img src="./images/logo2.png" alt="" style="width:50px"></a></h1>
    <nav style="display: flex; justify-content: center; width: 100%;">
        <div style="display: flex; gap: 20px;">
            <a href="index.php">Ana Sayfa </a>
            <a href="products.php">Ürünler </a>
            <a href="contact.php">İletişim </a>

            <?php if ($username): ?>
                <a href="logout.php"><i class="fa-solid fa-door-open"></i> Çıkış</a>

                <?php if ($is_admin): ?>
                    <a href="addproducts.php">Ürün Ekle </a>
                    <a href="admin_approve.php">Sipariş Onay</a>
                    <a href="discount_code_page.php">İndirim Kodu Sayfası</a>
                <?php endif; ?>
                
                <a href="orders.php">Siparişlerim</a>
            <?php else: ?>
                <a href="login.php"><i class="fa-solid fa-door-open"></i> Giriş Yap</a>
            <?php endif; ?>
        </div>
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
                <div class="like-dislike">
                    <?php if (!$user_id || !hasVoted($comment['order_id'], $user_id, $conn)): ?>
                        <form action="contact.php" method="POST" style="display: inline;">
                            <input type="hidden" name="order_id" value="<?php echo $comment['order_id']; ?>">
                            <button type="submit" name="action" value="like">Like (<?php echo $comment['like_count']; ?>)</button>
                        </form>
                        <form action="contact.php" method="POST" style="display: inline;">
                            <input type="hidden" name="order_id" value="<?php echo $comment['order_id']; ?>">
                            <button type="submit" name="action" value="dislike">Dislike (<?php echo $comment['dislike_count']; ?>)</button>
                        </form>
                    <?php else: ?>
                        <p>Yorumunuza oy verdiniz!</p>
                    <?php endif; ?>
                </div>

                <?php if ($is_admin && $comment['admin_response'] === null): ?>
                    <form action="contact.php" method="POST">
                        <input type="hidden" name="order_id" value="<?php echo $comment['order_id']; ?>">
                        <textarea name="response" rows="4" placeholder="Admin cevabınızı yazın..." required></textarea><br>
                        <button type="submit">Yanıtla</button>
                    </form>
                <?php elseif ($comment['admin_response'] !== null): ?>
                    <p><strong>Admin Cevabı:</strong> <?php echo nl2br(htmlspecialchars($comment['admin_response'])); ?></p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="no-comments">Henüz yorum yapılmamıştır.</p>
    <?php endif; ?>
</main>
</body>
</html>
