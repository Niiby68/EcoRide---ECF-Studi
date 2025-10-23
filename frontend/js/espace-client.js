//
//  Espace Client ( Javascript )
//  Chemin : /frontend/js/espace-client.js
//



document.addEventListener('DOMContentLoaded', function () {
    const sidebarLinks = document.querySelectorAll('.client-sidebar [data-ajax]');
    const selectMenu = document.getElementById('nav-client');
    const contentZone = document.getElementById('contenu-dynamique');
	const mainTitle = document.querySelector('main.container h1');

    // Fonction principale de chargement AJAX
	function loadContent(page) {
		contentZone.classList.remove('visible');

		fetch(`/backend/ajax-client.php?page=${encodeURIComponent(page)}`)
		.then(response => {
			if (!response.ok) throw new Error("Erreur réseau");
			return response.text();
		})
		.then(html => {
			contentZone.innerHTML = html;
			updateActiveLink(page);
			updatePageTitle(page);
			setTimeout(() => contentZone.classList.add('visible'), 50);
		})
		.catch(error => {
			contentZone.innerHTML = `<p class="text-danger">Une erreur est survenue lors du chargement.</p>`;
			console.error(error);
			setTimeout(() => contentZone.classList.add('visible'), 50);
		});
	}

    // Met à jour l’état du lien actif dans la sidebar
    function updateActiveLink(page) {
        sidebarLinks.forEach(link => {
            link.classList.toggle('active', link.dataset.ajax === page);
        });
    }
	
    // Met à jour le titre principal
    function updatePageTitle(page) {
        switch (page) {
            case 'compte':
                mainTitle.textContent = 'Mon Compte';
                break;
            case 'chauffeur':
                mainTitle.textContent = 'Chauffeur';
                break;
            case 'passager':
                mainTitle.textContent = 'Passager';
                break;
            default:
                mainTitle.textContent = 'Mon Compte';
        }
    }

    // Gestion des clics sur la sidebar (PC)
    sidebarLinks.forEach(link => {
        link.addEventListener('click', e => {
            e.preventDefault();
            loadContent(link.dataset.ajax);
        });
    });

    // Gestion de la liste déroulante (mobile/tablette)
    if (selectMenu) {
        selectMenu.addEventListener('change', e => {
            const page = e.target.value;
            if (page) loadContent(page);
        });
    }

    // Chargement initial : section "compte"
    loadContent('compte');
	updatePageTitle('compte');
	contentZone.classList.add('visible');
});
