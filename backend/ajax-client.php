<?php
declare(strict_types=1);



//
//  Espace Client ( Traitement )
//  Chemin : /backend/ajax-client.php
//
$chemin_racine = '../';



//
//  Initialisation de la session
//
require_once $chemin_racine . 'config/session_init.php';



//
//  Fichiers additionnels
//
require_once $chemin_racine . 'config/db.php';
require_once 'models/Utilisateur.php';



//
// Vérifie si l’utilisateur est connecté
//
if (empty($_SESSION['user_id'])) {
	http_response_code(401);
	echo '<div class="alert alert-warning">Vous devez être connecté pour accéder à cette page.</div>';
	exit;
}



//
// Récupère la section demandée
//
$page = $_GET['page'] ?? 'compte';
$allowed = ['compte', 'chauffeur', 'passager'];

if (!in_array($page, $allowed, true)) {
	http_response_code(400);
	echo '<div class="alert alert-danger">Section inconnue.</div>';
	exit;
}



//
// Charge les infos utilisateur
//
$user = new Utilisateur();
$user->set_id((int)$_SESSION['user_id']);

if (!$user->load_user_by_id($pdo)) {
	http_response_code(500);
	echo '<div class="alert alert-danger">Impossible de charger votre profil.</div>';
	exit;
}



//
// Route selon la section demandée
//
switch ($page) {
	case 'compte':
		render_compte($user, $chemin_racine);
		break;

	case 'chauffeur':
		render_chauffeur();
		break;

	case 'passager':
		render_passager();
		break;
}



//
// Section Mon Compte
//
function render_compte(Utilisateur $u, string $chemin_racine): void {
	$statut = ((int)$u->get_actif() === 1) ? 'Actif' : 'Suspendu';
	?>
	<div class="row">
		<div class="col-md-4 mb-3">
			<div class="card h-100 p-3 text-center">
				<img src="<?= $chemin_racine ?>frontend/img/profils/<?= htmlspecialchars($u->get_photo() ?? 'defaut.png', ENT_QUOTES, 'UTF-8') ?>"
					 alt="Photo de profil"
					 class="img-fluid rounded mb-3"
					 style="max-width:160px;">
				<div><strong>Pseudo :</strong> <?= htmlspecialchars($u->get_pseudo() ?? '', ENT_QUOTES, 'UTF-8') ?></div>
				<div><strong>Email :</strong> <?= htmlspecialchars($u->get_email() ?? '', ENT_QUOTES, 'UTF-8') ?></div>
				<div><strong>Inscription :</strong>
					<?= $u->get_date_inscription() ? (new DateTime($u->get_date_inscription()))->format('d/m/Y H:i') : '-' ?>
				</div>
				<div><strong>Crédits :</strong> <?= (int)($u->get_credits() ?? 0) ?></div>
				<div><strong>Statut :</strong> <?= htmlspecialchars($statut, ENT_QUOTES, 'UTF-8') ?></div>
			</div>
		</div>

		<div class="col-md-8 mb-3">
			<div class="card h-100 p-3">
				<h5>Actions rapides</h5>
				<div class="d-flex flex-wrap gap-2 mt-3">
					<a class="btn btn-primary" href="#">Modifier mon profil</a>
					<a class="btn btn-primary" href="#">Ajouter des crédits</a>
					<a class="btn btn-secondary" href="<?= $chemin_racine ?>backend/logout.php">Se déconnecter</a>
				</div>
			</div>
		</div>
	</div>
	<?php
}



//
// Section Chauffeur
//
function render_chauffeur(): void {
	echo '<div class="card p-3"><h5>Espace Chauffeur</h5><p>Fonctionnalité à venir…</p></div>';
}



//
// Section Passager
//
function render_passager(): void {
	echo '<div class="card p-3"><h5>Espace Passager</h5><p>Fonctionnalité à venir…</p></div>';
}
