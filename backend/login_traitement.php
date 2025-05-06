<?php
//
//	Script : login_traitement.php
//
//	Chemin : /backend/login_traitement.php
//

session_start();
header('Content-Type: application/json');

require_once 'models/Utilisateur.php';

$response = ['success' => false, 'message' => 'Une erreur est survenue.'];

try {
    $pdo = new PDO('mysql:host=localhost;dbname=ecoride;charset=utf8', 'root', '');

    $email = $_POST['email'] ?? '';
    $password = $_POST['mot_de_passe'] ?? '';

    if (empty($email) || empty($password)) {
        throw new Exception('Veuillez remplir tous les champs.');
    }

    $user = new Utilisateur();
    $user->set_email($email);

    $data = $user->load_user_by_email($pdo);

    if (!$data) {
        throw new Exception('Aucun utilisateur trouvé avec cet email.');
    }

    if (!password_verify($password, $user->get_hashpass())) {
        throw new Exception('Mot de passe incorrect.');
    }

    // Enregistrer les infos en session
    $_SESSION['user_id'] = $data['id'];
    $_SESSION['pseudo'] = $data['pseudo'];
    $_SESSION['role'] = $data['role'];

    $response['success'] = true;
    $response['message'] = 'Connexion réussie !';
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
?>
