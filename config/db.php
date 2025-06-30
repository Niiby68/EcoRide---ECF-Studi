<?php
declare(strict_types=1);

if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) {
    http_response_code(403);
    exit('Accès interdit.');
}



// 
//	Connexion à la BDD EcoRide
//	Chemin : /config/db.php
//



define('DB_HOST',    'localhost');
define('DB_NAME',    'ecoride');
define('DB_USER',    'root');
define('DB_PASS',    '');



try {
    $pdo = new PDO(
        'mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8',
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    exit('Erreur de connexion à la base de données : '.$e->getMessage());
}
?>
