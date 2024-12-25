function addToCart(productName, productPrice, productImage) {
    fetch('add_to_cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `name=${productName}`
    })
    .then(response => response.text())
    .then(data => alert(data))
    .catch(error => console.error('Hata:', error));
}
