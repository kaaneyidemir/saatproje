<?php
session_start();
$conn = new mysqli("localhost", "root", "", "test");

if ($conn->connect_error) {
    die("Veritabanı bağlantısı başarısız: " . $conn->connect_error);
}

// Token parametresini al
if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Token'ı veritabanında ara
    $query = "SELECT * FROM users WHERE reset_token = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        // Token doğruysa, kullanıcıyı şifre sıfırlama formuna yönlendir
    } else {
        $error = "Geçersiz bağlantı.";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_password = $_POST["new_password"];
    $confirm_password = $_POST["confirm_password"];

    if ($new_password == $confirm_password) {
        // Yeni şifreyi güncelle
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        $query = "UPDATE users SET password = ?, reset_token = NULL WHERE reset_token = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $hashed_password, $token);
        $stmt->execute();

        echo "Şifreniz başarıyla güncellendi.";
        // Kullanıcıyı giriş yapma sayfasına yönlendir
        header("Location: login.php");
        exit();
    } else {
        $error = "Şifreler uyuşmuyor.";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Şifreyi Yenile</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7fc;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        h2 {
            color: #333;
            margin-bottom: 20px;
        }

        label {
            display: block;
            text-align: left;
            margin-bottom: 8px;
            font-weight: bold;
            color: #555;
        }

        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            background-color: #218838;
        }

        p {
            font-size: 14px;
            margin-top: 15px;
        }

        .error {
            color: red;
            font-size: 14px;
        }

        .success {
            color: green;
            font-size: 14px;
        }
    </style>
</head>
<body>

    <div class="container">
        <form action="reset_password.php?token=<?php echo $token; ?>" method="POST">
            <h2>Yeni Şifre Belirle</h2>

            <?php if (isset($error)) { echo "<p class='error'>$error</p>"; } ?>

            <label for="new_password">Yeni Şifre:</label>
            <input type="password" id="new_password" name="new_password" required>

            <label for="confirm_password">Şifreyi Onayla:</label>
            <input type="password" id="confirm_password" name="confirm_password" required>

            <button type="submit">Şifreyi Güncelle</button>
        </form>
    </div>

</body>
</html>
