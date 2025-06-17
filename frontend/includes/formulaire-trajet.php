<?php
//
//	Formulaire de recherche de trajet
//	/frontend/includes/formulaire_trajet.php
//



function formulaire_trajet(string $action = 'recherche.php'): void {
	
	// Pré-remplissage des champs si disponible en GET
    $depart   = $_GET['depart']   ?? '';
    $arrivee  = $_GET['arrivee']  ?? '';
    $date     = $_GET['date']     ?? '';
	
	?>
	<form method="get" action="<?= htmlspecialchars($action, ENT_QUOTES, 'UTF-8') ?>" class="mb-4">
		<div class="row g-2">
			<div class="col-md-3">
				<input type="text" name="depart" class="form-control" placeholder="Ville de départ (ex : Strasbourg)" aria-label="Ville de départ" value="<?= htmlspecialchars($depart, ENT_QUOTES, 'UTF-8') ?>" required>
			</div>
			<div class="col-md-3">
				<input type="text" name="arrivee" class="form-control" placeholder="Ville d’arrivée (ex : Colmar)" aria-label="Ville d’arrivée" value="<?= htmlspecialchars($arrivee, ENT_QUOTES, 'UTF-8') ?>" required>
			</div>
			<div class="col-md-3">
				<input type="date" name="date" class="form-control" aria-label="Date de départ" value="<?= htmlspecialchars($date, ENT_QUOTES, 'UTF-8') ?>" required>
			</div>
			<div class="col-md-3">
				<button class="btn btn-primary w-100" type="submit">Rechercher</button>
			</div>
		</div>
	</form>
	<?php
}
?>
