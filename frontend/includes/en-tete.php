<?php
declare(strict_types=1);

if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) {
    http_response_code(403);
    exit('Accès interdit.');
}



//
//	En-tête des pages
//	Chemin : /frontend/includes/en-tete.php
//



function en_tete(string $chemin_racine, bool $is_connected = false): void {
	?>
    <header class="menu">
        <div class="container cont-menu">
            <nav class="navbar header-menu navbar-expand-md navbar-light">
			
			    <!-- Fallback CSS si JS est désactivé -->
                <noscript>
                    <style>
                        .collapse.navbar-collapse { display: block !important; visibility: visible !important; }
                    </style>
                </noscript>
			
				<!-- Groupe gauche : logo -->
                <div class="nav-left d-none d-lg-flex">
                    <ul class="nav mb-0">
                        <li class="nav-item">
                            <a href="index.php">
                                <img src="<?= htmlspecialchars($chemin_racine, ENT_QUOTES, 'UTF-8') ?>frontend/img/logo/logo.png" alt="EcoRide" height="40">
                            </a>
                        </li>
                    </ul>
                </div>

				<!-- Boutons d'ouverture et de fermeture du menu burger -->
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
                        <img src="<?= htmlspecialchars($chemin_racine, ENT_QUOTES, 'UTF-8') ?>frontend/img/logo/logo.png" alt="EcoRide" height="40">
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
                            <a href="<?= htmlspecialchars($chemin_racine, ENT_QUOTES, 'UTF-8') ?>frontend/index.php" class="btn btn-primary">Accueil</a>
                        </li>
						<li class="nav-item">
							<a href="<?= htmlspecialchars($chemin_racine, ENT_QUOTES, 'UTF-8') ?>frontend/covoiturages.php" class="btn btn-primary">Covoiturages</a>
						</li>
                    </ul>
                </div>
				
                <!-- Groupe droite : Connexion / Déconnexion -->
                <div class="nav-right">
                    <ul class="nav">
                        <li class="nav-item">
                            <?php if (!$is_connected): ?>
                            <a href="<?= htmlspecialchars($chemin_racine, ENT_QUOTES, 'UTF-8') ?>frontend/login.php" class="btn btn-primary codeco">Connexion</a>
                            <?php else: ?>
                            <a href="<?= htmlspecialchars($chemin_racine, ENT_QUOTES, 'UTF-8') ?>frontend/espace-client.php" class="btn btn-primary codeco">Espace Client</a>
                            <?php endif; ?>
                        </li>
                        <li class="nav-item">
							<?php if (!$is_connected): ?>
                            <a href="<?= htmlspecialchars($chemin_racine, ENT_QUOTES, 'UTF-8') ?>frontend/signup.php" class="btn btn-primary codeco">Inscription</a>
                            <?php else: ?>
                            <a href="<?= htmlspecialchars($chemin_racine, ENT_QUOTES, 'UTF-8') ?>backend/logout.php" class="btn btn-primary codeco">Déconnexion</a>
                            <?php endif; ?>
                        </li>
                    </ul>
                </div>
            </nav>
        </div>
    </header>
	<?php
}
?>
