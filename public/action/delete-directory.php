<?php

function deleteFolderContents($folder) {
    if (!is_dir($folder)) {
        return false;
    }

    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($folder, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );

    foreach ($files as $fileInfo) {
        $action = $fileInfo->isDir() ? 'rmdir' : 'unlink';
        if (!$action($fileInfo->getRealPath())) {
            return false;
        }
    }

    return true;
}

$path = $_POST['dir'];
$path = trim($path);
$name = $_POST['name'];

if(deleteFolderContents("../" . $path . "/" . $name)){
    rmdir("../" . $path . "/" . $name);
    header('Location: ../add/create?success=ok');
} else{
    header('Location: ../add/create?success=ko');
}