//
//  Page des covoiturages ( Javascript )
//  Chemin : /frontend/js/recherche-covoiturages.js
//



document.addEventListener('DOMContentLoaded', function () {
    let trajets = []; // Stockage global pour filtrage

    const form = document.getElementById('form-recherche-trajet');
    const resultContainer = document.getElementById('reponse-trajet');
    const submitButton = form?.querySelector('button[type="submit"]');

	// Sécurité du formulaire
    if (!form || !resultContainer || !submitButton) {
        console.error("Erreur : Formulaire, conteneur ou bouton non trouvé dans le DOM.");
        alert("Erreur critique détectée : le formulaire ou ses éléments sont introuvables. Veuillez contacter l'administrateur du site.");
        return;
    }

	// Sécurité anti-injection HTML
    const escapeHTML = (str) => {
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    };

	// Vérification de la date entrée
    const isDateValid = (inputDate) => {
        const today = new Date();
        today.setHours(0, 0, 0, 0);

        const selectedDate = new Date(inputDate);
        selectedDate.setHours(0, 0, 0, 0);

        return selectedDate >= today;
    };
	
	// Conversion de la durée TIME en minutes totales
	// - Retourne un entier en minutes si le format est correct
	// - Retourne Infinity si la durée est absente/invalide
	//   → Cela permet d'accepter ce trajet si l'utilisateur n'a PAS mis de limite
	//   → Mais de l'exclure si l'utilisateur impose une durée max
	function convertirDureeEnMinutes(dureeStr) {
		if (!dureeStr || !dureeStr.includes(':')) return Infinity;

		const [heures, minutes, secondes] = dureeStr.split(':').map(Number);
		return (heures * 60) + minutes + Math.floor(secondes / 60);
	}

	function afficherTrajets(trajetsAAfficher) {
		resultContainer.innerHTML = '';

		if (trajetsAAfficher.length === 0) {
			resultContainer.innerHTML = `
				<div class="no-results">
					<h3>Aucun trajet trouvé</h3>
					<p>Nous n'avons pas trouvé de covoiturage correspondant à vos critères.</p>
					<img src="img/covoiturages/voiture-impasse.png" alt="Voiture dans l'impasse" class="img-fluid mt-3 rounded my-3">
				</div>
			`;
			return;
		}

		trajetsAAfficher.forEach(trajet => {
			let formattedDateTime = trajet.date_depart;
			try {
				const dateObj = new Date(trajet.date_depart);
				formattedDateTime = dateObj.toLocaleString(undefined, {
					day: 'numeric', month: 'long', year: 'numeric',
					hour: '2-digit', minute: '2-digit'
				});
			} catch (_) {}

			const card = document.createElement('div');
			card.className = 'trajet-card' + (trajet.deja_participe == 1 ? ' participe' : '');
			card.style.border = '1px solid black';
			card.style.padding = '1rem';
			card.style.marginBottom = '1rem';
			card.style.borderRadius = '0.5rem';
			card.innerHTML = `
				<h3>
					Trajet du ${escapeHTML(formattedDateTime)} → ${escapeHTML(trajet.duree_formatee)}
				</h3>
				<p><strong>Départ :</strong> ${escapeHTML(trajet.adresse_depart)}</p>
				<p><strong>Arrivée :</strong> ${escapeHTML(trajet.adresse_arrivee)}</p>
				<p><strong>Énergie :</strong> ${escapeHTML(trajet.energie)}${trajet.voyage_ecologique ? ' → Voyage écologique 🌿' : ''}</p>
				<p><strong>Chauffeur :</strong> ${escapeHTML(trajet.pseudo)}${trajet.note_moyenne !== null ? ` (${escapeHTML(trajet.note_moyenne)} ⭐)` : ''}</p>
				<p><strong>Places restantes :</strong> ${escapeHTML(trajet.nb_places_restantes)} / ${escapeHTML(trajet.nb_places_total)}</p>
				<p><strong>Prix :</strong> ${escapeHTML(trajet.prix)} crédits</p>
				<p><strong>Options :</strong> 
					${trajet.fumeur == 1 ? "🚬 Fumeur accepté" : "🚭 Non-fumeur"} | 
					${trajet.animaux == 1 ? "🐾 Animaux acceptés" : "🚫 Pas d'animaux"}
				</p>
				${trajet.deja_participe == 1 
					? '<div class="alert alert-success mt-2 fw-bold text-center">✅ Vous participez déjà à ce trajet</div>' 
					: ''}
				${trajet.alternative ? '<p class="text-danger">⚠️ Ce trajet est une proposition alternative à une autre date.</p>' : ''}
			`;
			const detailsLink = document.createElement('div');
			detailsLink.style.textAlign = 'right';
			detailsLink.innerHTML = `
				<a href="detail-trajet.php?id=${trajet.id}
				&depart=${encodeURIComponent(form.querySelector('[name="depart"]').value)}
				&arrivee=${encodeURIComponent(form.querySelector('[name="arrivee"]').value)}
				&date=${encodeURIComponent(form.querySelector('[name="date"]').value)}
				&prix=${encodeURIComponent(document.getElementById("filtre-prix").value)}
				&note=${encodeURIComponent(document.getElementById("filtre-note").value)}
				&duree_h=${encodeURIComponent(document.getElementById("filtre-duree-heures").value)}
				&duree_m=${encodeURIComponent(document.getElementById("filtre-duree-minutes").value)}
				${document.getElementById("filtre-ecolo").checked ? '&ecolo=1' : ''}"
				class="details-link">Afficher les détails</a>
			`;
			card.appendChild(detailsLink);
			resultContainer.appendChild(card);
		});
	}

	// Filtrage des trajets
    function filtrerTrajets() {
        const ecolo = document.getElementById("filtre-ecolo").checked;
        const prixMax = parseFloat(document.getElementById("filtre-prix").value) || Infinity;
        const noteMin = parseFloat(document.getElementById("filtre-note").value) || 0;
		const heures = parseInt(document.getElementById("filtre-duree-heures").value) || 0;
		const minutes = parseInt(document.getElementById("filtre-duree-minutes").value) || 0;
		const dureeMax = (heures * 60) + minutes || Infinity;

        const trajetsFiltres = trajets.filter(trajet => {
            return (!ecolo || trajet.energie === "électrique")
                && parseFloat(trajet.prix) <= prixMax
                && convertirDureeEnMinutes(trajet.duree) <= dureeMax
                && parseFloat(trajet.note_moyenne || 0) >= noteMin;
        });

        afficherTrajets(trajetsFiltres);
    }

    document.getElementById("btn-filtrer")?.addEventListener("click", filtrerTrajets);

	// Envoi des données à la page de traitement
    function envoyer_data() {
        const formData = new FormData(form);
        const date = formData.get('date');

        if (!isDateValid(date)) {
            resultContainer.innerHTML = `
                <div class="error-block">
                    <p class="error">La date choisie est antérieure à aujourd'hui. Veuillez sélectionner une date valide.</p>
                    <img src="img/covoiturages/voiture-impasse.png" alt="Voiture dans l'impasse" class="img-fluid mt-3 rounded my-3">
                </div>
            `;
            return;
        }

        submitButton.disabled = true;
        resultContainer.innerHTML = '<p id="loading-message">Chargement...</p>';

        fetch('../backend/recherche-covoiturages.php', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: formData
        })
        .then(response => {
            if (!response.ok) throw new Error(`HTTP ${response.status}`);
            return response.json();
        })
        .then(data => {
            resultContainer.innerHTML = '';

            if (data?.success && Array.isArray(data.resultats)) {
                trajets = data.resultats; // Stockage pour filtrage
				
				// Affichage des filtres uniquement si résultats trouvés
				const filtres = document.getElementById('bloc-filtres');
				if (filtres) {
					filtres.classList.remove('d-none');
				}

				// Affichage des résultats
                afficherTrajets(trajets);
            } else {
                const msg = data?.message || 'Réponse inattendue du serveur.';
                resultContainer.innerHTML = `
                    <div class="error-block">
                        <p class="error">${escapeHTML(msg)}</p>
                        <img src="img/covoiturages/voiture-impasse.png" alt="Voiture dans l'impasse" class="img-fluid mt-3 rounded my-3">
                    </div>
                `;
            }
        })
        .catch(error => {
            resultContainer.innerHTML = `
                <div class="error-block">
                    <p class="error">Erreur technique : ${escapeHTML(error.message)}</p>
                    <img src="img/covoiturages/voiture-impasse.png" alt="Voiture dans l'impasse" class="img-fluid mt-3 rounded my-3">
                </div>
            `;
        })
        .finally(() => {
            submitButton.disabled = false;
        });
    }

	// Ecouteur du bouton "Rechercher"
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        envoyer_data();
    });

	// Récupération des données de recherche
    const depart = form.querySelector('[name="depart"]').value.trim();
    const arrivee = form.querySelector('[name="arrivee"]').value.trim();
    const date = form.querySelector('[name="date"]').value.trim();

	// Lancement automatique de la recherche si pré-remplis
	if (depart !== '' && arrivee !== '' && date !== '') {
		envoyer_data();

		// Filtrage automatique après un court délai
		setTimeout(() => {
			document.getElementById("btn-filtrer")?.click();
		}, 300); // délai léger pour laisser le temps au chargement AJAX
	}
});
