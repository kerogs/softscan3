<?php

chdir('../../');

require_once('../backend/core.php');


// Limiter les résultats pour la galerie
$resultsGalerie = array_slice($results, 0, 40);

foreach ($resultsGalerie as $image) {
    echo '<div><a href="view?url=' . htmlspecialchars($image) . '"><img src="' . htmlspecialchars($image) . '" alt=""></a></div>';
}
?>