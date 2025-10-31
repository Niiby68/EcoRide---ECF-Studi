<?php
declare(strict_types=1);



//
//  Page de connexion ( Traitement )
//  Chemin : /backend/login_traitement.php
//
$chemin_racine = '../';



//
//	Initialisation de la session
//
require_once $chemin_racine . 'config/session_init.php';



//
//  Fichiers additionnels
//
require_once $chemin_racine . 'config/db.php';
require_once 'login_attempts.php';
require_once 'models/Utilisateur.php';



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
if (empty($_POST['csrf_token']) || empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    http_response_code(403); // Forbidden

    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) 
        && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Échec de la vérification CSRF.']);
    } else {
        header('Location: ' . $chemin_racine . 'frontend/login.php?error=' . urlencode('Votre session a expiré, merci de recharger la page.'));
    }
    exit;
}



//
// Détection de l'AJAX
//
$is_ajax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';



//
// Connexion
//
try {
    // Vérification du statut de l'IP
    $client_ip = get_client_ip();
    $block = is_ip_blocked($pdo, $client_ip);
	if ($block['blocked']) {
		$minutes = ceil($block['remaining_seconds'] / 60);
		if ($is_ajax) {
			header('Content-Type: application/json');
			echo json_encode([
				'success' => false,
				'message' => 'Trop de tentatives. Réessayez dans ' . $minutes . ' minute(s).',
				'remaining_seconds' => $block['remaining_seconds']
			]);
		} else {
			$msg = urlencode('Trop de tentatives. Réessayez dans ' . $minutes . ' minute(s).');
			header('Location: ' . $chemin_racine . 'frontend/login.php?error=$msg');
		}
		exit;
	}

    // Récupération des données du formulaire
    $email = $_POST['email'] ?? '';
    $password = $_POST['mot_de_passe'] ?? '';
    $next = $_POST['next'] ?? '';

	// Validation basique
	if (empty($email) || empty($password)) {
		record_failed_attempt($pdo, $client_ip);
		throw new Exception('Merci de renseigner votre email et votre mot de passe.');
	}

	// Vérif email valide
	if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		record_failed_attempt($pdo, $client_ip);
		throw new Exception('Adresse email invalide. Utilisez une adresse correcte.');
	}

	// Chargement utilisateur
	$userObj = new Utilisateur();
	$userObj->set_email($email);
	$ok = $userObj->load_user_by_email($pdo);
	if (!$ok) {
		record_failed_attempt($pdo, $client_ip);
		throw new Exception('Adresse email inconnue. Vérifiez ou créez un compte.');
	}

	// Vérification du mot de passe
	if (!password_verify($password, $userObj->get_motdepasse())) {
		record_failed_attempt($pdo, $client_ip);
		throw new Exception('Mot de passe incorrect. Veuillez réessayer.');
	}

    // Connexion réussie → reset des tentatives
    reset_attempts($pdo, $client_ip);

    // Sécurité session
    session_regenerate_id(true);

    // Création de la session
    $_SESSION['user_id'] = $userObj->get_id();
    $_SESSION['pseudo']  = $userObj->get_pseudo();

    // Calcul de la redirection sécurisée
    $redirect = '/frontend/index.php';
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

    if ($is_ajax) {
        header('Content-Type: application/json');
        $payload = [
            'success' => true,
            'message' => 'Connexion réussie ! Vous allez être redirigé...'
        ];
        if (!empty($next) && $redirect !== '/frontend/espace-client.php') {
            $payload['redirect'] = $redirect;
        }
        echo json_encode($payload);
        exit;
    } else {
        header('Location: '.$redirect);
        exit;
    }

} catch (Exception $e) {
    if ($is_ajax) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
        exit;
    } else {
        $msg = urlencode($e->getMessage());
        header('Location: ' . $chemin_racine . 'frontend/login.php?error=$msg');
        exit;
    }
}
?>
