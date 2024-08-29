<?php

chdir('../../');

require_once('../backend/core.php');

if ($_GET['dir'] == true) {
    $directoryToScan = $_GET['dir'];
} else {
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

foreach ($resultsGalerie as $image) {

    $extension = pathinfo($image, PATHINFO_EXTENSION);

    switch ($extension) {
        case in_array($extension, $videoExtensions):
            $icon = '<i class="bx bxs-video-recording"></i> '.$extension;
            break;
        case in_array($extension, $imageExtensions):
            $icon = '<i class="bx bxs-image"></i> '.$extension;
            break;
        default:
            $icon = '<i class="bx bxs-file"></i> '.$extension;
            break;
    }

    if (in_array($extension, $videoExtensions)) {
        echo '<div><a href="view?url=' . htmlspecialchars($image) . '"><div class="type">' . $icon . ' </div><img src="' . htmlspecialchars(videoToThumbnailURL($image)) . '" alt=""></a></div>';
    } else {
        echo '<div><a href="view?url=' . htmlspecialchars($image) . '"><div class="type">' . $icon . ' </div><img src="' . htmlspecialchars($image) . '" alt=""></a></div>';
    }
}
