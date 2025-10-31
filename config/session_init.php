<?php
declare(strict_types=1);

if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) {
	http_response_code(403);
	exit('Accès interdit.');
}



//
//  Initialisation sécurisée de la session
//  Chemin : /config/session_init.php
//



// Empêche JavaScript d’accéder au cookie de session
ini_set('session.cookie_httponly', '1');

// Empêche l’envoi du cookie en HTTP si HTTPS est activé
ini_set('session.cookie_secure', 
	isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? '1' : '0'
);

// Empêche la réutilisation d’une ancienne ID de session
ini_set('session.use_strict_mode', '1');

// N’autorise l’ID de session que via cookie (pas d’URL)
ini_set('session.use_only_cookies', '1');

// Empêche la propagation d’une ID de session via l’URL
ini_set('session.use_trans_sid', '0');



//
// Paramétrage de la session
//
session_set_cookie_params([
    'lifetime' => 0,
    'path'     => '/',
    'domain'   => '',
    'secure'   => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on'),
    'httponly' => true,
    'samesite' => 'Lax'
]);



//
// Démarrage de la session
//
if (session_status() === PHP_SESSION_NONE) {
	session_start();
}
?>
