//
//  Page des covoiturages ( Javascript )
//  Chemin : /frontend/js/recherche-covoiturages.js
//

document.addEventListener('DOMContentLoaded', () => {
    // Sélection du formulaire, du bouton et du conteneur de résultats
    const form = document.getElementById('form-recherche-trajet');
    const resultContainer = document.getElementById('reponse-trajet');
    const submitButton = form?.querySelector('button[type="submit"]');

    if (!form || !resultContainer || !submitButton) return;

    // Fonction d'échappement pour éviter les injections
    const escapeHTML = (str) => {
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    };

    // Vérification de la validité de la date
    const isDateValid = (inputDate) => {
        const today = new Date();
        today.setHours(0, 0, 0, 0);

        const selectedDate = new Date(inputDate);
        selectedDate.setHours(0, 0, 0, 0);

        return selectedDate >= today;
    };

    form.addEventListener('submit', function(e) {
        e.preventDefault();
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

        // Pré-affichage : état de chargement
        submitButton.disabled = true;
        resultContainer.innerHTML = '<p id="loading-message">Chargement...</p>';

        // Appel au backend
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
            // Réinitialisation de l'affichage
            resultContainer.innerHTML = '';

            if (data?.success && Array.isArray(data.resultats)) {
                if (data.resultats.length === 0) {
                    // Message plus visuel lorsqu'aucun trajet n'est trouvé
                    resultContainer.innerHTML = `
                        <div class="no-results">
                            <h3>Aucun trajet trouvé</h3>
                            <p>Nous n'avons pas trouvé de covoiturage correspondant à vos critères.</p>
                            <img src="img/covoiturages/voiture-impasse.png" alt="Voiture dans l'impasse" class="img-fluid mt-3 rounded my-3">
                        </div>
                    `;
                } else {
                    data.resultats.forEach(trajet => {
                        // Formatage de la date et de l'heure
                        let formattedDateTime = trajet.date_depart;
                        try {
                            const dateObj = new Date(trajet.date_depart);
                            formattedDateTime = dateObj.toLocaleString(undefined, {
                                day: 'numeric', month: 'long', year: 'numeric',
                                hour: '2-digit', minute: '2-digit'
                            });
                        } catch (_) {}

                        // Création de la carte trajet
                        const card = document.createElement('div');
                        card.className = 'trajet-card';
                        card.innerHTML = `
                            <h3>Trajet du ${escapeHTML(formattedDateTime)}</h3>
                            <p><strong>Départ :</strong> ${escapeHTML(trajet.adresse_depart)}</p>
                            <p><strong>Arrivée :</strong> ${escapeHTML(trajet.adresse_arrivee)}</p>
                            <p><strong>Prix :</strong> ${escapeHTML(trajet.prix)} crédits</p>
                            <p><strong>Places restantes :</strong> ${escapeHTML(trajet.nb_places_restantes)} / ${escapeHTML(trajet.nb_places_total)}</p>
                            <p><strong>Chauffeur :</strong> ${escapeHTML(trajet.pseudo)}${trajet.note_moyenne !== null ? ` (${escapeHTML(trajet.note_moyenne)} ⭐)` : ''}</p>
                            <p><strong>Énergie :</strong> ${escapeHTML(trajet.energie)}${trajet.voyage_ecologique ? ' → 🌿 Voyage écologique' : ''}</p>
                            ${trajet.alternative ? '<p class="text-danger">⚠️ Ce trajet est une proposition alternative à une autre date.</p>' : ''}
                        `;
                        resultContainer.appendChild(card);
                    });
                }
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
            // Rétablissement du bouton
            submitButton.disabled = false;
        });
    });
});
