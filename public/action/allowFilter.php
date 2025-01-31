<?php

require_once '../../config.php';

if(!isset($_GET['allow'])) {
    header('Location: /');
}

$srvConfigJSON['allow'] = $_GET['allow'];
file_put_contents(__DIR__ . '/../../backend/config.json', json_encode($srvConfigJSON, JSON_PRETTY_PRINT));

header('Location: /');