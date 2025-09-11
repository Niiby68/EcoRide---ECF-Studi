//
//  Page de Détail d'un Trajet ( Javascript )
//  Chemin : /frontend/js/detail-trajet.js
//



document.addEventListener('DOMContentLoaded', function () {
	const triggerBtn = document.getElementById('btn-participer');
	const confirmBtn = document.getElementById('confirm-participation');
	const modalEl = document.getElementById('confirmParticipationModal');
	
	if (!triggerBtn || !confirmBtn || !modalEl) return;

	const modal = new bootstrap.Modal(modalEl);

	// Clic sur "Participer" => ouvrir la modale
	triggerBtn.addEventListener('click', function () {
		modal.show();
	});

	// Clic sur "Oui, je confirme" => redirection vers participer.php
	confirmBtn.addEventListener('click', function () {
		const url = triggerBtn.getAttribute('data-url');
		if (url) window.location.href = url;
	});
});
