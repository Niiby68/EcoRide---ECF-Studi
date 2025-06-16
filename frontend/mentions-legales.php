<?php
//
//  Page Mentions légales
//  Chemin : /frontend/mentions-legales.php
//



session_start();
$is_connected = isset($_SESSION['user_id']);
?>



<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EcoRide - Mentions légales</title>
	
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
		<div class="alert alert-warning text-center my-4" role="alert">
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
	</header>

	<!-- Fil d’Ariane -->
	<div class="container">
		<nav aria-label="breadcrumb">
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="index.php">Accueil</a></li>
				<li class="breadcrumb-item active" aria-current="page">Mentions légales</li>
			</ol>
		</nav>
	</div>

	<main class="container">
		<h1 class="mb-4">Mentions légales</h1>
		
		<div class="card">
			<p><strong>Éditeur du site :</strong><br>
			EcoRide<br>
			<a href="mailto:contact@ecoride.com">contact@ecoride.com</a><br>
			RCS / SIRET : [à compléter]</p>

			<p><strong>Responsable de la publication :</strong><br>
			[Nom du responsable]</p>

			<p><strong>Hébergement :</strong><br>
			[Nom de l’hébergeur]<br>
			[Adresse de l’hébergeur]<br>
			Téléphone : [à compléter]</p>

			<p><strong>Propriété intellectuelle :</strong><br>
			Tous les contenus présents sur ce site sont la propriété d’EcoRide, sauf mention contraire. Toute reproduction, représentation ou diffusion, même partielle, est interdite sans autorisation écrite préalable.</p>

			<p><strong>Protection des données personnelles :</strong><br>
			Aucune donnée personnelle n’est collectée sans votre consentement. Vous pouvez demander la suppression de vos données à tout moment par e-mail à <a href="mailto:contact@ecoride.com">contact@ecoride.com</a>.</p>
		</div>
	</main>

	<footer class="text-center mt-5 mb-3">
		<p>&copy; 2025 EcoRide &nbsp;&mdash;&nbsp; <a href="mailto:contact@ecoride.com">Contacts</a> &nbsp;&mdash;&nbsp; <a href="mentions-legales.php">Mentions légales</a></p>
	</footer>
</body>
</html>
