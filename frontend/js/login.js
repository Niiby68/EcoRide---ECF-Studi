//
// Moteur Javascript pour login.php
//
// Chemin : /frontend/js/login.js
//

document.addEventListener('DOMContentLoaded', function () {
	const form = document.getElementById('form-login');
	const messageBox = document.getElementById('form-message');
	const messageText = document.getElementById('form-message-text');

	form.addEventListener('submit', function (e) {
		e.preventDefault();

		const formData = new FormData(form);

		fetch('../backend/login_traitement.php', {
			method: 'POST',
			body: formData
		})
		.then(response => response.json())
		.then(data => {
			messageBox.classList.remove('d-none', 'alert-success', 'alert-danger');

			if (data.success) {
				messageBox.classList.add('alert-success');
				messageText.textContent = data.message;

				// Redirection après succès (ex. tableau de bord)
				setTimeout(() => {
					window.location.href = 'index.html';
				}, 1500);
			} else {
				messageBox.classList.add('alert-danger');
				messageText.textContent = data.message;
			}
		})
		.catch(error => {
			messageBox.classList.remove('d-none', 'alert-success');
			messageBox.classList.add('alert-danger');
			messageText.textContent = "Erreur réseau, veuillez réessayer.";
		});
	});
});
