<?php

header('Content-Type: application/json');

// Activer l'affichage des erreurs pour le débogage (uniquement en développement)
// ini_set('display_errors', 1);
// error_reporting(E_ALL);
session_start();







require_once __DIR__ . '/../../vendor/autoload.php';
// ! SECURITY CHECK IF USER IS LOGGED IN
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable('../../');
$dotenv->safeLoad();

if (!isset($_SESSION['keyaccess']) || $_SESSION['keyaccess'] != $_ENV['KEY_ACCESS']) {
    echo json_encode([
        "success" => false,
        "message" => "Unauthorized"
    ]);
    exit;
}







if (isset($_GET['newPassword'])) {
    // require_once '../../config.php';

    // Vérifier si la nouvelle valeur du mot de passe est bien présente
    if (empty($_GET['newPassword'])) {
        echo json_encode([
            "success" => false,
            "message" => "Veuillez fournir une nouvelle valeur pour le mot de passe."
        ]);
        exit;
    }

    // Échapper la nouvelle valeur pour éviter les problèmes de sécurité
    $newPassword = htmlspecialchars($_GET['newPassword']);

    // Chemin vers le fichier .env
    $envFilePath = '../../.env';

    // Vérifier si le fichier .env existe avant de tenter de le lire
    if (!file_exists($envFilePath)) {
        // echo 'Le fichier .env est introuvable.';
        echo json_encode([
            "success" => false,
            "message" => "Le fichier .env est introuvable."
        ]);
        exit;
    }

    // Lire le contenu du fichier .env
    $envContent = file_get_contents($envFilePath);

    // Vérifiez si le fichier a été lu correctement
    if ($envContent === false) {
        echo json_encode([
            "success" => false,
            "message" => "Impossible de lire le contenu du fichier .env."
        ]);
        exit;
    }

    // Convertir le contenu en tableau associatif
    $envArray = [];
    foreach (explode("\n", $envContent) as $line) {
        // Ignorer les lignes vides et les commentaires
        $line = trim($line);
        if ($line === '' || strpos($line, '#') === 0) {
            continue;
        }

        // Vérifier si la ligne est bien formatée (key=value)
        $parts = explode('=', $line, 2);
        if (count($parts) < 2) {
            // Ligne mal formatée, afficher un message de débogage et sauter cette ligne
            // echo "Ligne mal formatée ignorée : $line<br>";
            continue;
        }

        // Ajouter à l'array associatif
        list($key, $value) = $parts;
        $envArray[trim($key)] = trim($value);
    }

    // Vérifier si KEY_ACCESS existe avant de le modifier
    if (!isset($envArray['KEY_ACCESS'])) {
        // echo 'KEY_ACCESS introuvable dans le fichier .env.';
        echo json_encode([
            "success" => false,
            "message" => "KEY_ACCESS introuvable dans le fichier .env."
        ]);
        exit;
    }

    // Modifier la valeur de KEY_ACCESS
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    if ($hashedPassword === false) {
        // echo 'Erreur lors du hachage du mot de passe.';
        echo json_encode([
            "success" => false,
            "message" => "Erreur lors du hachage du mot de passe."
        ]);
        exit;
    }
    $envArray['KEY_ACCESS'] = $hashedPassword;

    // Reconstruire le contenu du fichier .env
    $newEnvContent = "";
    foreach ($envArray as $key => $value) {
        $newEnvContent .= "{$key}={$value}\n";
    }

    // Écrire le nouveau contenu dans le fichier .env
    if (file_put_contents($envFilePath, $newEnvContent) === false) {
        // echo 'Erreur lors de l\'écriture dans le fichier .env. Veuillez vérifier les permissions.';
        echo json_encode([
            "success" => false,
            "message" => "Erreur lors de l'écriture dans le fichier .env. Veuillez vérifier les permissions."
        ]);
        exit;
    }

    // echo "KEY_ACCESS a été mis à jour avec succès.";
    echo json_encode([
        "success" => true,
        "message" => "Le mot de passe a été mis à jour avec succès."
    ]);
    exit;
} else {
    // echo "Aucune nouvelle valeur fournie.";
    echo json_encode([
        "success" => false,
        "message" => "Aucune nouvelle valeur fournie."
    ]);
    exit;
}
