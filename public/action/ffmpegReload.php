<?php 

    header('Content-Type: application/json');

    require_once '../../config.php';

    logs('../../server.log', "Reload ffmpeg (action from user)", 200, "INFO");

    if(!file_exists('../../ffmpeg_start.php')){
        echo json_encode([
            "success" => false,
            "message" => "Impossible de charger ffmpeg_start.php"
        ]);
        exit();
    }
    
    require_once '../../ffmpeg_start.php';

    echo json_encode([
        "success" => true,
        "message" => "Ffmpeg reload en cours... Cela peut prendre un certain temps !"
    ]);
    exit();

?>