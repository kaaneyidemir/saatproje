<?php
session_start();
$sepet_sayisi = isset($_SESSION['sepet']) ? array_sum(array_column($_SESSION['sepet'], 'adet')) : 0;
echo $sepet_sayisi;
?>
<script>
function addToCart(productId) {
    // Ürün bilgilerini PHP'ye göndermek için elemanları seç
    const card = event.target.closest('.card');
    const urunAdi = card.querySelector('.card-title').innerText;
    const fiyat = card.querySelector('.price').innerText.replace('₺', '').trim();

    // AJAX isteği gönder
    fetch('add_to_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `id=${productId}&urun_adi=${urunAdi}&fiyat=${fiyat}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            alert(data.message);
            updateCartCount();
        } else {
            alert("Ürün sepete eklenirken bir hata oluştu.");
        }
    });
}

// Sepet sayısını güncellemek için bir fonksiyon
function updateCartCount() {
    fetch('get_cart_count.php')
        .then(response => response.text())
        .then(count => {
            document.getElementById('cart-count').innerText = count;
        });
}
</script>
