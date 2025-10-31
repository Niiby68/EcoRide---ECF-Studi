<?php
declare(strict_types=1);



//
//  Espace client
//  Chemin : /frontend/espace-client.php
//
$chemin_racine = '../';



//
//	Initialisation de la session
//
require_once $chemin_racine . 'config/session_init.php';
$is_connected = isset($_SESSION['user_id']);



//
//  Fichiers additionnels
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
	<script src="js/espace-client.js" defer></script>
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
				<li class="breadcrumb-item active" aria-current="page">Espace Client</li>
			</ol>
		</nav>
	</div>
	
	<!-- Conteneur principal -->
    <main class="container espace-client mb-5">
        <h1 class="mb-4">Mon Compte</h1>

        <!-- Wrapper principal -->
        <div class="client-wrapper">

            <!-- Sidebar pour PC -->
            <aside class="client-sidebar">
                <ul>
                    <li><a href="#" data-ajax="compte" class="active">Mon Compte</a></li>
                    <li><a href="#" data-ajax="chauffeur">Chauffeur</a></li>
                    <li><a href="#" data-ajax="passager">Passager</a></li>
                </ul>
            </aside>

            <!-- Menu déroulant pour mobile et tablette -->
            <div class="client-select">
                <select id="nav-client" class="form-select">
                    <option value="compte" selected>Mon Compte</option>
                    <option value="chauffeur">Chauffeur</option>
                    <option value="passager">Passager</option>
                </select>
            </div>

            <!-- Zone de contenu -->
            <section id="contenu-dynamique" class="client-content"></section>
        </div>
    </main>
	
	<!-- Footer -->
	<?php pied_de_page($chemin_racine); ?>
</body>
</html>
