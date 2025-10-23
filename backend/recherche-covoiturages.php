<?php
declare(strict_types=1);



//
//  Recherche de covoiturages ( Traitement )
//  Chemin : /backend/recherche-covoiturages.php
//



session_start();



//
//  Fichiers additionnels
//
require_once '../config/db.php';
require_once '../frontend/includes/modif-duree.php';



//
// Vérifie si un utilisateur est connecté
//
$user_id = $_SESSION['user_id'] ?? null;



//
// Détection de l'AJAX
//
$is_ajax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';



//
// Si ce n'est pas une requête POST → on considère que c'est sans JS (GET)
//
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $is_ajax = false;
}



//
//  Réponse si le Javascript est activé ( variables POST reçus )
//
$response = ['success' => false, 'message' => '', 'resultats' => []];

try {
	$depart  = strtoupper(trim($_POST['depart'] ?? $_GET['depart'] ?? ''));
	$arrivee = strtoupper(trim($_POST['arrivee'] ?? $_GET['arrivee'] ?? ''));
	$date    = trim($_POST['date'] ?? $_GET['date'] ?? '');

    if ($depart === '' || $arrivee === '' || $date === '') {
        throw new Exception("Tous les champs sont requis.");
    }

    if (!preg_match("/^[\p{L}0-9\s'\-]+$/u", $depart)) {
        throw new Exception("Ville de départ invalide.");
    }
    if (!preg_match("/^[\p{L}0-9\s'\-]+$/u", $arrivee)) {
        throw new Exception("Ville d'arrivée invalide.");
    }

    $dateObj = DateTime::createFromFormat('Y-m-d', $date);
    if (!$dateObj || $dateObj->format('Y-m-d') !== $date) {
        throw new Exception("Veuillez entrer une date valide (exemple : 15/09/2025).");
    }

    $today = new DateTime('today');
    if ($dateObj < $today) {
        throw new Exception("La date de départ ne peut pas être antérieure à aujourd’hui.");
    }



    //
    // Recherche du trajet demandé
    //
    $sql = "
        SELECT t.*, t.duree, u.pseudo, v.energie,
               (
                   SELECT ROUND(AVG(a.note), 1)
                   FROM participations pa
                   JOIN avis a ON a.participation_id = pa.id
                   WHERE pa.trajet_id = t.id
               ) AS note_moyenne
               " . ($user_id ? ",
               EXISTS (
                   SELECT 1 FROM participations p
                   WHERE p.trajet_id = t.id
                   AND p.utilisateur_id = :user_id
               ) AS deja_participe" : "") . "
        FROM trajets t
        JOIN utilisateurs u ON t.chauffeur_id = u.id
        JOIN vehicules v ON t.vehicule_id = v.id
        WHERE UPPER(TRIM(SUBSTRING_INDEX(t.adresse_depart, ' ', -1))) = :ville_depart
          AND UPPER(TRIM(SUBSTRING_INDEX(t.adresse_arrivee, ' ', -1))) = :ville_arrivee
          AND DATE(t.date_depart) = :date
          AND t.nb_places_restantes >= 1
          AND t.statut IN ('à_venir', 'en_cours')
    ";

    $stmt = $pdo->prepare($sql);

    $params = [
        'ville_depart'  => $depart,
        'ville_arrivee' => $arrivee,
        'date'          => $date
    ];
    if ($user_id) {
        $params['user_id'] = $user_id;
    }

    $stmt->execute($params);
    $resultats = $stmt->fetchAll(PDO::FETCH_ASSOC);



    //
    // Trajet alternatif si aucun résultat
    //
    if (empty($resultats)) {
        $sqlAlt = "
            SELECT t.*, t.duree, u.pseudo, v.energie,
                   (
                       SELECT ROUND(AVG(a.note), 1)
                       FROM participations pa
                       JOIN avis a ON a.participation_id = pa.id
                       WHERE pa.trajet_id = t.id
                   ) AS note_moyenne
                   " . ($user_id ? ",
                   EXISTS (
                       SELECT 1 FROM participations p
                       WHERE p.trajet_id = t.id
                       AND p.utilisateur_id = :user_id
                   ) AS deja_participe" : "") . "
            FROM trajets t
            JOIN utilisateurs u ON t.chauffeur_id = u.id
            JOIN vehicules v ON t.vehicule_id = v.id
            WHERE UPPER(TRIM(SUBSTRING_INDEX(t.adresse_depart, ' ', -1))) = :ville_depart
              AND UPPER(TRIM(SUBSTRING_INDEX(t.adresse_arrivee, ' ', -1))) = :ville_arrivee
              AND t.statut IN ('à_venir', 'en_cours')
              AND DATE(t.date_depart) >= :date
              AND t.nb_places_restantes >= 1
            ORDER BY DATEDIFF(DATE(t.date_depart), :date) ASC
            LIMIT 3
        ";

        $altStmt = $pdo->prepare($sqlAlt);
        $altParams = [
            'ville_depart'  => $depart,
            'ville_arrivee' => $arrivee,
            'date'          => $date
        ];
        if ($user_id) {
            $altParams['user_id'] = $user_id;
        }
        $altStmt->execute($altParams);
        $resultats = $altStmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($resultats as &$alt) {
            $alt['voyage_ecologique'] = ($alt['energie'] === 'électrique');
            $alt['alternative'] = true;
			$alt['duree_formatee'] = formatDuree($alt['duree']);
        }
    } else {
        foreach ($resultats as &$trajet) {
            $trajet['voyage_ecologique'] = ($trajet['energie'] === 'électrique');
            $trajet['alternative'] = false;
			$trajet['duree_formatee'] = formatDuree($trajet['duree']);
        }
    }



	//
	// Stockage des réponses
	//
    $response['success'] = true;
    $response['resultats'] = $resultats;

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}



//
// Sortie
//
if ($is_ajax) {
    // Réponse pour AJAX (JS activé)
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode($response);
    exit;
} else {
    // Réponse pour HTML direct (JS désactivé)
    header('Content-Type: text/html; charset=UTF-8');

    if (!$response['success']) {
        echo "<div class='alert alert-danger text-center'>"
           . htmlspecialchars($response['message'], ENT_QUOTES, 'UTF-8')
           . "</div>";
    } elseif (empty($response['resultats'])) {
        echo "<div class='alert alert-warning text-center'>Aucun trajet trouvé pour votre recherche.</div>";
    } else {
        foreach ($response['resultats'] as $trajet) {
            ?>
			<div class="trajet-card card mb-3 <?= !empty($trajet['deja_participe']) ? 'participe' : '' ?>">
				<div class="card-body">
					<h5 class="card-title">
						Chauffeur : <?= htmlspecialchars($trajet['pseudo']) ?>
						<?php if (!empty($trajet['note_moyenne'])): ?>
							<span class="badge bg-success ms-2">
								⭐ <?= $trajet['note_moyenne'] ?>/5
							</span>
						<?php endif; ?>
					</h5>
                    <p class="card-text mb-1">
                        <strong>Départ :</strong> <?= htmlspecialchars($trajet['adresse_depart']) ?><br>
                        <strong>Arrivée :</strong> <?= htmlspecialchars($trajet['adresse_arrivee']) ?><br>
                        <strong>Date :</strong> <?= (new DateTime($trajet['date_depart']))->format('d/m/Y H:i') ?><br>
                        <strong>Durée :</strong> <?= htmlspecialchars($trajet['duree_formatee'], ENT_QUOTES, 'UTF-8') ?><br>
                        <strong>Énergie :</strong> <?= htmlspecialchars($trajet['energie']) ?>
                        <?php if ($trajet['energie'] === 'électrique'): ?> 🌿<?php endif; ?>
                    </p>
                    <?php if (!empty($trajet['alternative'])): ?>
                        <p class="text-muted"><em>Proposition alternative</em></p>
                    <?php endif; ?>
                </div>
            </div>
            <?php
        }
    }
}
?>
