<?php
declare(strict_types=1);



//
//  Page d'accueil
//  Chemin : /frontend/index.php
//



session_start();
$chemin_racine = '../';
$is_connected = isset($_SESSION['user_id']);



//
//	Fichiers additionnels
//
require_once('includes/formulaire-trajet.php');
require_once('includes/pied-de-page.php');
require_once('includes/javamess.php');
require_once('includes/en-tete.php');
?>



<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EcoRide - Accueil</title>
	
	<!-- Chargement complet des familles Roboto et Lora via Google Fonts -->
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&family=Lora:ital,wght@0,400..700;1,400..700&display=swap" rel="stylesheet">
	
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
	
    <!-- Feuille de style locale -->
    <link rel="stylesheet" href="css/style.css">
	
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" defer></script>
</head>

<body>
	<!-- Message d'activation du Javascript -->
    <?php javamess(); ?>
	
	<!-- Header -->
    <?php en_tete($chemin_racine, $is_connected); ?>
	
    <main class="container">
        <h1 class="titre-principal">Bienvenue sur EcoRide</h1>
		
		<!-- Barre de recherche -->
		<?php formulaire_trajet( 'covoiturages.php' ); ?>
		
		<!-- Contenu du site -->
        <div class="card">
            <section class="presentation d-flex flex-lg-row flex-column align-items-center justify-content-between">
                
                <!-- Texte -->
                <div class="text-content pe-lg-4">
                    <h2 class="mb-3">Le covoiturage écolo 🌿</h2>
                    <p>
                        EcoRide est une plateforme de covoiturage pensée pour faciliter les déplacements tout en réduisant notre impact sur l'environnement.
                        En mettant en relation des conducteurs et des passagers, nous encourageons une mobilité plus douce, plus responsable et plus économique.
                    </p>
                    <p>
                        Chaque trajet partagé permet de limiter les émissions de CO₂, de désengorger les routes et de créer du lien social entre les usagers.
                        Que vous soyez conducteur ou passager, EcoRide vous accompagne avec simplicité, efficacité et transparence.
                    </p>
					
					<!-- Image insérée ici pour mobile -->
					<img src="img/accueil/voiture.png"
						alt="Covoiturage écologique"
						class="img-fluid rounded my-3 d-lg-none mx-auto">
					
                    <p>
                        Ce projet a été conçu dans une logique de développement durable, en mettant l'humain et la planète au cœur de nos préoccupations.
                        Grâce à une interface intuitive et responsive, vous pouvez facilement proposer un trajet ou en rechercher un, où que vous soyez.
                    </p>
                    <p class="mb-0">
                        Ensemble, adoptons une nouvelle façon de voyager : plus écologique, plus solidaire… plus EcoRide. 🌱
                    </p>
                </div>

				<!-- Image visible uniquement sur desktop -->
				<img src="img/accueil/voiture.png"
					alt="Covoiturage écologique"
					class="img-fluid rounded mt-3 mt-lg-0 ms-lg-4 d-none d-lg-block">
            </section>
        </div>
    </main>
	
	<!-- Footer -->
	<?php pied_de_page($chemin_racine); ?>
</body>
</html>
