<?php
    require_once '../../config.php';

    $srvConfigJSON = json_decode(file_get_contents(__DIR__ . '/config.json'), true);
    
    // check for method GET

    $srvConfigJSON['autoFFMPEG'] = !isset($_GET['set']) || $_GET['set'] == 'true' ? true : false;

    file_put_contents(__DIR__ . '/../../backend/config.json', json_encode($srvConfigJSON, JSON_PRETTY_PRINT));

    header('Location: /');