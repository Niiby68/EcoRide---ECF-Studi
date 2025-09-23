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
	
	<!-- Fallback CSS si JS désactivé -->
    <noscript>
		<style>
			#bloc-filtres { display: block !important; }
		</style>
	</noscript>
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
		<?php formulaire_trajet($chemin_racine.'frontend/covoiturage.php'); ?>
		
		<div id="form-message" class="alert d-none alert-dismissible fade show" role="alert">
			<span id="form-message-text"></span>
			<button type="button" class="btn-close pb-2" data-bs-dismiss="alert" aria-label="Fermer"></button>
		</div>
		
		<!-- Filtres dynamiques -->
		<?php
		$prix = $_GET['prix'] ?? '';
		$note = $_GET['note'] ?? '';
		$duree_h = $_GET['duree_h'] ?? '';
		$duree_m = $_GET['duree_m'] ?? '';
		$ecolo = (isset($_GET['ecolo']) && $_GET['ecolo'] === '1') ? 'checked' : '';
		?>
		
		<div class="filters card p-3 mb-4 d-none" id="bloc-filtres">
			<div class="row g-2 align-items-end">
				<div class="col-md-4">
					<label for="filtre-prix" class="form-label">Prix maximum (crédits)</label>
					<input type="number" id="filtre-prix" class="form-control" name="prix" min="0" step="1" value="<?= htmlspecialchars($prix) ?>">
				</div>
				
				<div class="col-md-4">
					<label class="form-label">Durée maximale</label>
					<div class="d-flex gap-2">
						<select id="filtre-duree-heures" class="form-select" name="duree_h">
							<option value="">Heures</option>
							<?php for ($i = 0; $i <= 24; $i++): ?>
								<option value="<?= $i ?>" <?= ($duree_h == $i) ? 'selected' : '' ?>><?= $i ?> h</option>
							<?php endfor; ?>
						</select>

						<select id="filtre-duree-minutes" class="form-select" name="duree_m">
							<option value="">Minutes</option>
							<?php for ($i = 0; $i <= 55; $i += 5): ?>
								<option value="<?= $i ?>" <?= ($duree_m == $i) ? 'selected' : '' ?>><?= $i ?> mn</option>
							<?php endfor; ?>
						</select>
					</div>
				</div>

				<div class="col-md-4">
					<label for="filtre-note" class="form-label">Note minimum du chauffeur</label>
					<input type="number" id="filtre-note" class="form-control" name="note" min="0" max="5" step="0.1" value="<?= htmlspecialchars($note) ?>">
				</div>
			</div>

			<div class="row g-2 mt-3">
				<div class="col-md-6">
					<div class="form-check">
						<input type="checkbox" class="form-check-input" id="filtre-ecolo" name="ecolo" <?= $ecolo ?>>
						<label class="form-check-label" for="filtre-ecolo">Voyages écologiques 🌿 (électriques uniquement)</label>
					</div>
				</div>
				<div class="col-md-6 text-md-end">
					<button id="btn-filtrer" class="btn btn-primary">Filtrer</button>
				</div>
			</div>
		</div>
		
		<!-- Résultats de la recherche -->
		<div class="card">
			<div class="card-body text-center" id="reponse-trajet">
				<?php if (!empty($_GET['depart']) && !empty($_GET['arrivee']) && !empty($_GET['date'])): ?>
					<?php
					// Inclusion directe du traitement si JS désactivé
					include($chemin_racine.'backend/recherche-covoiturages.php');
					?>
				<?php else: ?>
					<p class="text-muted">Aucun trajet n'a encore été recherché. Utilisez le formulaire ci-dessus pour lancer une recherche.</p>
					<img src="img/covoiturages/voiture.png" alt="Illustration voiture en attente d'un trajet" class="img-fluid mt-3 rounded my-3">
				<?php endif; ?>
			</div>
		</div>	
    </main>
	
	<!-- Footer -->
	<?php pied_de_page($chemin_racine); ?>
</body>
</html>
