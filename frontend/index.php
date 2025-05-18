<?php
//
//  Page d'accueil
//  Chemin : /frontend/index.php
//



session_start();
$is_connected = isset($_SESSION['user_id']);
?>



<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EcoRide - Accueil</title>
	
    <!-- Google Fonts Roboto & Lora -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;400;700&family=Lora:wght@400;700&display=swap" rel="stylesheet">
	
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

                <div class="nav-logo d-flex d-lg-none align-items-center justify-content-center w-100">
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
        <div class="card">
            <div class="card-body">
                <h2 class="card-title titre-secondaire">La plateforme de covoiturage écoresponsable</h2>
                <p class="card-text">Inscrivez-vous, proposez ou réservez un trajet, et contribuez à un transport plus durable.</p>
                <?php if (!$is_connected): ?>
                  <a href="signup.php" class="btn btn-primary me-2">S'inscrire</a>
                  <a href="login.php" class="btn btn-primary">Se connecter</a>
                <?php else: ?>
                  <p class="text-success">Connecté · <strong><?= htmlspecialchars($_SESSION['pseudo']); ?></strong></p>
                <?php endif; ?>
            </div>
        </div>
    </main>
	
    <footer class="text-center mt-5 mb-3">
        <p>&copy; 2025 EcoRide - Tous droits réservés.</p>
    </footer>
</body>
</html>