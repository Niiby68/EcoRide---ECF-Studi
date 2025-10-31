<?php
declare(strict_types=1);



//
//	Page de déconnexion
//	Chemin : /backend/logout.php
//
$chemin_racine = '../';



//
//	Initialisation de la session
//
require_once $chemin_racine . 'config/session_init.php';
session_destroy();
header('Location: ' . $chemin_racine . 'frontend/index.php');
exit;
