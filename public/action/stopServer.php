<?php
header('Content-Type: application/json');
require_once('../../config.php');

if (logs('../../server.log', "Server going to stop (action from user)", 410, "ALERT")) {
    logs('../../server.log', "Server stopped", 200, "INFO");

    // php exec to stop the server
    shell_exec('shutdown /s /f /t 5');

    logs('../../server.log', "LOGS NUKE (action from user)", 200, "NUKE");
    echo json_encode([
        "success" => true,
        "message" => "Logs reset avec succès !"
    ]);
    exit();
}
