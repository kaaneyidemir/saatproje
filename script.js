document.addEventListener('DOMContentLoaded', function() {
    const inputField = document.getElementById('input');
    inputField.addEventListener('keydown', function(e) {
      if (e.code === 'Enter') {
        let input = inputField.value;
        inputField.value = '';
        fetch('/api/chat', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({ message: input })
        })
        .then(response => response.json())
        .then(data => {
          // API yanıtını işleyin
          console.log(data);
        })
        .catch(error => {
          console.error('Hata:', error);
        });
      }
    });
  });
  