<?php
declare(strict_types=1);



//
//	Page de connexion
//	Chemin : /frontend/login.php
//



session_start();
if (isset($_SESSION['user_id'])) {
	header('Location: index.php');
	exit;
}

$chemin_racine = '../';



//
//	Fichiers additionnels
//
require_once('includes/pied-de-page.php');
require_once('includes/javamess.php');
require_once('includes/en-tete.php');



//
// Génération du jeton CSRF si non existant
//
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
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
	<link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&family=Lora:ital,wght@0,400..700;1,400..700&display=swap" rel="stylesheet">

	<!-- Bootstrap CSS -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

	<!-- Feuille de style locale -->
	<link rel="stylesheet" href="css/style.css">

	<!-- Bootstrap JS -->
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>

	<!-- JS pour AJAX -->
	<script src="js/login.js" defer></script>
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
				<li class="breadcrumb-item active" aria-current="page">Connexion</li>
			</ol>
		</nav>
	</div>

	<main class="container">
		<h1 class="mb-4">Connexion</h1>

		<?php
		// Gestion des messages sans JS
		$successMsg = $_GET['success'] ?? '';
		$errorMsg   = $_GET['error']   ?? '';
		$hasSuccess = !empty($successMsg);
		$hasError   = !empty($errorMsg);
		?>
		
		<div id="form-message" 
			 class="alert alert-dismissible fade show <?= $hasSuccess ? 'alert-success' : ($hasError ? 'alert-danger' : 'd-none') ?>" 
			 role="alert">
			<span id="form-message-text">
				<?= $hasSuccess ? htmlspecialchars($successMsg, ENT_QUOTES, 'UTF-8') : '' ?>
				<?= $hasError   ? htmlspecialchars($errorMsg,   ENT_QUOTES, 'UTF-8') : '' ?>
			</span>
			<button type="button" class="btn-close pb-2" data-bs-dismiss="alert" aria-label="Fermer"></button>
		</div>
		
		<div class="card">
			<div class="card-body">
				<form id="form-login" method="post" action="../backend/login_traitement.php">
					<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
					<input type="hidden" name="next" value="<?= htmlspecialchars($_GET['next'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
					<div class="mb-3">
						<label for="email" class="form-label">Adresse email</label>
						<input type="email" class="form-control" id="email" name="email" required>
					</div>
					<div class="mb-3">
						<label for="mot_de_passe" class="form-label">Mot de passe</label>
						<input type="password" class="form-control" id="mot_de_passe" name="mot_de_passe" required>
					</div>
					<button id="validation" type="submit" class="btn btn-primary">Se connecter</button>
				</form>
			</div>
		</div>		
	</main>
	
	<?php pied_de_page($chemin_racine); ?>
</body>
</html>
