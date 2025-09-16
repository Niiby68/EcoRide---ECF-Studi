//
//  Page de Détail d'un Trajet ( Javascript )
//  Chemin : /frontend/js/detail-trajet.js
//



document.addEventListener('DOMContentLoaded', function () {
    const triggerBtn = document.getElementById('btn-participer');
    const confirmBtn = document.getElementById('confirm-participation');
    const modalEl = document.getElementById('confirmParticipationModal');
    const formParticiper = document.getElementById('form-participer');
    
    if (!triggerBtn || !confirmBtn || !modalEl || !formParticiper) return;

    const modal = new bootstrap.Modal(modalEl);

    // Clic sur "Participer" => ouvrir la modale
    triggerBtn.addEventListener('click', function () {
        modal.show();
    });

    // Clic sur "Oui, je confirme" => soumettre le formulaire
    confirmBtn.addEventListener('click', function () {
        formParticiper.submit();
    });
});
