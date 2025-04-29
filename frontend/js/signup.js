// 
//	Moteur Javascript pour signup.php
//
//	/frontend/js/signup.js
//
console.log("Script chargé : prêt pour capter le formulaire.");

document.addEventListener("DOMContentLoaded", () => {
	const form = document.getElementById("form-inscription");

	if (form) {
		form.addEventListener("submit", function (e) {
			e.preventDefault();

			// ➤ Tu ajouteras ici l’appel AJAX plus tard
			console.log("Formulaire soumis !");
		});
	}
});
