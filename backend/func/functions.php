<?php

/**
 * Scans a directory and returns an array of files and directories within it.
 *
 * @param string $dir The directory to scan.
 * @param int $returnNameType The type of name to return for each file/directory.
 *                           0: Filename without extension.
 *                           1: Filename with extension.
 *                           2: Full file path.
 * @param bool $returnDirPath Whether to return the full directory path or just the directory name.
 * @param string|array $authorise A string or array of file extensions to allow.
 *                                If "*", all file extensions are allowed.
 * @param array $ignore An array of file extensions to ignore.
 * @param bool $recursive Whether to recursively scan subdirectories.
 * @return array An array with 'files' and 'directories' keys.
 *               'files' is an array of file names or full file paths based on $returnNameType.
 *               'directories' is an array of directory names or full directory paths based on $returnDirPath.
 */
if (!function_exists('scanDirRecursive')) {
    function scanDirRecursive($dir, $returnNameType, $returnDirPath, $authorise, $ignore, $recursive, &$results, &$directories, $baseDir = "") {
        if (!is_dir($dir)) {
            return;
        }

        $handle = opendir($dir);
        if (!$handle) {
            return;
        }

        $allowedExtensions = $authorise === "*" ? [] : array_map('strtolower', (array)$authorise);
        $ignoreExtensions = array_map('strtolower', (array)$ignore);

        while (($file = readdir($handle)) !== false) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            $filePath = "$dir/$file";
            $relativePath = $baseDir ? "$baseDir/$file" : $file;

            if (is_dir($filePath)) {
                if ($returnDirPath) {
                    $directories[] = $relativePath;
                } else {
                    $directories[] = $file;
                }

                if ($recursive) {
                    scanDirRecursive($filePath, $returnNameType, $returnDirPath, $authorise, $ignore, $recursive, $results, $directories, $relativePath);
                }
            } else {
                $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));

                if ((!empty($allowedExtensions) && !in_array($extension, $allowedExtensions)) ||
                    (!empty($ignoreExtensions) && in_array($extension, $ignoreExtensions))) {
                    continue;
                }

                switch ($returnNameType) {
                    case 1:
                        $results[] = $file;
                        break;
                    case 2:
                        $results[] = $filePath;
                        break;
                    default:
                        $results[] = pathinfo($file, PATHINFO_FILENAME);
                        break;
                }
            }
        }

        closedir($handle);
    }
}

function ss2_dirScanner($dir, $returnNameType = 0, $returnDirPath = false, $authorise = "*", $ignore = [], $recursive = false) {
    $results = [];
    $directories = [];

    scanDirRecursive($dir, $returnNameType, $returnDirPath, $authorise, $ignore, $recursive, $results, $directories);

    return [
        'files' => $results,
        'directories' => $directories
    ];
}


function ss2_sizeIMG($url, $returnRatioHeight = false) {
    // Initialiser les dimensions à null
    $width = null;
    $height = null;
    $ratio = null;

    // Obtenir le type mime du fichier
    $fileType = mime_content_type($url);

    if (strpos($fileType, 'image') !== false) {
        // C'est une image ou un GIF, nous pouvons utiliser getimagesize
        list($width, $height) = getimagesize($url);
    } elseif (strpos($fileType, 'video') !== false) {
        // C'est une vidéo, utiliser FFmpeg pour obtenir les dimensions
        $ffmpegOutput = shell_exec("ffmpeg -i " . escapeshellarg($url) . " 2>&1");
        if (preg_match('/, (\d{2,5})x(\d{2,5})/', $ffmpegOutput, $matches)) {
            $width = $matches[1];
            $height = $matches[2];
        }
    }

    // Calculer le rapport si demandé et les dimensions sont disponibles
    if ($returnRatioHeight && $width && $height) {
        $ratio = $width / $height;
    }

    $result = ['width' => $width, 'height' => $height];
    if ($returnRatioHeight) {
        $result['ratio'] = $ratio;
    }

    return $result;
}


function getRecentFiles($directory, $limit = 5) {
    $files = [];

    // Fonction récursive pour parcourir les sous-dossiers
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));

    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getFilename() !== '.gitkeep') {
            // Ajout du fichier avec son chemin complet et son temps de modification
            $files[$file->getPathname()] = $file->getMTime();
        }
    }

    // Trier les fichiers par date de modification (plus récents en premier)
    arsort($files);

    // Limiter le nombre de fichiers retournés
    $recentFiles = array_slice($files, 0, $limit, true);

    // Remplacer les "\" par des "/" dans les chemins
    return array_map(function($path) {
        return str_replace('\\', '/', $path);
    }, array_keys($recentFiles)); // Retourne uniquement les chemins des fichiers
}

function getRecentDirectories($directory, $limit = 5) {
    $dirs = [];

    // Fonction récursive pour parcourir les sous-dossiers
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );

    foreach ($iterator as $file) {
        // Vérifie si c'est un dossier et non un fichier comme .gitkeep
        if ($file->isDir() && $file->getFilename() !== '.gitkeep') {
            // Ajout du dossier avec son chemin complet et son temps de modification
            $dirs[$file->getPathname()] = $file->getMTime();
        }
    }

    // Trier les dossiers par date de modification (plus récents en premier)
    arsort($dirs);

    // Limiter le nombre de dossiers retournés
    $recentDirs = array_slice($dirs, 0, $limit, true);

    // Remplacer les "\" par des "/" dans les chemins
    return array_map(function($path) {
        return str_replace('\\', '/', $path);
    }, array_keys($recentDirs)); // Retourne uniquement les chemins des dossiers
}
