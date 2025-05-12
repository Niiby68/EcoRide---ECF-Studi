<?php
//
//	Script : signup_traitement.php
//
//	Chemin : /backend/signup_traitement.php
//



session_start();



// Détection AJAX
$is_ajax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

require_once 'models/Utilisateur.php';

$response = ['success' => false, 'message' => 'Une erreur est survenue.'];

try {
    $pdo = new PDO('mysql:host=localhost;dbname=ecoride;charset=utf8', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    $pseudo = $_POST['pseudo'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['mot_de_passe'] ?? '';
    $role = $_POST['role'] ?? '';

    $user = new Utilisateur();

    if (!$user->set_pseudo($pseudo) || !$user->set_email($email) || !$user->set_password($password) || !$user->set_role($role)) {
        throw new Exception('Données invalides.');
    }

    if ($user->is_user_exist($pdo)) {
        throw new Exception('Cet email est déjà enregistré.');
    }

    if ($user->is_pseudo_exist($pdo)) {
        throw new Exception('Ce pseudo est déjà utilisé.');
    }

    if (!$user->add_user($pdo)) {
        throw new Exception('Erreur lors de l’enregistrement.');
    }

    // Réponse AJAX
    if ($is_ajax) {
        $response['success'] = true;
        $response['message'] = 'Inscription réussie ! Redirection vers la connexion...';
        $response['redirect'] = '../frontend/login.php';
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }

    // Réponse classique (sans AJAX)
    header('Location: ../frontend/login.php?success=1');
    exit;

} catch (Exception $e) {
    if ($is_ajax) {
        $response['message'] = $e->getMessage();
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    } else {
        $msg = urlencode($e->getMessage());
        header("Location: ../frontend/signup.php?error=$msg");
        exit;
    }
}
?>
