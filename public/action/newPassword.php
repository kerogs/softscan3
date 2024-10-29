<?php

if (isset($_GET['newPassword'])) {
    require_once '../../config.php';

    // Échapper la nouvelle valeur pour éviter les problèmes de sécurité
    $newPassword = htmlspecialchars($_GET['newPassword']);

    // Chemin vers le fichier .env
    $envFilePath = '../../.env';

    // Lire le contenu du fichier .env
    $envContent = file_get_contents($envFilePath);

    // Vérifiez si le fichier a été lu correctement
    if ($envContent === false) {
        die('Erreur lors de la lecture du fichier .env');
    }

    // Convertir le contenu en tableau associatif
    $envArray = [];
    foreach (explode("\n", $envContent) as $line) {
        // Ignorer les lignes vides et les commentaires
        if (trim($line) !== '' && strpos(trim($line), '#') !== 0) {
            list($key, $value) = explode('=', $line, 2);
            $envArray[trim($key)] = trim($value);
        }
    }

    // Modifier la valeur de KEY_ACCESS
    $envArray['KEY_ACCESS'] = password_hash($newPassword, PASSWORD_DEFAULT);

    // Reconstruire le contenu du fichier .env
    $newEnvContent = "";
    foreach ($envArray as $key => $value) {
        $newEnvContent .= "{$key}={$value}\n";
    }

    // Écrire le nouveau contenu dans le fichier .env
    if (file_put_contents($envFilePath, $newEnvContent) === false) {
        die('Erreur lors de l\'écriture dans le fichier .env');
    }

    echo "KEY_ACCESS a été mis à jour avec succès.";
} else {
    echo "Aucune nouvelle valeur fournie.";
}
?>
