//
//  Page de connexion ( Javascript )
//  Chemin : /frontend/js/login.js
//

document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('form-login');
  const messageBox = document.getElementById('form-message');
  const messageTxt = document.getElementById('form-message-text');
  const validationBtn = document.getElementById('validation');

  if (!form) return;

  form.addEventListener('submit', e => {
    e.preventDefault();

    // Laisse le navigateur gérer la validation HTML5
    if (!form.checkValidity()) {
      form.reportValidity();
      return;
    }

    const formData = new FormData(form);

    // Désactiver le bouton + spinner
    let originalHTML = '';
    if (validationBtn) {
      validationBtn.disabled = true;
      originalHTML = validationBtn.innerHTML;
      validationBtn.innerHTML =
        '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Connexion…';
    }

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

          const target = data.redirect || 'espace-client.php';
          setTimeout(() => {
            window.location.href = target;
          }, 1500);
        } else {
          if (validationBtn) {
            validationBtn.disabled = false;
            validationBtn.innerHTML = originalHTML || 'Se connecter';
          }

          messageBox.classList.add('alert-danger');

          if (data.message === 'Échec de la vérification CSRF.') {
            messageTxt.textContent =
              "Votre session a expiré, merci de recharger la page.";
          } else if (data.message && data.message.startsWith('Trop de tentatives')) {
            messageTxt.textContent = data.message;

            if (validationBtn) {
              validationBtn.disabled = true;

              if (data.remaining_seconds) {
                let seconds = data.remaining_seconds;
                const interval = setInterval(() => {
                  if (seconds > 0) {
                    validationBtn.innerHTML = `Bloqué (${seconds}s)`;
                    seconds--;
                  } else {
                    clearInterval(interval);
                    validationBtn.disabled = false;
                    validationBtn.innerHTML = 'Se connecter';
                  }
                }, 1000);
              } else {
                setTimeout(() => {
                  validationBtn.disabled = false;
                  validationBtn.innerHTML = 'Se connecter';
                }, 5000);
              }
            }
          } else {
            messageTxt.textContent = data.message || 'Identifiants invalides.';
          }
        }
      })
      .catch(err => {
        console.error('Erreur AJAX :', err);
        if (validationBtn) {
          validationBtn.disabled = false;
          validationBtn.innerHTML = originalHTML || 'Se connecter';
        }
        messageBox.classList.remove('d-none', 'alert-success');
        messageBox.classList.add('alert-danger');
        messageTxt.textContent =
          'Une erreur technique est survenue. Veuillez réessayer.';
      });
  });
});
