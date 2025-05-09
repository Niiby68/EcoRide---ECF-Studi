//
//	Moteur Javascript pour login.php
//
//	Chemin : /frontend/js/login.js
//

document.addEventListener('DOMContentLoaded', () => {
  const form       = document.getElementById('form-login');
  const messageBox = document.getElementById('form-message');
  const messageTxt = document.getElementById('form-message-text');

  form.addEventListener('submit', e => {
    e.preventDefault();
    const formData = new FormData(form);

    fetch('/backend/login_traitement.php', {
      method: 'POST',
      headers: { 'X-Requested-With': 'XMLHttpRequest' },
      body: formData
    })
    .then(res => res.json())
    .then(data => {
      messageBox.classList.remove('d-none', 'alert-success', 'alert-danger');
      if (data.success) {
        messageBox.classList.add('alert-success');
        messageTxt.textContent = data.message;
        setTimeout(() => { window.location.href = 'index.php'; }, 1500);
      } else {
        messageBox.classList.add('alert-danger');
        messageTxt.textContent = data.message;
      }
    })
    .catch(err => {
      console.error('Erreur AJAX :', err);
      window.location.href = 'index.php';
    });
  });
});
