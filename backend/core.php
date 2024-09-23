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

if (!$_SESSION['keyaccess']) {
    header('Location: login.php');
    exit();
}

