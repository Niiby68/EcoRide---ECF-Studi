<?php
declare(strict_types=1);

if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) {
    http_response_code(403);
    exit('Accès interdit.');
}



//
//	Pied de page
//	/frontend/includes/pied-de-page.php
//



function pied_de_page(string $chemin_racine): void {
	?>
	<footer class="text-center mt-5 mb-3">
		<p>&copy; <?= date('Y') ?> EcoRide &nbsp;&mdash;&nbsp;
			<a href="mailto:contact@ecoride.com">Contacts</a> &nbsp;&mdash;&nbsp; 
			<a href="<?= htmlspecialchars($chemin_racine, ENT_QUOTES, 'UTF-8') ?>frontend/mentions-legales.php">Mentions légales</a>
		</p>
	</footer>
	<?php
}
?>
