<?php
//
//	Page d'accueil
//
//	Chemin : /frontend/index.php
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
	
	<header class="container mt-3">
		<nav>
			<ul class="nav">
				<li class="nav-item"><a href="index.php" class="nav-link active">Accueil</a></li>
				<?php 
				if (!$is_connected) {
					echo '<li class="nav-item"><a href="signup.php" class="nav-link">Inscription</a></li>';
					echo '<li class="nav-item"><a href="login.php" class="nav-link">Connexion</a></li>';
				} else {
					echo '<li class="nav-item"><a href="../backend/logout.php" class="nav-link">Déconnexion</a></li>';
				}
				?>
			</ul>
		</nav>
	</header>

	<main class="container">
		<h1 class="mb-4">Bienvenue sur EcoRide</h1>

		<div class="card">
			<div class="card-body">
				<h2 class="card-title">La plateforme de covoiturage écoresponsable</h2>
				<p class="card-text">Inscrivez-vous, proposez ou réservez un trajet, et contribuez à un transport plus durable.</p>
				<?php 
				if (!$is_connected) {
					echo '<a href="signup.php" class="btn btn-primary pb-2 me-2">S\'inscrire</a>';
					echo '<a href="login.php" class="btn btn-primary pb-2">Se connecter</a>';
				} else {
					echo '<p class="text-success">Vous êtes connecté en tant que <strong>' . htmlspecialchars($_SESSION['pseudo']) . '</strong>.</p>';
				}
				?>
			</div>
		</div>
	</main>

	<footer class="text-center mt-5 mb-3">
		<p>&copy; 2025 EcoRide - Tous droits réservés.</p>
	</footer>
</body>
</html>
