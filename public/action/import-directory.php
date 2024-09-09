<?php

// Démarrer le chronomètre
$startTime = microtime(true);

// Initialisation des compteurs
$successCount = 0;
$failureCount = 0;
$totalSize = 0;
$foldersCreated = 0;
$files = isset($_FILES['files']) ? $_FILES['files'] : [];
$filePaths = isset($_POST['filePaths']) ? json_decode($_POST['filePaths'], true) : [];

if (!empty($files) && !empty($filePaths)) {
    // Répertoire de destination de l'upload
    $uploadDir = '../public_data/';

    // Créer le dossier de destination s'il n'existe pas
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // Parcourir chaque fichie r uploadé
    foreach ($files['tmp_name'] as $key => $tmp_name) {
        // Récupérer le chemin relatif original depuis le tableau des chemins
        $relativePath = $filePaths[$key];
        $destination = $uploadDir . $relativePath;

        // Récupérer le dossier parent et le créer si nécessaire
        $folderPath = dirname($destination);
        if (!is_dir($folderPath)) {
            mkdir($folderPath, 0755, true);
            $foldersCreated++;
        }

        // Calculer la taille totale des fichiers
        $fileSize = filesize($tmp_name);  // Taille en octets
        $totalSize += $fileSize;

        // Déplacer le fichier vers sa destination finale en respectant l'arborescence
        if (is_uploaded_file($tmp_name)) {
            if (move_uploaded_file($tmp_name, $destination)) {
                $successCount++;
                // echo '<p style="color:green;">Le fichier ' . $relativePath . ' a été uploadé avec succès.</p>';
            } else {
                $failureCount++;
                // echo '<p style="color:red;">Erreur lors du téléchargement du fichier ' . $relativePath . '.</p>';
            }
        } else {
            $failureCount++;
            // echo '<p style="color:red;">Le fichier ' . $relativePath . ' n\'a pas été correctement uploadé.</p>';
        }
    }

    // Calculer le temps total écoulé
    $endTime = microtime(true);
    $timeElapsed = $endTime - $startTime;

    // Conversion des tailles en Mo et Go
    $totalSizeMB = $totalSize / (1024 * 1024); // Mo
    $totalSizeGB = $totalSizeMB / 1024; // Go

    // Afficher les résultats
    header('Location: ../add/create?success=ok');
} else {
    header('Location: ../add/create?success=ko');
}
