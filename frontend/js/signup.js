//
//	Moteur Javascript pour signup.php
//
//	Chemin : /frontend/js/signup.js
//



document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("form-inscription");
  const messageBox = document.getElementById("form-message");
  const messageText = document.getElementById("form-message-text");

  if (!form) return;

  form.addEventListener("submit", function (e) {
    e.preventDefault();
    const formData = new FormData(form);

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
        form.reset();
        if (data.redirect) {
          setTimeout(() => {
            window.location.href = data.redirect;
          }, 1500);
        }
      } else {
        messageBox.classList.add('alert-danger');
        messageText.textContent = data.message;
      }
    })
    .catch(error => {
      console.error('Erreur AJAX :', error);
      messageBox.classList.remove('d-none','alert-success');
      messageBox.classList.add('alert-danger');
      messageText.textContent = "Une erreur technique est survenue.";
    });
  });
});
