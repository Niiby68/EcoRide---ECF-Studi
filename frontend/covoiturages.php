<?php
//
//  Page des Covoiturages
//  Chemin : /frontend/covoiturage.php
//



session_start();
$chemin_racine = '../';
$is_connected = isset($_SESSION['user_id']);



//
//  Fichiers additionnels
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
    <title>EcoRide - Covoiturage</title>

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

	<!-- JS pour AJAX -->
	<script src="js/recherche-covoiturages.js" defer></script>
</head>

<body>
	<!-- Message d'activation du Javascript -->
    <?php javamess(); ?>
	
	<!-- Header -->
    <?php en_tete($chemin_racine, $is_connected); ?>

	<!-- Fil d’Ariane -->
	<div class="container">
		<nav aria-label="breadcrumb">
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="index.php">Accueil</a></li>
				<li class="breadcrumb-item active" aria-current="page">Covoiturages</li>
			</ol>
		</nav>
	</div>

    <main class="container">
        <h1 class="mb-4">Trouvez un trajet en covoiturage</h1>
		
		<!-- Barre de recherche -->
		<?php formulaire_trajet( $chemin_racine.'backend/recherche-covoiturages.php' ); ?>
		
		<div id="form-message" class="alert d-none alert-dismissible fade show" role="alert">
			<span id="form-message-text"></span>
			<button type="button" class="btn-close pb-2" data-bs-dismiss="alert" aria-label="Fermer"></button>
		</div>
		
		<div class="card">
			<div class="card-body text-center" id="reponse-trajet">
				<p class="text-muted">Aucun trajet n’a encore été recherché. Utilisez le formulaire ci-dessus pour lancer une recherche.</p>
				<img src="img/covoiturages/voiture.png" alt="Illustration voiture en attente d'un trajet" class="img-fluid mt-3 rounded my-3">
			</div>
		</div>	
    </main>
	
	<!-- Footer -->
	<?php pied_de_page($chemin_racine); ?>
</body>
</html>
