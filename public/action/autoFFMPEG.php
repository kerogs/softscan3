<?php
    require_once '../../config.php';
    
    // check for method GET

    $srvConfigJSON['autoFFMPEG'] = !isset($_GET['set']) || $_GET['set'] == 'true' ? true : false;

    file_put_contents(__DIR__ . '/../../backend/config.json', json_encode($srvConfigJSON, JSON_PRETTY_PRINT));

    header('Location: /');