<?php
//
//	Page d'inscription ( Traitement )
//	Chemin : /backend/signup_traitement.php
//



session_start();
require_once 'models/Utilisateur.php';



//
// Autoriser uniquement POST
//
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    exit('Méthode non autorisée.');
}



//
// Vérification du token CSRF
//
if (!isset($_POST['csrf_token'], $_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    http_response_code(403); // Forbidden
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Échec de la vérification CSRF.']);
    } else {
        header("Location: ../frontend/signup.php?error=" . urlencode("Votre session a expiré, merci de recharger la page."));
    }
    exit;
}



//
// Détection AJAX
//
$is_ajax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';



//
//	Traitement des données
//
$response = ['success' => false, 'message' => 'Une erreur est survenue.'];

try {
    $pdo = new PDO('mysql:host=localhost;dbname=ecoride;charset=utf8', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    $pseudo = $_POST['pseudo'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['mot_de_passe'] ?? '';
    $role = $_POST['role'] ?? '';
	$next = $_POST['next'] ?? '';

    $user = new Utilisateur();

    if (!$user->set_pseudo($pseudo) || !$user->set_email($email) || !$user->set_password($password) || !$user->set_role($role)) {
        throw new Exception('Certains champs ne sont pas valides. Vérifiez vos informations.');
    }

    if ($user->is_user_exist($pdo)) {
        throw new Exception('Cette adresse email est déjà utilisée.');
    }

    if ($user->is_pseudo_exist($pdo)) {
        throw new Exception('Ce pseudo n\'est pas disponible.');
    }

    if (!$user->add_user($pdo)) {
        throw new Exception('Un problème est survenu lors de l\'inscription. Veuillez réessayer.');
    }

	$redirect = '../frontend/login.php';
	if (!empty($next)) {
		$parts  = parse_url($next);
		$path   = $parts['path']  ?? '';
		$query  = isset($parts['query']) ? ('?'.$parts['query']) : '';
		$host   = $parts['host']  ?? '';
		$scheme = $parts['scheme']?? '';
		if ($scheme === '' && $host === '' && str_starts_with($path, '/')) {
			$redirect = $path.$query;
		}
	}

    // Réponse AJAX
    if ($is_ajax) {
        $response['success'] = true;
        $response['message'] = 'Inscription réussie ! Vous allez être redirigé...';
        $response['redirect'] = $redirect;
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }

    // Réponse classique (sans AJAX)
    header('Location: '.$redirect);
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
