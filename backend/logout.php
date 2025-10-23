<?php
declare(strict_types=1);



//
//	Page de déconnexion
//	Chemin : /backend/logout.php
//



session_start();
session_destroy();
header('Location: ../frontend/index.php');
exit;
