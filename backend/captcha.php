<?php
//
//  Génération d'un captcha maison (AJAX)
//  Chemin : /backend/captcha.php
//



session_start();



$nb1 = rand(1, 9);
$nb2 = rand(1, 9);

$_SESSION['captcha_result'] = $nb1 + $nb2;

header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'question' => "Combien font $nb1 + $nb2 ?"
]);
?>
