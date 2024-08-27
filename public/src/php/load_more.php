<?php

chdir('../../');

require_once('../backend/core.php');

if($_GET['dir'] == true){
    $directoryToScan = $_GET['dir'];
} else{
    $directoryToScan = 'public_data';
}
$returnNameType = 2;
$returnDirPath = true;
$authorise = ['jpg', 'gif', 'png', 'jpeg', 'webp', 'svg', 'mp4', 'webm', 'mov', 'avi'];
$ignore = [];
$recursive = true;

$results = [];
$directories = [];

// Scan the directory for images
scanDirRecursive($directoryToScan, $returnNameType, $returnDirPath, $authorise, $ignore, $recursive, $results, $directories);

// Retrieve how many images have already been displayed
$offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;
$limit = 40;

// Get the next set of images
$resultsGalerie = array_slice($results, $offset, $limit);

// Output the images in JSON format
echo json_encode($resultsGalerie);
?>
