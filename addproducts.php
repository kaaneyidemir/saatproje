<?php
// Veritabanı bağlantısı
$host = "localhost";
$kullanici = "root"; // phpMyAdmin kullanıcı adınız
$sifre = ""; // phpMyAdmin şifreniz
$veritabani = "test";

$baglanti = new mysqli($host, $kullanici, $sifre, $veritabani);
if ($baglanti->connect_error) {
    die("Bağlantı hatası: " . $baglanti->connect_error);
}

session_start();

// Admin kontrolü
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    echo "Bu sayfaya erişim yetkiniz yok!";
    exit();
}

// Güvenlik için girişleri temizleme fonksiyonu
function temizle($veri) {
    global $baglanti;
    return htmlspecialchars($baglanti->real_escape_string(trim($veri)));
}

// Ürün Güncelleme veya Ekleme
$notification = ''; // Bildirim mesajı
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $urunAdi = temizle($_POST['urun_adi']);
    $urunAciklama = temizle($_POST['urun_aciklama']);
    $fiyat = temizle($_POST['fiyat']);
    $stok = temizle($_POST['stok']);
    $urun_id = isset($_POST['urun_id']) ? temizle($_POST['urun_id']) : null;

    // Fotoğraf yükleme işlemi
    $fotoğraf = null;
    if ($_POST['foto_secim'] == "dosya" && isset($_FILES['urun_foto']) && $_FILES['urun_foto']['error'] == 0) {
        $target_dir = "uploads/"; // Yükleme yapılacak klasör
        $target_file = $target_dir . basename($_FILES["urun_foto"]["name"]);

        // Fotoğrafın formatını kontrol et
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowed_formats = ["jpg", "jpeg", "png", "gif"];
        if (in_array($imageFileType, $allowed_formats)) {
            if (move_uploaded_file($_FILES["urun_foto"]["tmp_name"], $target_file)) {
                $fotoğraf = $target_file;
            } else {
                $notification = "Fotoğraf yüklenirken bir hata oluştu.";
            }
        } else {
            $notification = "Geçersiz dosya formatı.";
        }
    } elseif ($_POST['foto_secim'] == "link" && !empty($_POST['urun_foto_link'])) {
        $fotoğraf = temizle($_POST['urun_foto_link']);
    }

    // Eğer fotoğraf seçilmediyse, eski fotoğrafı koruyalım
    if ($urun_id && !$fotoğraf) {
        // Ürünü veritabanından çekip eski fotoğrafı alalım
        $sql_get = "SELECT fotoğraf FROM urunler WHERE id='$urun_id'";
        $result = $baglanti->query($sql_get);
        if ($result->num_rows > 0) {
            $urun = $result->fetch_assoc();
            $fotoğraf = $urun['fotoğraf'];  // Eski fotoğrafı al
        }
    }

    // Güncelleme veya Ekleme işlemi
    if ($urun_id) {
        // Güncelleme işlemi
        $sql_update = "UPDATE urunler SET urun_adi='$urunAdi', urun_aciklama='$urunAciklama', fiyat='$fiyat', stok='$stok', fotoğraf='$fotoğraf' WHERE id='$urun_id'";
        if ($baglanti->query($sql_update) === TRUE) {
            $notification = "Ürün başarıyla güncellendi!";
            header("refresh:2; url=addproducts.php"); // 2 saniye sonra yönlendirme
            exit;
        } else {
            $notification = "Hata: " . $baglanti->error;
        }
    } else {
        // Ekleme işlemi
        $sql_insert = "INSERT INTO urunler (urun_adi, urun_aciklama, fiyat, stok, fotoğraf) VALUES ('$urunAdi', '$urunAciklama', '$fiyat', '$stok', '$fotoğraf')";
        if ($baglanti->query($sql_insert) === TRUE) {
            $notification = "Ürün başarıyla eklendi!";
            header("refresh:2; url=addproducts.php"); // 2 saniye sonra yönlendirme
            exit;
        } else {
            $notification = "Hata: " . $baglanti->error;
        }
    }
}

// Ürün Silme
if (isset($_GET['delete'])) {
    $urun_id = temizle($_GET['delete']);
    $sql_delete = "DELETE FROM urunler WHERE id='$product_id'";
    if ($baglanti->query($sql_delete) === TRUE) {
        header("Location: addproducts.php");
        exit;
    } else {
        echo "Hata: " . $baglanti->error;
    }
}

// Ürün Güncelleme İçin Veritabanından Veri Çekme
$urun = null;
if (isset($_GET['edit'])) {
    $urun_id = temizle($_GET['edit']);
    $sql_get = "SELECT * FROM urunler WHERE id='$urun_id'";
    $result = $baglanti->query($sql_get);
    $urun = $result->fetch_assoc();
}

// Ürünleri listeleme
$sql = "SELECT * FROM urunler";
$sonuc = $baglanti->query($sql);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ürün Ekle/Güncelle</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f4f4;
            font-family: Arial, sans-serif;
        }
        .container {
            margin-top: 50px;
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #333;
        }
        .form-group label {
            font-weight: bold;
        }
        .btn-primary {
            background-color: rgb(0, 255, 47);
            border: none;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        .alert {
            text-align: center;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Ürün Ekle/Güncelle</h1>

    <!-- Bildirim Mesajı -->
    <?php if ($notification): ?>
        <div class="alert alert-info"><?php echo $notification; ?></div>
    <?php endif; ?>

    <form action="addproducts.php" method="POST" enctype="multipart/form-data">
        <!-- Ürün Adı -->
        <div class="form-group">
            <label for="urun_adi">Ürün Adı:</label>
            <input type="text" class="form-control" name="urun_adi" value="<?php echo isset($urun) ? htmlspecialchars($urun['urun_adi']) : ''; ?>" required>
        </div>

        <!-- Ürün Açıklaması -->
        <div class="form-group">
            <label for="urun_aciklama">Ürün Açıklaması:</label>
            <textarea class="form-control" name="urun_aciklama" required><?php echo isset($urun) ? htmlspecialchars($urun['urun_aciklama']) : ''; ?></textarea>
        </div>

        <!-- Fiyat -->
        <div class="form-group">
            <label for="fiyat">Fiyat:</label>
            <input type="number" step="0.01" class="form-control" name="fiyat" value="<?php echo isset($urun) ? htmlspecialchars($urun['fiyat']) : ''; ?>" required>
        </div>

        <!-- Stok -->
        <div class="form-group">
            <label for="stok">Stok:</label>
            <input type="number" class="form-control" name="stok" value="<?php echo isset($urun) ? htmlspecialchars($urun['stok']) : ''; ?>" required>
        </div>

        <!-- Fotoğraf Seçimi -->
        <div class="form-group">
            <label for="foto_secim">Fotoğraf Seçin:</label><br>
            <input type="radio" name="foto_secim" value="dosya" <?php echo (isset($urun) && $urun['fotoğraf']) ? 'checked' : ''; ?> required> Masaüstünden Yükle<br>
            <input type="radio" name="foto_secim" value="link" <?php echo (isset($urun) && !$urun['fotoğraf']) ? 'checked' : ''; ?>> Fotoğraf Linki Ekleyin
        </div>

        <!-- Fotoğraf Yükleme veya Link Alanı -->
        <div class="form-group" id="foto_dosya_input" style="<?php echo isset($urun) && $urun['fotoğraf'] ? 'display:none;' : ''; ?>">
            <label for="urun_foto">Fotoğraf Yükle:</label>
            <input type="file" class="form-control" name="urun_foto">
        </div>

        <div class="form-group" id="foto_link_input" style="<?php echo isset($urun) && !$urun['fotoğraf'] ? 'display:block;' : 'display:none;'; ?>">
            <label for="urun_foto_link">Fotoğraf Linki:</label>
            <input type="text" class="form-control" name="urun_foto_link" value="<?php echo isset($urun) ? htmlspecialchars($urun['fotoğraf']) : ''; ?>" placeholder="Fotoğraf URL'sini girin">
        </div>

        <!-- Ürün ID'si (Güncelleme için) -->
        <input type="hidden" name="urun_id" value="<?php echo isset($urun) ? htmlspecialchars($urun['id']) : ''; ?>">

        <!-- Submit Butonu -->
        <button type="submit" class="btn btn-primary btn-block"><?php echo isset($urun) ? 'Ürünü Güncelle' : 'Ürünü Ekle'; ?></button>
    </form>

    <!-- Ana Sayfaya Dön Butonu -->
    <a href="index.php" class="btn btn-secondary btn-block mt-3">Ana Sayfaya Dön</a>
</div>

<h2 class="container mt-5">Ürünler</h2>
<div class="container">
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Ürün Adı</th>
                <th>Ürün Açıklaması</th>
                <th>Fiyat</th>
                <th>Stok</th>
                <th>Fotoğraf</th>
                <th>İşlemler</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($sonuc->num_rows > 0) {
                while ($satir = $sonuc->fetch_assoc()) {
                    echo "<tr>
                            <td>" . htmlspecialchars($satir['id']) . "</td>
                            <td>" . htmlspecialchars($satir['urun_adi']) . "</td>
                            <td>" . htmlspecialchars($satir['urun_aciklama']) . "</td>
                            <td>" . htmlspecialchars($satir['fiyat']) . "₺</td>
                            <td>" . htmlspecialchars($satir['stok']) . "</td>
                            <td><img src='" . htmlspecialchars($satir['fotoğraf']) . "' width='50'></td>
                            <td>
                                <a href='addproducts.php?edit=" . htmlspecialchars($satir['id']) . "' class='btn btn-warning btn-sm'>Düzenle</a>
                                <a href='addproducts.php?delete=" . htmlspecialchars($satir['id']) . "' class='btn btn-danger btn-sm' onclick='return confirm(\"Silmek istediğinize emin misiniz?\")'>Sil</a>
                            </td>
                        </tr>";
                }
            } else {
                echo "<tr><td colspan='7'>Hiç ürün bulunamadı.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<script>
    // Fotoğraf seçimine göre inputları gösterme/gizleme
    document.querySelectorAll('input[name="foto_secim"]').forEach(input => {
        input.addEventListener('change', function() {
            if (this.value == 'dosya') {
                document.getElementById('foto_dosya_input').style.display = 'block';
                document.getElementById('foto_link_input').style.display = 'none';
            } else {
                document.getElementById('foto_dosya_input').style.display = 'none';
                document.getElementById('foto_link_input').style.display = 'block';
            }
        });
    });
</script>

</body>
</html>
