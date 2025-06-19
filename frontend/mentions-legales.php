<?php
//
//  Page Mentions légales
//  Chemin : /frontend/mentions-legales.php
//



session_start();
$chemin_racine = '../';
$is_connected = isset($_SESSION['user_id']);



//
//	Fichiers additionnels
//
require_once('includes/pied-de-page.php');
require_once('includes/javamess.php');
require_once('includes/en-tete.php');
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
	<!-- Message d'activation du Javascript -->
    <?php javamess(); ?>
	
	<!-- Header -->
    <?php en_tete($chemin_racine, $is_connected); ?>

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
	
	<?php pied_de_page($chemin_racine); ?>
</body>
</html>
