<?php
declare(strict_types=1);



//
//	Page d'Inscription
//	Chemin : /frontend/signup.php
//
$chemin_racine = '../';



//
//	Initialisation de la session
//
require_once $chemin_racine . 'config/session_init.php';
if (isset($_SESSION['user_id'])) {
	header('Location: index.php');
	exit;
}



//
//	Fichiers additionnels
//
require_once('includes/pied-de-page.php');
require_once('includes/javamess.php');
require_once('includes/en-tete.php');



// Génération du jeton CSRF si non existant
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>



<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>EcoRide - Inscription</title>
	
	<!-- Chargement complet des familles Roboto et Lora via Google Fonts -->
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&family=Lora:ital,wght@0,400..700;1,400..700&display=swap" rel="stylesheet">

	<!-- Bootstrap CSS -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

	<!-- Feuille de style locale -->
	<link rel="stylesheet" href="css/style.css">

	<!-- Bootstrap JS (non bloquant) -->
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>

	<!-- JS pour AJAX -->
	<script src="js/signup.js" defer></script>
</head>

<body>
	<!-- Message d'activation du Javascript -->
    <?php javamess(); ?>
	
	<!-- Header -->
    <?php en_tete($chemin_racine); ?>

	<!-- Fil d'Ariane -->
	<div class="container">
		<nav aria-label="breadcrumb">
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="index.php">Accueil</a></li>
				<li class="breadcrumb-item active" aria-current="page">Inscription</li>
			</ol>
		</nav>
	</div>

	<main class="container">
		<h1 class="mb-4">Inscription</h1>
		
		<?php
		// Gestion du message si retour sans JS
		$errorMsg = $_GET['error'] ?? '';
		$hasError = !empty($errorMsg);
		?>
		
		<div id="form-message" 
			 class="alert alert-dismissible fade show <?= $hasError ? 'alert-danger' : 'd-none' ?>" 
			 role="alert">
			<span id="form-message-text"><?= $hasError ? htmlspecialchars($errorMsg, ENT_QUOTES, 'UTF-8') : '' ?></span>
			<button type="button" class="btn-close pb-2" data-bs-dismiss="alert" aria-label="Fermer"></button>
		</div>
		
		<div class="card">
			<div class="card-body">
				<form id="form-inscription" method="post" action="../backend/signup_traitement.php">
					<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
					<input type="hidden" name="next" value="<?= htmlspecialchars($_GET['next'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
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
					<?php
					// Génération du captcha de base
					$nb1 = rand(1, 9);
					$nb2 = rand(1, 9);
					$_SESSION['captcha_result'] = $nb1 + $nb2;
					?>

					<div class="mb-3">
					  <label for="captcha" class="form-label" id="captcha-label">
						Combien font <?php echo $nb1; ?> + <?php echo $nb2; ?> ?
					  </label>
					  <input type="number" class="form-control" id="captcha" name="captcha" required>
					</div>
					<button id="validation" type="submit" class="btn btn-primary">S'inscrire</button>
				</form>
			</div>
		</div>
	</main>
	
	<?php pied_de_page($chemin_racine); ?>
</body>
</html>
