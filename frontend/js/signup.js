//
//  Page d'inscription ( Javascript )
//  Chemin : /frontend/js/signup.js
//



document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('form-inscription');
  const messageBox = document.getElementById('form-message');
  const messageTxt = document.getElementById('form-message-text');
  const validationBtn = document.getElementById('validation');
  const captchaLabel = document.getElementById('captcha-label'); 

  if (!form) return;

  // Fonction pour régénérer le captcha via AJAX
  function refreshCaptcha() {
    fetch('/backend/captcha.php')
      .then(res => res.json())
      .then(data => {
        if (data.success && captchaLabel) {
          captchaLabel.textContent = data.question;
        }
      })
      .catch(err => {
        console.error("Erreur lors de la régénération du captcha :", err);
      });
  }

  form.addEventListener('submit', e => {
    e.preventDefault();
    const formData = new FormData(form);

    // Désactiver dès l’envoi + spinner
    let originalHTML = '';
    if (validationBtn) {
      validationBtn.disabled = true;
      originalHTML = validationBtn.innerHTML;
      validationBtn.innerHTML =
        '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Inscription…';
    }

    fetch('/backend/signup_traitement.php', {
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

        // Redirection
        const target = data.redirect || 'login.php';
        setTimeout(() => { window.location.href = target; }, 1500);
      } else {
        // Échec → réactiver + restaurer le bouton
        if (validationBtn) {
          validationBtn.disabled = false;
          validationBtn.innerHTML = originalHTML || "S'inscrire";
        }
        messageBox.classList.add('alert-danger');

        // Cas particulier captcha
        if (data.message && data.message.includes("anti-robot")) {
          messageTxt.textContent = "Vérification anti-robot échouée. Nouveau captcha généré.";
          refreshCaptcha(); // 👈 régénère la question
        } 
        // Cas particulier CSRF
        else if (data.message === "Échec de la vérification CSRF.") {
          messageTxt.textContent = "Votre session a expiré, merci de recharger la page.";
        }
        // Autres erreurs (email déjà utilisé, pseudo indisponible, etc.)
        else {
          messageTxt.textContent = data.message || "Une erreur est survenue.";
        }
      }
    })
    .catch(err => {
      console.error('Erreur AJAX :', err);
      if (validationBtn) {
        validationBtn.disabled = false;
        validationBtn.innerHTML = originalHTML || "S'inscrire";
      }
      messageBox.classList.remove('d-none', 'alert-success');
      messageBox.classList.add('alert-danger');
      messageTxt.textContent = "Une erreur technique est survenue. Veuillez réessayer.";
    });
  });
});
