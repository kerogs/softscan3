<?php
header('Content-Type: application/json');

require_once '../../config.php';

if (unlink('../../server.log')) {
    file_put_contents("../../server.log", "");
    if (file_exists('../../server.log')) {
        // json return
        logs('../../server.log', "LOGS NUKE (action from user)", 200, "NUKE");
        echo json_encode([
            "success" => true,
            "message" => "Logs reset avec succès !"
        ]);
        exit();
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Impossible de créer le fichier server.log !"
        ]);
        exit();
    }
} else {
    echo json_encode([
        "success" => false,
        "message" => "Impossible de supprimer le fichier server.log !"
    ]);
    exit();
}
