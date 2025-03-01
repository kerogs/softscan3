<?php
// json
header('Content-Type: application/json');
require_once '../../config.php';

// ! REMOVE ALL IMAGES
function deleteFolderContents($folder)
{
    if (!is_dir($folder)) {
        logs('../../server.log', "Invalid folder path", 400, "NUKE");
        return false;
    }

    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($folder, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );

    foreach ($files as $fileInfo) {
        $action = $fileInfo->isDir() ? 'rmdir' : 'unlink';
        if (!$action($fileInfo->getRealPath())) {
            logs('../../server.log', "Failed to delete: " . $fileInfo->getRealPath(), 500, "NUKE");
            return false;
        }
    }

    logs('../../server.log', "DATA NUKE (action from user)", 200, "NUKE");
    return true;
}

$r = deleteFolderContents("../public_data/");
file_put_contents("../public_data/.gitkeep", "");

// ! DELETE STATS FILE

unlink("../temp/stats.json");
logs('../../server.log', "STATS FILE NUKE (action from user)", 200, "NUKE");

// ! DELETE ALL FFMPEG FILES
deleteFolderContents("../temp/thumbnail/");
logs('../../server.log', "FFMPEG FILES NUKE (action from user)", 200, "NUKE");

// ! DELETE MAP FILES
unlink("../../backend/map.json");
unlink("../../backend/map_history.json");

echo json_encode([
    "success" => true,
    "message" => "Data nuked with success !"
]);
exit();
