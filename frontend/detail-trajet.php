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
    exit('ID de trajet invalide.');
}

$trajet_id = (int) $_GET['id'];
$depart = $_GET['depart']  ?? '';
$arrivee = $_GET['arrivee'] ?? '';
$date = $_GET['date']    ?? '';



// Requête SQL pour charger les infos du trajet + chauffeur + véhicule
$sql = "
SELECT 
    t.*,
    u.pseudo,
    u.photo,
    v.marque,
    v.modele,
    v.energie,
    v.fumeur,
    v.animaux,
    v.preferences,
    (
        SELECT ROUND(AVG(a.note), 1)
        FROM participations p
        JOIN avis a ON a.participation_id = p.id
        WHERE p.trajet_id = t.id
          AND a.valide = 1
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
    exit('Trajet introuvable.');
}



// Récupération des crédits utilisateur si connecté
$credits_utilisateur = null;
if ($is_connected) {
    $stmt_credit = $pdo->prepare("SELECT credits FROM utilisateurs WHERE id = :id");
    $stmt_credit->execute([':id' => $_SESSION['user_id']]);
    $credits_utilisateur = (int) $stmt_credit->fetchColumn();
}



// Récupération des avis (validés)
$avis_stmt = $pdo->prepare("
    SELECT a.note, a.commentaire
    FROM avis a 
    JOIN participations p ON a.participation_id = p.id
    WHERE p.trajet_id = :id AND a.valide = 1
");
$avis_stmt->execute([':id' => $trajet_id]);
$avis_list = $avis_stmt->fetchAll(PDO::FETCH_ASSOC);



// Construction de l'URL actuelle pour redirection après login/signup
$current_url = urlencode($_SERVER['REQUEST_URI']);



// Reconstruction propre du lien “retour”
$backParams = [
    'depart'   => $depart,
    'arrivee'  => $arrivee,
    'date'     => $date,
];
if (isset($_GET['prix']))    { $backParams['prix']    = $_GET['prix']; }
if (isset($_GET['note']))    { $backParams['note']    = $_GET['note']; }
if (isset($_GET['duree_h'])) { $backParams['duree_h'] = $_GET['duree_h']; }
if (isset($_GET['duree_m'])) { $backParams['duree_m'] = $_GET['duree_m']; }
if (isset($_GET['ecolo']))   { $backParams['ecolo']   = '1'; }

$backQuery = http_build_query($backParams, arg_separator: '&', encoding_type: PHP_QUERY_RFC3986);



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
    <title>EcoRide - Détail du trajet</title>
    
    <!-- Chargement complet des familles Roboto et Lora via Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
      href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&family=Lora:ital,wght@0,400..700;1,400..700&display=swap"
      rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Feuille de style locale -->
    <link rel="stylesheet" href="css/style.css">
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" defer></script>
	
	<!-- JS pour AJAX -->
	<script src="js/detail-trajet.js" defer></script>
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
                <li class="breadcrumb-item">
                    <a href="covoiturages.php?<?= htmlspecialchars($backQuery, ENT_QUOTES, 'UTF-8') ?>">Covoiturages</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Détail du trajet</li>
            </ol>
        </nav>

        <h1 class="mb-4">Détail du trajet</h1>
		
		<!-- Gestion des erreurs ( participation ) -->
		<?php if (isset($_GET['erreur'])) : ?>
		<div class="alert alert-danger text-center">
		<?php
			switch ($_GET['erreur']) {
				case 'credits':
					echo "❌ Vous n'avez pas suffisamment de crédits pour ce trajet.";
					echo "<br><a href=\"covoiturages.php?" . htmlspecialchars($backQuery, ENT_QUOTES, 'UTF-8') . "\" class=\"btn btn-sm btn-secondary mt-2\">← Retour aux résultats</a>";
					break;

				case 'places':
					echo "❌ Il ne reste plus assez de places disponibles pour ce trajet.";
					echo "<br><a href=\"covoiturages.php?" . htmlspecialchars($backQuery, ENT_QUOTES, 'UTF-8') . "\" class=\"btn btn-sm btn-secondary mt-2\">← Retour aux résultats</a>";
					break;

				case 'deja':
					echo "⚠️ Vous êtes déjà inscrit à ce trajet.";
					echo "<br><a href=\"covoiturages.php?" . htmlspecialchars($backQuery, ENT_QUOTES, 'UTF-8') . "\" class=\"btn btn-sm btn-secondary mt-2\">← Retour aux résultats</a>";
					break;

				case 'nonco':
					echo "🔑 Vous devez être connecté pour participer à ce trajet.";
					echo "<br><a href=\"login.php?next=" . $current_url . "\" class=\"btn btn-sm btn-primary mt-2\">Se connecter</a> ";
					echo "<a href=\"signup.php?next=" . $current_url . "\" class=\"btn btn-sm btn-outline-primary mt-2\">Créer un compte</a>";
					break;

				case 'trajet':
					echo "❌ Le trajet demandé est introuvable ou invalide.";
					echo "<br><a href=\"covoiturages.php\" class=\"btn btn-sm btn-secondary mt-2\">← Retour aux covoiturages</a>";
					break;

				case 'chauffeur':
					echo "🚫 Vous ne pouvez pas vous inscrire à votre propre trajet.";
					echo "<br><a href=\"covoiturages.php?" . htmlspecialchars($backQuery, ENT_QUOTES, 'UTF-8') . "\" class=\"btn btn-sm btn-secondary mt-2\">← Retour aux résultats</a>";
					break;

				case 'csrf':
					echo "⚠️ Sécurité : votre session a expiré, veuillez "
						. "<a href=\"detail-trajet.php?id=" 
						. htmlspecialchars($trajet_id, ENT_QUOTES, 'UTF-8') 
						. "\">réessayer</a>.";
					break;

				case 'exception':
					echo "❌ Une erreur technique est survenue. Merci de réessayer plus tard.";
					echo "<br><a href=\"covoiturages.php\" class=\"btn btn-sm btn-secondary mt-2\">← Retour aux covoiturages</a>";
					break;

				default:
					echo "❌ Une erreur inattendue est survenue.";
					echo "<br><a href=\"covoiturages.php\" class=\"btn btn-sm btn-secondary mt-2\">← Retour aux covoiturages</a>";
					break;
			}
		?>
		</div>
		<?php endif; ?>

		<?php if (isset($_GET['succes']) && $_GET['succes'] === 'ok') : ?>
		<div class="alert alert-success text-center">
			Vous êtes inscrit à ce trajet ! 🎉
		</div>
		<?php endif; ?>

		<!-- Détail du trajet -->
        <div class="card mb-4">
            <div class="card-header">Résumé</div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 d-flex flex-column justify-content-center align-items-center text-center h100">
                        <img src="img/photos/<?= htmlspecialchars(!empty($trajet['photo']) ? $trajet['photo'] : 'defaut.png', ENT_QUOTES, 'UTF-8') ?>" alt="Photo du chauffeur" class="img-fluid rounded-circle mb-2" width="120">
                        <h5><?= htmlspecialchars($trajet['pseudo'] ?? '', ENT_QUOTES, 'UTF-8') ?></h5>
                        <?php if (!is_null($trajet['note_moyenne'])) : ?>
                            <p><?= htmlspecialchars((string)$trajet['note_moyenne'], ENT_QUOTES, 'UTF-8') ?> ⭐</p>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-9">
                        <p><strong>Départ :</strong> <?= htmlspecialchars($trajet['adresse_depart'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
                        <p><strong>Arrivée :</strong> <?= htmlspecialchars($trajet['adresse_arrivee'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
                        <p><strong>Date :</strong> <?= (new DateTime($trajet['date_depart']))->format('d/m/Y H:i') ?></p>
                        <p><strong>Durée :</strong> <?= htmlspecialchars($trajet['duree'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
                        <p><strong>Prix :</strong> <?= htmlspecialchars((string)$trajet['prix'], ENT_QUOTES, 'UTF-8') ?> crédits</p>
                        <p><strong>Places restantes :</strong> 
                            <?= htmlspecialchars((string)$trajet['nb_places_restantes'], ENT_QUOTES, 'UTF-8') ?> / 
                            <?= htmlspecialchars((string)$trajet['nb_places_total'], ENT_QUOTES, 'UTF-8') ?>
                        </p>
                        <p><strong>Écologie :</strong> 
                            <?= ($trajet['energie'] ?? '') === 'électrique' ? '🌿 Électrique (Voyage écologique)' : htmlspecialchars($trajet['energie'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">Véhicule</div>
            <div class="card-body">
                <p><strong>Modèle :</strong> <?= htmlspecialchars($trajet['modele'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
                <p><strong>Marque :</strong> <?= htmlspecialchars($trajet['marque'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
                <p><strong>Énergie :</strong> <?= htmlspecialchars($trajet['energie'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">Préférences du conducteur</div>
            <div class="card-body">
                <p><?= !empty($trajet['fumeur']) ? '✅ Accepte les fumeurs' : '🚭 Non fumeur' ?></p>
                <p><?= !empty($trajet['animaux']) ? '✅ Accepte les animaux' : '❌ N\'accepte pas les animaux' ?></p>
                <?php if (!empty($trajet['preferences'])) : ?>
                    <p><strong>Autres :</strong> <?= nl2br(htmlspecialchars($trajet['preferences'], ENT_QUOTES, 'UTF-8')) ?></p>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($avis_list) : ?>
            <div class="card mb-4">
                <div class="card-header">Avis des passagers</div>
                <div class="card-body">
                    <?php foreach ($avis_list as $avis) : ?>
                        <p><strong>Note :</strong> <?= htmlspecialchars((string)$avis['note'], ENT_QUOTES, 'UTF-8') ?> ⭐</p>
                        <?php if (!empty($avis['commentaire'])) : ?>
                            <p><?= nl2br(htmlspecialchars($avis['commentaire'], ENT_QUOTES, 'UTF-8')) ?></p>
                        <?php endif; ?>
                        <hr>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="text-center">
            <a href="covoiturages.php?<?= htmlspecialchars($backQuery, ENT_QUOTES, 'UTF-8') ?>" class="btn btn-primary">← Retour aux résultats</a>
            <?php if (!$is_connected): ?>
                <a href="login.php?next=<?= $current_url ?>" class="btn btn-warning">Se connecter</a>
                <a href="signup.php?next=<?= $current_url ?>" class="btn btn-warning">Créer un compte</a>
            <?php else: ?>
                <?php if ((int)$trajet['nb_places_restantes'] <= 0): ?>
                    <button class="btn btn-primary" disabled>Plus aucune place disponible</button>
                <?php elseif ($credits_utilisateur < (int)$trajet['prix']): ?>
                    <button class="btn btn-primary" disabled>Crédits insuffisants</button>
                    <p class="text-danger mt-2">
                        Il vous manque 
                        <?= htmlspecialchars((string)((int)$trajet['prix'] - $credits_utilisateur), ENT_QUOTES, 'UTF-8') ?> 
                        crédits pour ce trajet.
                    </p>
                <?php else: ?>
					<form id="form-participer" method="POST" action="../backend/participer.php" class="d-inline">
						<input type="hidden" name="trajet_id" value="<?= htmlspecialchars((string)$trajet['id'], ENT_QUOTES, 'UTF-8') ?>">
						<input type="hidden" name="next" value="<?= $current_url ?>">
						<input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
						<button type="button" id="btn-participer" class="btn btn-primary">
							Participer à ce trajet
						</button>
					</form>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
	
	<!-- Confirmation de participation -->
	<div class="modal fade" id="confirmParticipationModal" tabindex="-1" aria-labelledby="confirmParticipationLabel" aria-hidden="true">
	  <div class="modal-dialog">
		<div class="modal-content">
		  <div class="modal-header">
			<h5 class="modal-title" id="confirmParticipationLabel">Confirmer votre participation</h5>
			<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
		  </div>
		  <div class="modal-body">
			Voulez-vous participer à ce trajet ? Vos crédits seront débités immédiatement.
		  </div>
		  <div class="modal-footer">
			<button type="button" class="btn btn-warning" data-bs-dismiss="modal">Annuler</button>
			<button type="button" class="btn btn-primary" id="confirm-participation">Oui, je confirme</button>
		  </div>
		</div>
	  </div>
	</div>

    <?php pied_de_page($chemin_racine); ?>
</body>
</html>
