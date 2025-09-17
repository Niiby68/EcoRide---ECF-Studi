<?php
//
//  Participation à un trajet
//  Chemin : /backend/participer.php
//



session_start();
require_once('../config/db.php');



// Vérification de la connexion
if (!isset($_SESSION['user_id'])) {
    $next = $_POST['next'] ?? '/frontend/covoiturages.php';
    $redirect = rawurldecode($next);
    if (!preg_match('#^/frontend/#', $redirect)) {
        $redirect = '/frontend/covoiturages.php';
    }
    $sep = (strpos($redirect, '?') !== false) ? '&' : '?';

    header("Location: {$redirect}{$sep}erreur=nonco");
    exit;
}

$user_id = (int) $_SESSION['user_id'];



//
// Vérification de la méthode
//
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    exit('Méthode non autorisée.');
}



//
// Vérification CSRF
//
if (empty($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    if (!empty($_POST['trajet_id'])) {
        $redirect = "/frontend/detail-trajet.php?id=" . (int)$_POST['trajet_id'];
    } else {
        $redirect = "/frontend/covoiturages.php";
    }
    header("Location: {$redirect}?erreur=csrf");
    exit;
}

$_SESSION['csrf_token'] = bin2hex(random_bytes(32));



// Reconstruction propre du redirect
$rawNext  = $_POST['next'] ?? '/frontend/covoiturages.php';
$redirect = rawurldecode($rawNext);
if (!preg_match('#^/frontend/#', $redirect)) {
    $redirect = '/frontend/covoiturages.php';
}
$sep = (strpos($redirect, '?') !== false) ? '&' : '?';



// Récupération et validation du trajet_id
$trajet_id = isset($_POST['trajet_id']) ? (int) $_POST['trajet_id'] : 0;
if (!$trajet_id) {
    header("Location: {$redirect}{$sep}erreur=trajet");
    exit;
}



// Vérifie si le trajet existe et récupère infos
$sql = "
    SELECT prix, nb_places_restantes, chauffeur_id
    FROM trajets
    WHERE id = :id AND statut IN ('à_venir', 'en_cours')
    LIMIT 1";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $trajet_id]);
$trajet = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$trajet) {
    header("Location: {$redirect}{$sep}erreur=trajet");
    exit;
}



// Empêcher le chauffeur de s’inscrire à son propre trajet
if ((int)$trajet['chauffeur_id'] === $user_id) {
    header("Location: {$redirect}{$sep}erreur=chauffeur");
    exit;
}



// Vérifie que l'utilisateur n'est pas déjà inscrit
$stmt = $pdo->prepare("SELECT COUNT(*) FROM participations WHERE utilisateur_id = :uid AND trajet_id = :tid");
$stmt->execute([':uid' => $user_id, ':tid' => $trajet_id]);
if ($stmt->fetchColumn() > 0) {
    header("Location: {$redirect}{$sep}erreur=deja");
    exit;
}



// Transaction sécurisée
try {
    $pdo->beginTransaction();

    // 1) Déduire les crédits (seulement si suffisants)
    $stmt = $pdo->prepare("
        UPDATE utilisateurs 
        SET credits = credits - :prix 
        WHERE id = :id AND credits >= :prix
    ");
    $stmt->execute([':prix' => (int)$trajet['prix'], ':id' => $user_id]);

    if ($stmt->rowCount() !== 1) {
        $pdo->rollBack();
        header("Location: {$redirect}{$sep}erreur=credits");
        exit;
    }

    // 2) Décrémenter une place (si disponible et trajet toujours à_venir)
    $stmt = $pdo->prepare("
        UPDATE trajets
        SET nb_places_restantes = nb_places_restantes - 1
        WHERE id = :id AND nb_places_restantes > 0 AND statut IN ('à_venir', 'en_cours')
    ");
    $stmt->execute([':id' => $trajet_id]);

    if ($stmt->rowCount() !== 1) {
        $pdo->rollBack();
        header("Location: {$redirect}{$sep}erreur=places");
        exit;
    }

    // 3) Insérer la participation
    $stmt = $pdo->prepare("
        INSERT INTO participations (utilisateur_id, trajet_id, valide)
        VALUES (:uid, :tid, 1)
    ");
    $stmt->execute([':uid' => $user_id, ':tid' => $trajet_id]);

    $pdo->commit();

    header("Location: {$redirect}{$sep}succes=ok");
    exit;

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log("Erreur participation : " . $e->getMessage());
    header("Location: {$redirect}{$sep}erreur=exception");
    exit;
}
