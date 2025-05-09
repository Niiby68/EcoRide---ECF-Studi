<?php
//
//	Script : logout.php
//
//	Chemin : /backend/logout.php
//

session_start();
session_destroy();
header('Location: ../frontend/index.php');
exit;
