<?php
declare(strict_types=1);

if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) {
    http_response_code(403);
    exit('Accès interdit.');
}



//
//	Fonction de modification du format d'une durée
//	Chemin : /frontend/includes/modif-duree.php
//
//	Exemple : "01:35:00" → "1h 35mn"
//



function formatDuree(?string $duree): string {
    if (!$duree || !str_contains($duree, ':')) {
        return 'N/A';
    }

    [$heures, $minutes, $secondes] = array_map('intval', explode(':', $duree));

    $result = '';
    if ($heures > 0) {
        $result .= $heures . 'h';
    }
    if ($minutes > 0) {
        $result .= ($result ? ' ' : '') . $minutes . 'mn';
    }

    return $result !== '' ? $result : '0mn';
}
