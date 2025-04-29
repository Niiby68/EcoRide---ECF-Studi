<!--
	Formulaire d'Inscription
	
	/frontend/signup.php
-->
<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Inscription - EcoRide</title>
	
	<!-- Feuille de sytle locale -->
	<link rel="stylesheet" href="css/style.css">

	<!-- Bootstrap CSS -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

	<!-- JS pour AJAX -->
	<script src="js/signup.js" defer></script>
</head>

<body>
	<header class="container mt-3">
		<nav>
			<ul class="nav">
				<li class="nav-item"><a href="index.html" class="nav-link">Accueil</a></li>
				<li class="nav-item"><a href="signup.php" class="nav-link">Inscription</a></li>
			</ul>
		</nav>
	</header>

	<!-- Fil d’Ariane -->
	<div class="container">
		<nav aria-label="breadcrumb">
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="index.html">Accueil</a></li>
				<li class="breadcrumb-item active" aria-current="page">Inscription</li>
			</ol>
		</nav>
	</div>

	<main class="container">
		<h1 class="mb-4">Inscription sur EcoRide</h1>
		<form id="form-inscription" method="post">
			<div class="mb-3">
				<label for="pseudo" class="form-label">Pseudo</label>
				<input type="text" class="form-control" id="pseudo" name="pseudo" required>
			</div>

			<div class="mb-3">
				<label for="email" class="form-label">Adresse email</label>
				<input type="email" class="form-control" id="email" name="email" required>
			</div>

			<div class="mb-3">
				<label for="mot_de_passe" class="form-label">Mot de passe</label>
				<input type="password" class="form-control" id="mot_de_passe" name="mot_de_passe" required>
			</div>

			<div class="mb-3">
				<label for="role" class="form-label">Rôle</label>
				<select class="form-select" id="role" name="role" required>
					<option value="passager">Passager</option>
					<option value="chauffeur">Chauffeur</option>
					<option value="les_deux">Les deux</option>
				</select>
			</div>

			<button type="submit" class="btn btn-primary">S'inscrire</button>
		</form>
	</main>

	<footer class="text-center mt-5 mb-3">
		<p>&copy; 2025 EcoRide - Tous droits réservés.</p>
	</footer>
</body>
</html>
