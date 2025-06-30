<?php
//
//  Recherche de covoiturages ( Traitement )
//  /backend/recherche-covoiturages.php
//



header('Content-Type: application/json');



// Chargement de la configuration BDD
require_once '../config/db.php';



$response = ['success' => false, 'message' => '', 'resultats' => []];

try {
    // Récupération des données depuis le formulaire
    $depart   = strtoupper(trim($_POST['depart'] ?? ''));
    $arrivee  = strtoupper(trim($_POST['arrivee'] ?? ''));
    $date     = trim($_POST['date']   ?? '');

    // Vérification de la présence des champs
    if ($depart === '' || $arrivee === '' || $date === '') {
        throw new Exception("Tous les champs sont requis.");
    }

    // Validation des noms de ville (lettres, accents, tirets, apostrophes, espaces)
    if (!preg_match("/^[\p{L}\s'\-]{2,}\$/u", $depart)) {
        throw new Exception("Ville de départ invalide.");
    }
    if (!preg_match("/^[\p{L}\s'\-]{2,}\$/u", $arrivee)) {
        throw new Exception("Ville d’arrivée invalide.");
    }

    // Validation du format de la date (aaaa-mm-jj attendu car <input type="date">)
    $dateObj = DateTime::createFromFormat('Y-m-d', $date);
    if (!$dateObj || $dateObj->format('Y-m-d') !== $date) {
        throw new Exception("Date invalide. Format attendu : JJ/MM/AAAA.");
    }

    // Vérification que la date n'est pas antérieure à aujourd'hui
    $today = new DateTime('today');
    if ($dateObj < $today) {
        throw new Exception("La date de départ ne peut pas être antérieure à aujourd’hui.");
    }

    // Requête préparée avec extraction de la ville réelle depuis l'adresse (ville après code postal)
    $stmt = $pdo->prepare(
        "SELECT * FROM trajets
         WHERE UPPER(TRIM(SUBSTRING_INDEX(adresse_depart, ' ', -1))) = :ville_depart
           AND UPPER(TRIM(SUBSTRING_INDEX(adresse_arrivee, ' ', -1))) = :ville_arrivee
           AND DATE(date_depart) = :date"
    );

    $stmt->execute([
        'ville_depart'  => $depart,
        'ville_arrivee' => $arrivee,
        'date'          => $date
    ]);

    $resultats = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $response['success'] = true;
    $response['resultats'] = $resultats;

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

// Réponse JSON
echo json_encode($response);
?>
