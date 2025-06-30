<?php
declare(strict_types=1);

if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) {
    http_response_code(403);
    exit('Accès interdit.');
}



//
//	Message d'activation du Javascript
//	Chemin : /frontend/includes/javamess.php
//



function javamess(): void {
	?>
	<noscript>
		<div class="container">
			<div class="alert alert-warning javamess" role="alert">
				<strong>Attention :</strong> JavaScript est désactivé dans votre navigateur.  
				Le site fonctionnera en mode simplifié, mais certaines fonctionnalités seront limitées.
			</div>
		</div>
    </noscript>
	<?php
}
?>
