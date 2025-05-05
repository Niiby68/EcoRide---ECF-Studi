<?php
//
//	Traitement pour le formulaire d'inscription
//
//	Chemin : /backend/signup_traitement.php
//

header('Content-Type: application/json');

// Charger l'objet Utilisateur
require_once 'models/Utilisateur.php';

$response = ['success' => false, 'message' => 'Une erreur est survenue.'];

try {
    // Connexion à la BDD
    $pdo = new PDO('mysql:host=localhost;dbname=ecoride;charset=utf8', 'root', '');

    // Récupérer les données POST
    $pseudo = $_POST['pseudo'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['mot_de_passe'] ?? '';
    $role = $_POST['role'] ?? '';

    // Créer l'objet utilisateur
    $user = new Utilisateur();

    // Valider les données
    if (!$user->set_pseudo($pseudo)) {
        throw new Exception('Pseudo invalide.');
    }
    if (!$user->set_email($email)) {
        throw new Exception('Email invalide.');
    }
    if (!$user->set_password($password)) {
        throw new Exception('Mot de passe trop court (min 6 caractères).');
    }
    if (!$user->set_role($role)) {
        throw new Exception('Rôle invalide.');
    }

    // Vérifier si l'utilisateur existe déjà
    if ($user->is_user_exist($pdo)) {
        throw new Exception('Cet email est déjà enregistré.');
    }

	// Vérifier si le pseudo existe déjà
	if ($user->is_pseudo_exist($pdo)) {
		throw new Exception('Ce pseudo est déjà utilisé.');
	}

    // Ajouter l'utilisateur à la BDD
    if ($user->add_user($pdo)) {
        $response['success'] = true;
        $response['message'] = 'Inscription réussie !';
    } else {
        throw new Exception('Erreur lors de l\'inscription.');
    }
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
?>
