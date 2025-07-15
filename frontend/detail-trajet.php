<?php
//
//  Page de Détail d'un Trajet
//  Chemin : /frontend/detail-trajet.php
//



session_start();
$chemin_racine = '../';
$is_connected = isset($_SESSION['user_id']);



//
//  Fichiers additionnels
//
require_once('../config/db.php');
require_once('includes/en-tete.php');
require_once('includes/pied-de-page.php');
require_once('includes/javamess.php');



// Sécurisation de l'ID
if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) {
    http_response_code(400);
    exit("ID de trajet invalide.");
}

$trajet_id = (int) $_GET['id'];
$depart = $_GET['depart']  ?? '';
$arrivee = $_GET['arrivee'] ?? '';
$date = $_GET['date']    ?? '';

// Requête SQL pour charger les infos du trajet + chauffeur + véhicule
$sql = "
SELECT t.*, u.pseudo, u.photo, v.marque, v.modele, v.energie, v.fumeur, v.animaux, v.preferences,
       (
            SELECT ROUND(AVG(a.note), 1)
            FROM participations p
            JOIN avis a ON a.participation_id = p.id
            WHERE p.trajet_id = t.id
       ) AS note_moyenne
FROM trajets t
JOIN utilisateurs u ON u.id = t.chauffeur_id
JOIN vehicules v ON v.id = t.vehicule_id
WHERE t.id = :id
LIMIT 1";

$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $trajet_id]);
$trajet = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$trajet) {
    http_response_code(404);
    exit("Trajet introuvable.");
}

// Récupération des avis
$avis_stmt = $pdo->prepare("SELECT a.note, a.commentaire FROM avis a JOIN participations p ON a.participation_id = p.id WHERE p.trajet_id = :id AND a.valide = 1");
$avis_stmt->execute([':id' => $trajet_id]);
$avis_list = $avis_stmt->fetchAll(PDO::FETCH_ASSOC);
?>



<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EcoRide - Détail du trajet</title>
	
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

	<div class="container">
		<nav aria-label="breadcrumb">
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="index.php">Accueil</a></li>
				<li class="breadcrumb-item"><a href="covoiturages.php?depart=<?= urlencode($depart) ?>&arrivee=<?= urlencode($arrivee) ?>&date=<?= urlencode($date) ?>">Covoiturages</a></li>
				<li class="breadcrumb-item active" aria-current="page">Détail du trajet</li>
			</ol>
		</nav>

		<h1 class="mb-4">Détail du trajet</h1>

		<div class="card mb-4">
			<div class="card-header">Résumé</div>
			<div class="card-body">
				<div class="row">
					<div class="col-md-3 text-center">
						<img src="img/photos/<?= htmlspecialchars($trajet['photo']) ?>" alt="Photo du chauffeur" class="img-fluid rounded-circle mb-2" width="120">
						<h5><?= htmlspecialchars($trajet['pseudo']) ?></h5>
						<?php if ($trajet['note_moyenne']) : ?>
							<p><?= htmlspecialchars($trajet['note_moyenne']) ?> ⭐</p>
						<?php endif; ?>
					</div>
					<div class="col-md-9">
						<p><strong>Départ :</strong> <?= htmlspecialchars($trajet['adresse_depart']) ?></p>
						<p><strong>Arrivée :</strong> <?= htmlspecialchars($trajet['adresse_arrivee']) ?></p>
						<p><strong>Date :</strong> <?= (new DateTime($trajet['date_depart']))->format('d/m/Y H:i') ?></p>
						<p><strong>Durée :</strong> <?= htmlspecialchars($trajet['duree']) ?></p>
						<p><strong>Prix :</strong> <?= htmlspecialchars($trajet['prix']) ?> crédits</p>
						<p><strong>Places restantes :</strong> <?= htmlspecialchars($trajet['nb_places_restantes']) ?> / <?= htmlspecialchars($trajet['nb_places_total']) ?></p>
						<p><strong>Écologie :</strong> <?= $trajet['energie'] === 'électrique' ? '🌿 Électrique (Voyage écologique)' : htmlspecialchars($trajet['energie']) ?></p>
					</div>
				</div>
			</div>
		</div>

		<div class="card mb-4">
			<div class="card-header">Véhicule</div>
			<div class="card-body">
				<p><strong>Modèle :</strong> <?= htmlspecialchars($trajet['modele']) ?></p>
				<p><strong>Marque :</strong> <?= htmlspecialchars($trajet['marque']) ?></p>
				<p><strong>Énergie :</strong> <?= htmlspecialchars($trajet['energie']) ?></p>
			</div>
		</div>

		<div class="card mb-4">
			<div class="card-header">Préférences du conducteur</div>
			<div class="card-body">
				<p><?= $trajet['fumeur'] ? '✅ Accepte les fumeurs' : '🚭 Non fumeur' ?></p>
				<p><?= $trajet['animaux'] ? '✅ Accepte les animaux' : '❌ N\'accepte pas les animaux' ?></p>
				<?php if (!empty($trajet['preferences'])) : ?>
					<p><strong>Autres :</strong> <?= nl2br(htmlspecialchars($trajet['preferences'])) ?></p>
				<?php endif; ?>
			</div>
		</div>

		<?php if ($avis_list) : ?>
			<div class="card mb-4">
				<div class="card-header">Avis des passagers</div>
				<div class="card-body">
					<?php foreach ($avis_list as $avis) : ?>
						<p><strong>Note :</strong> <?= htmlspecialchars($avis['note']) ?> ⭐</p>
						<?php if (!empty($avis['commentaire'])) : ?>
							<p><?= nl2br(htmlspecialchars($avis['commentaire'])) ?></p>
						<?php endif; ?>
						<hr>
					<?php endforeach; ?>
				</div>
			</div>
		<?php endif; ?>

		<div class="text-center">
			<a href="covoiturages.php?depart=<?= urlencode($depart) ?>&arrivee=<?= urlencode($arrivee) ?>&date=<?= urlencode($date) ?>" class="btn btn-primary">← Retour aux résultats</a>
			<a href="#" class="btn btn-primary disabled">Participer à ce trajet</a>
		</div>
	</div>

	<?php pied_de_page($chemin_racine); ?>
</body>
</html>
