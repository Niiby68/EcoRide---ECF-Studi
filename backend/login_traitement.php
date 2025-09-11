<?php
//
//	Page de connexion ( Traitement )
//	Chemin : /backend/login_traitement.php
//



session_start();
require_once 'models/Utilisateur.php';



// Détection de l’AJAX
$is_ajax = !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
    && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

try {
    // Connexion PDO en mode exception
    $pdo = new PDO(
        'mysql:host=localhost;dbname=ecoride;charset=utf8',
        'root',
        '',
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    // Récupération des données du formulaire
    $email = $_POST['email'] ?? '';
    $password = $_POST['mot_de_passe'] ?? '';
	$next = $_POST['next'] ?? '';

    // Validation basique
    if (empty($email) || empty($password)) {
        throw new Exception('Veuillez remplir tous les champs.');
    }

    // Chargement utilisateur
    $userObj = new Utilisateur();
    $userObj->set_email($email);
    $data = $userObj->load_user_by_email($pdo);
    if (!$data) {
        throw new Exception('Aucun utilisateur trouvé avec cet email.');
    }

    // Vérification du mot de passe
    if (!password_verify($password, $userObj->get_hashpass())) {
        throw new Exception('Mot de passe incorrect.');
    }

    // Création de la session
    $_SESSION['user_id'] = $data['id'];
    $_SESSION['pseudo']  = $data['pseudo'];
    $_SESSION['role']    = $data['role'];
	
	// Calcul de la redirection sécurisée
    $redirect = '/frontend/index.php'; // valeur par défaut
    if (!empty($next)) {
        $parts  = parse_url($next);
        $path   = $parts['path']  ?? '';
        $query  = isset($parts['query']) ? ('?'.$parts['query']) : '';
        $host   = $parts['host']  ?? '';
        $scheme = $parts['scheme']?? '';

        // Autoriser uniquement une URL interne relative commençant par '/'
        if ($scheme === '' && $host === '' && str_starts_with($path, '/')) {
            $redirect = $path.$query;
        }
    }

    if ($is_ajax) {
        // Réponse JSON pour AJAX
        header('Content-Type: application/json');

        $payload = [
            'success' => true,
            'message' => 'Connexion réussie !'
        ];
        if (!empty($next) && $redirect !== '/frontend/index.php') {
            $payload['redirect'] = $redirect;
        }

        echo json_encode($payload);
        exit;
    } else {
        // Redirection classique
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
        header("Location: ../frontend/login.php?error=$msg");
        exit;
    }
}
?>
