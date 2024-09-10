<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $uploadDir = "../".$_POST['dir']; // Récupérer le dossier sélectionné
    $targetDir = $uploadDir . '/';

    // Vérifier si le dossier existe
    if (!is_dir($uploadDir)) {
        echo "Le dossier sélectionné n'existe pas.";
        exit;
    }

    // Vérifier si des fichiers ont été téléchargés
    if (isset($_FILES['files'])) {
        $files = $_FILES['files'];

        // Boucle sur chaque fichier téléchargé
        for ($i = 0; $i < count($files['name']); $i++) {
            if ($files['error'][$i] == 0) {
                $targetFile = $targetDir . basename($files['name'][$i]);

                // Déplacer le fichier vers le dossier cible
                if (move_uploaded_file($files['tmp_name'][$i], $targetFile)) {
                    echo "Le fichier " . htmlspecialchars(basename($files['name'][$i])) . " a été téléchargé avec succès.<br>";
                } else {
                    echo "Erreur lors du téléchargement du fichier " . htmlspecialchars(basename($files['name'][$i])) . ".<br>";
                }
            } else {
                echo "Erreur avec le fichier " . htmlspecialchars(basename($files['name'][$i])) . ".<br>";
            }
        }
    } else {
        echo "Aucun fichier n'a été téléchargé.";
    }
}
