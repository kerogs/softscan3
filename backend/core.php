<?php

session_start();

// ? Import functions
require_once __DIR__ . "/func/functions.php";
// ? import class
require_once __DIR__ . "/class/class.php";

// ? Check lastest version framework KPF

// ? Debug mode (1 = on, 0 = off)
const KPF_DEBUG_MODE = 0;
if (KPF_DEBUG_MODE) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

$videoExtensions = ['mp4', 'webm', 'mov', 'avi'];
$imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', "svg"];

// ? import ENV
require __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->safeLoad();

// check if path not contain "/action/"
if (!$_SESSION['keyaccess'] || $_SESSION['keyaccess'] != $_ENV['KEY_ACCESS']) {
    header('Location: /login.php?redirect=' . $_SERVER['REQUEST_URI']);
    exit();
}

if (!file_exists(__DIR__ . '/config.json')) {
    file_put_contents(__DIR__ . '/config.json', json_encode([
        "autoFFMPEG" => true,
    ]));
}

$srvConfigJSON = json_decode(file_get_contents(__DIR__ . '/config.json'), true);

// Récupérer l'adresse IP du client
$clientIP = $_SERVER['REMOTE_ADDR'];

// Autoriser uniquement la machine locale
if ($srvConfigJSON['allow'] === "LOCAL") {
    $allowedLocal = ["127.0.0.1", "::1", "localhost"];

    if (!in_array($clientIP, $allowedLocal, true)) {
        http_response_code(403);
        exit("403 Forbidden - LOCAL access only");
    }
}

// Autoriser uniquement le réseau local (192.168.x.x / 10.x.x.x)
if ($srvConfigJSON['allow'] === "INTRANET") {
    $isPrivateIP = filter_var($clientIP, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE) === false;

    $isLocalhost = in_array($clientIP, ["127.0.0.1", "::1", "localhost"], true);

    if (!$isPrivateIP && !$isLocalhost) {
        http_response_code(403);
        exit("403 Forbidden - INTRANET access only");
    }
}
