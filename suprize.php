<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hediye Kutusu Efekti</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            overflow: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        /* Hediye Kutusu */
        #box {
            width: 250px;
            height: 250px;
            background-color: #ff6f61;
            margin: 100px auto;
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
            font-size: 24px;
            font-weight: bold;
            cursor: pointer;
            border: 10px solid #fff;
            border-radius: 15px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
            background-image: url('https://www.freeiconspng.com/uploads/gift-box-png-4.png');
            background-size: cover;
            background-position: center;
            transition: transform 0.3s ease-in-out;
            text-align: center;
        }

        /* Kutu Tıklanınca Zıplama */
        #box.clicked {
            transform: scale(1.1);
        }

        /* Konfeti Efekti */
        .confetti {
            position: absolute;
            width: 10px;
            height: 10px;
            background-color: #ffd700;
            border-radius: 50%;
            opacity: 0;
            animation: confettiAnimation 3s infinite;
        }

        /* Mesaj */
        #message {
            display: none;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: rgba(255, 255, 255, 0.8);
            padding: 20px;
            border-radius: 10px;
            color: #ff6f61;
            font-size: 18px;
            border: 2px solid #ff6f61;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            font-weight: bold;
            text-align: center;
            animation: messageFadeIn 0.5s forwards;
        }

        @keyframes confettiAnimation {
            0% {
                opacity: 1;
                transform: translate(0, 0) rotate(0deg);
            }
            100% {
                opacity: 0;
                transform: translate(300px, 500px) rotate(720deg);
            }
        }

        @keyframes messageFadeIn {
            0% {
                opacity: 0;
            }
            100% {
                opacity: 1;
            }
        }
    </style>
</head>
<body>

    <div id="box">
        <span>Tıkla!</span>
    </div>
    <div id="message">20% KAZANDINIZ TEBRİKLER İŞTE KODUNUZ "5S184KI0OD"</div>

    <script>
        const box = document.getElementById("box");
        const message = document.getElementById("message");

        box.addEventListener("click", function() {
            box.classList.toggle("clicked");

            // Konfeti sayısı
            const confettiCount = 250;

            // Konfetileri oluşturma
            for (let i = 0; i < confettiCount; i++) {
                const confetti = document.createElement("div");
                confetti.classList.add("confetti");
                confetti.style.left = `${Math.random() * 100}%`;
                confetti.style.animationDuration = `${Math.random() * 2 + 3}s`;  // Konfeti hızını ayarlamak için
                confetti.style.animationDelay = `${Math.random() * 2}s`;  // Konfeti gecikmesini ayarlamak için
                document.body.appendChild(confetti);
            }

            // Mesajın görünmesini sağla
            message.style.display = "block";

            // 3 saniye sonra mesaj kaybolacak
            setTimeout(function() {
                message.style.display = "none";
            }, 3000);
        });
    </script>

</body>
</html>
