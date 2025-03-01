<?php

require_once '../../config.php';

if(!isset($_GET['url'])) {
    header('Location: /');
    exit();
}

var_dump($_GET);

$path = '../'.$_GET['url'];

if(unlink($path)) {
    // echo 'deleted';
    header('Location: /');
    exit();
} else{
    // echo 'error PATH : '.$path;
    header('Location: /view?url='.$_GET['url'].'&error=cantDelete&viewCounter=noCount');
    exit();
}