<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $uploadDir = "../" . $_POST['dir']; // Récupérer le dossier sélectionné
    $targetDir = rtrim($uploadDir, '/') . '/';

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $successCount = 0;
    $errorCount = 0;

    if (isset($_FILES['files']) && is_array($_FILES['files']['name'])) {
        $files = $_FILES['files'];

        for ($i = 0; $i < count($files['name']); $i++) {
            if ($files['error'][$i] === UPLOAD_ERR_OK) {
                $targetFile = $targetDir . basename($files['name'][$i]);

                if (move_uploaded_file($files['tmp_name'][$i], $targetFile)) {
                    $successCount++;
                } else {
                    $errorCount++;
                }
            } else {
                $errorCount++;
            }
        }
    } else {
        $errorCount++;
    }

    if ($errorCount === 0) {
        header('Location: ../add/create/ok');
    } elseif ($successCount > 0) {
        header('Location: ../add/create/partial');
    } else {
        header('Location: ../add/create/ko');
    }
    exit;
}
?>
