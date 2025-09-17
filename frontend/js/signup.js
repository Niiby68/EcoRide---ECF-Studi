//
//  Page d'inscription ( Javascript )
//  Chemin : /frontend/js/signup.js
//



document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('form-inscription');
  const messageBox = document.getElementById('form-message');
  const messageText = document.getElementById('form-message-text');
  const validationBtn = document.getElementById('validation');

  if (!form) return;

  const nextField = form.querySelector('input[name="next"]');
  
  if (nextField && !nextField.value) {
    const params = new URLSearchParams(location.search);
    nextField.value = params.get('next') || '';
  }

  form.addEventListener('submit', function (e) {
    e.preventDefault();
    const formData = new FormData(form);
	
	if (!formData.get('csrf_token')) {
	  console.error('CSRF token manquant dans le formulaire.');
	  return;
	}

    let originalHTML = '';
    if (validationBtn) {
      validationBtn.disabled = true;
      originalHTML = validationBtn.innerHTML;
      validationBtn.innerHTML =
        '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Inscription…';
    }

    fetch('/backend/signup_traitement.php', {
      method: 'POST',
      headers: {'X-Requested-With': 'XMLHttpRequest'},
      body: formData
    })
    .then(response => response.json())
    .then(data => {
      messageBox.classList.remove('d-none','alert-success','alert-danger');

      if (data.success) {
        messageBox.classList.add('alert-success');
        messageText.textContent = data.message;

        const target = data.redirect || 'index.php';
        setTimeout(() => { window.location.href = target; }, 1500);
      } else {
        if (validationBtn) {
          validationBtn.disabled = false;
          validationBtn.innerHTML = originalHTML || 'Valider';
        }
        messageBox.classList.add('alert-danger');
        
		if (data.message === 'Échec de la vérification CSRF.') {
			messageText.textContent = "Votre session a expiré, merci de recharger la page.";
        } else {
			messageText.textContent = data.message || "L'inscription a échoué.";
        }
      }
    })
    .catch(error => {
      console.error('Erreur AJAX :', error);
      if (validationBtn) {
        validationBtn.disabled = false;
        validationBtn.innerHTML = originalHTML || 'Valider';
      }
      messageBox.classList.remove('d-none','alert-success');
      messageBox.classList.add('alert-danger');
      messageText.textContent = 'Une erreur technique est survenue.';
    });
  });
});
