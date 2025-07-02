<?php
//
//  Recherche de covoiturages ( Traitement )
//  /backend/recherche-covoiturages.php
//

header('Content-Type: application/json');

require_once '../config/db.php';
$response = ['success' => false, 'message' => '', 'resultats' => []];

try {
    $depart   = strtoupper(trim($_POST['depart'] ?? ''));
    $arrivee  = strtoupper(trim($_POST['arrivee'] ?? ''));
    $date     = trim($_POST['date']   ?? '');

    if ($depart === '' || $arrivee === '' || $date === '') {
        throw new Exception("Tous les champs sont requis.");
    }

    if (!preg_match("/^[\p{L}\s'\-]{2,}\$/u", $depart)) {
        throw new Exception("Ville de départ invalide.");
    }
    if (!preg_match("/^[\p{L}\s'\-]{2,}\$/u", $arrivee)) {
        throw new Exception("Ville d’arrivée invalide.");
    }

    $dateObj = DateTime::createFromFormat('Y-m-d', $date);
    if (!$dateObj || $dateObj->format('Y-m-d') !== $date) {
        throw new Exception("Date invalide. Format attendu : JJ/MM/AAAA.");
    }

    $today = new DateTime('today');
    if ($dateObj < $today) {
        throw new Exception("La date de départ ne peut pas être antérieure à aujourd’hui.");
    }

    // Requête enrichie avec pseudo, énergie, et note moyenne via sous-requête
    $stmt = $pdo->prepare(
        "SELECT t.*, 
                u.pseudo,
                v.energie,
                (
                    SELECT ROUND(AVG(a.note), 1)
                    FROM participations pa
                    JOIN avis a ON a.participation_id = pa.id
                    WHERE pa.trajet_id = t.id
                ) AS note_moyenne
         FROM trajets t
         JOIN utilisateurs u ON t.chauffeur_id = u.id
         JOIN vehicules v ON t.vehicule_id = v.id
         WHERE UPPER(TRIM(SUBSTRING_INDEX(t.adresse_depart, ' ', -1))) = :ville_depart
           AND UPPER(TRIM(SUBSTRING_INDEX(t.adresse_arrivee, ' ', -1))) = :ville_arrivee
           AND DATE(t.date_depart) = :date
           AND t.nb_places_restantes >= 1
           AND t.statut = 'en_cours'"
    );

    $stmt->execute([
        'ville_depart'  => $depart,
        'ville_arrivee' => $arrivee,
        'date'          => $date
    ]);

    $resultats = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($resultats)) {
        // Recherche d'alternatives à une autre date
        $altStmt = $pdo->prepare(
            "SELECT t.*, u.pseudo, v.energie,
                    (
                        SELECT ROUND(AVG(a.note), 1)
                        FROM participations pa
                        JOIN avis a ON a.participation_id = pa.id
                        WHERE pa.trajet_id = t.id
                    ) AS note_moyenne
             FROM trajets t
             JOIN utilisateurs u ON t.chauffeur_id = u.id
             JOIN vehicules v ON t.vehicule_id = v.id
             WHERE UPPER(TRIM(SUBSTRING_INDEX(t.adresse_depart, ' ', -1))) = :ville_depart
               AND UPPER(TRIM(SUBSTRING_INDEX(t.adresse_arrivee, ' ', -1))) = :ville_arrivee
               AND t.statut = 'en_cours'
             ORDER BY ABS(DATEDIFF(DATE(t.date_depart), :date)) ASC
             LIMIT 3"
        );

        $altStmt->execute([
            'ville_depart'  => $depart,
            'ville_arrivee' => $arrivee,
            'date'          => $date
        ]);

        $resultats = $altStmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($resultats as &$alt) {
            $alt['voyage_ecologique'] = ($alt['energie'] === 'electrique');
            $alt['alternative'] = true;
        }
    } else {
        foreach ($resultats as &$trajet) {
            $trajet['voyage_ecologique'] = ($trajet['energie'] === 'electrique');
            $trajet['alternative'] = false;
        }
    }

    $response['success'] = true;
    $response['resultats'] = $resultats;

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
?>
