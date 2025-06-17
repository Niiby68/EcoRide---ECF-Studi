<?php
//
//  Page d'accueil
//  Chemin : /frontend/index.php
//



session_start();
$is_connected = isset($_SESSION['user_id']);



//
//	Fichiers additionnels
//
require_once( "includes/formulaire-trajet.php" );
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
    <noscript>
      <div class="container">
        <div class="alert alert-warning javamess" role="alert">
			<strong>Attention :</strong> JavaScript est désactivé dans votre navigateur.  
			Le site fonctionnera en mode simplifié, mais certaines fonctionnalités seront limitées.
        </div>
      </div>
    </noscript>
	
    <header class="menu">
        <div class="container cont-menu">
            <nav class="navbar header-menu navbar-expand-md navbar-light">
			
				<!-- Groupe gauche : logo -->
                <div class="nav-left d-none d-lg-flex">
                    <ul class="nav mb-0">
                        <li class="nav-item">
                            <a href="index.php">
                                <img src="/img/logo/logo.png" alt="EcoRide" height="40">
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="nav-logo d-flex d-lg-none align-items-center justify-content-center w-100 mt-3">
					<button class="navbar-toggler me-5"
							type="button"
							data-bs-toggle="collapse"
							data-bs-target="#ecoNavbar"
							aria-controls="ecoNavbar"
							aria-expanded="false"
							aria-label="Ouvrir le menu">
						<span class="navbar-toggler-icon"></span>
					</button>
                    <a href="index.php" class="mx-3">
                        <img src="/img/logo/logo.png" alt="EcoRide" height="40">
                    </a>
					<button class="navbar-toggler ms-5"
							type="button"
							data-bs-toggle="collapse"
							data-bs-target="#ecoNavbar"
							aria-controls="ecoNavbar"
							aria-expanded="false"
							aria-label="Ouvrir le menu">
						<span class="navbar-toggler-icon"></span>
					</button>
                </div>
				
                <!-- Groupe milieu : liens -->
				<div class="nav-mid collapse navbar-collapse justify-content-center" id="ecoNavbar">
					<ul class="nav mb-0">
                        <li class="nav-item">
                            <a href="index.php" class="btn btn-primary">Accueil</a>
                        </li>
						<li class="nav-item">
							<a href="covoiturages.php" class="btn btn-primary">Covoiturages</a>
						</li>
						<?php if (!$is_connected): ?>
                        <li class="nav-item">
                            <a href="signup.php" class="btn btn-primary">Inscription</a>
                        </li>
						<?php endif; ?>
                    </ul>
                </div>
				
                <!-- Groupe droite : Connexion / Déconnexion -->
                <div class="nav-right">
                    <ul class="nav">
                        <li class="nav-item">
                            <?php if (!$is_connected): ?>
                            <a href="login.php" class="btn btn-primary codeco">Connexion</a>
                            <?php else: ?>
                            <a href="/backend/logout.php" class="btn btn-primary codeco">Déconnexion</a>
                            <?php endif; ?>
                        </li>
                    </ul>
                </div>
            </nav>
        </div>
    </header>
	
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
                        EcoRide est une plateforme de covoiturage pensée pour faciliter les déplacements tout en réduisant notre impact sur l’environnement.
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
                        Ce projet a été conçu dans une logique de développement durable, en mettant l’humain et la planète au cœur de nos préoccupations.
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
	
	<footer class="text-center mt-5 mb-3">
		<p>&copy; 2025 EcoRide &nbsp;&mdash;&nbsp; <a href="mailto:contact@ecoride.com">Contacts</a> &nbsp;&mdash;&nbsp; <a href="mentions-legales.php">Mentions légales</a></p>
	</footer>
</body>
</html>
