<?php
//
//	Formulaire de Connexion
//
//	Chemin : /frontend/login.php
//



session_start();
if (isset($_SESSION['user_id'])) {
	header('Location: index.php');
	exit;
}
?>



<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>EcoRide - Connexion</title>
	
	<!-- Chargement complet des familles Roboto et Lora via Google Fonts -->
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?
		family=Roboto:ital,wght@0,100..900;1,100..900&
		family=Lora:ital,wght@0,400..700;1,400..700&
		display=swap"
	rel="stylesheet">

	<!-- Bootstrap CSS -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

	<!-- Feuille de style locale -->
	<link rel="stylesheet" href="css/style.css">

	<!-- Bootstrap JS -->
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" defer></script>

	<!-- JS pour AJAX -->
	<script src="js/login.js" defer></script>
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
	
	<?php 
	if (isset($_GET['success'])) {
		echo '<div class="alert alert-success text-center m-3">';
		echo 'Inscription réussie ! Vous pouvez maintenant vous connecter.';
		echo '</div>';
	}
	?>
	
	<header class="container mt-3">
		<div class="container d-flex align-items-center py-3">
			<nav class="ms-auto">
				<ul class="nav">
				<img src="/assets/logo/logo.png" alt="Logo EcoRide" height="50">
					<li class="nav-item"><a href="index.php" class="nav-link">Accueil</a></li>
					<li class="nav-item"><a href="signup.php" class="nav-link active">Inscription</a></li>
					<li class="nav-item"><a href="login.php" class="nav-link">Connexion</a></li>
				</ul>
			</nav>
		</div>
	</header>

	<!-- Fil d’Ariane -->
	<div class="container">
		<nav aria-label="breadcrumb">
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="index.php">Accueil</a></li>
				<li class="breadcrumb-item active" aria-current="page">Connexion</li>
			</ol>
		</nav>
	</div>

	<main class="container">
		<h1 class="mb-4">Connexion à EcoRide</h1>

		<div id="form-message" class="alert d-none alert-dismissible fade show" role="alert">
			<span id="form-message-text"></span>
			<button type="button" class="btn-close pb-2" data-bs-dismiss="alert" aria-label="Fermer"></button>
		</div>

		<form id="form-login" method="post" action="../backend/login_traitement.php">
			<div class="mb-3">
				<label for="email" class="form-label">Adresse email</label>
				<input type="email" class="form-control" id="email" name="email" required>
			</div>

			<div class="mb-3">
				<label for="mot_de_passe" class="form-label">Mot de passe</label>
				<input type="password" class="form-control" id="mot_de_passe" name="mot_de_passe" required>
			</div>

			<button type="submit" class="btn btn-primary pb-2">Se connecter</button>
		</form>
	</main>

	<footer class="text-center mt-5 mb-3">
		<p>&copy; 2025 EcoRide - Tous droits réservés.</p>
	</footer>
</body>
</html>
