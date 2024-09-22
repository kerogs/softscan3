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
    function scanDirRecursive($dir, $returnNameType, $returnDirPath, $authorise, $ignore, $recursive, &$results, &$directories, $baseDir = "")
    {
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
                    (!empty($ignoreExtensions) && in_array($extension, $ignoreExtensions))
                ) {
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

function ss2_dirScanner($dir, $returnNameType = 0, $returnDirPath = false, $authorise = "*", $ignore = [], $recursive = false)
{
    $results = [];
    $directories = [];

    scanDirRecursive($dir, $returnNameType, $returnDirPath, $authorise, $ignore, $recursive, $results, $directories);

    return [
        'files' => $results,
        'directories' => $directories
    ];
}


function ss2_sizeIMG($url, $returnRatioHeight = false)
{
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


function getRecentFiles($directory, $limit = 5)
{
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
    return array_map(function ($path) {
        return str_replace('\\', '/', $path);
    }, array_keys($recentFiles)); // Retourne uniquement les chemins des fichiers
}

function getRecentDirectories($directory, $limit = 5)
{
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
    return array_map(function ($path) {
        return str_replace('\\', '/', $path);
    }, array_keys($recentDirs)); // Retourne uniquement les chemins des dossiers
}















function genFakeType()
{
    $fakeCategory = array(
        array(
            "name" => "Video",
            "icon" => "<i class='bx bxs-video-recording'></i>"
        ),
        array(
            "name" => "GIF",
            "icon" => "<i class='bx bxs-image-alt'></i>"
        ),
        array(
            "name" => "Photo",
            "icon" => "<i class='bx bxs-image-alt'></i>"
        ),
    );

    $randomKey = array_rand($fakeCategory);
    $randomCategory = $fakeCategory[$randomKey];

    return $randomCategory;
}

function genFakeCategory()
{
    $fakeCategory = array("Anime", "Animation", "Reddit", "Tendance", "jstn", "alsn", "supervideo", "fun", "manga", "manhwa", "manhua");
    return $fakeCategory[array_rand($fakeCategory)];
}













// url change for thumbnail
function videoToThumbnailURL($url)
{
    // Obtenir l'extension du fichier
    $extension = pathinfo($url, PATHINFO_EXTENSION);

    // Obtenir le dernier nom de répertoire
    $lastDirName = basename(pathinfo($url, PATHINFO_DIRNAME));

    $url = str_replace($extension, 'jpg', $url);
    $url = str_replace("public_data", "temp/thumbnail", $url);

    return $url;
}






function getUrlStats($jsonFilePath, $url)
{
    // Si le fichier JSON n'existe pas, le créer avec un tableau vide
    if (!file_exists($jsonFilePath)) {
        file_put_contents($jsonFilePath, json_encode([]));
    }

    // Lire le fichier JSON et le convertir en tableau PHP
    $json_data = json_decode(file_get_contents($jsonFilePath), true);

    // Chercher l'URL dans le fichier JSON
    foreach ($json_data as &$entry) {
        if ($entry['url'] === $url) {
            // Si l'URL existe, on retourne le nombre de like, dislike, et vue
            return [
                'like' => $entry['like'],
                'dislike' => $entry['dislike'],
                'vue' => $entry['vue']
            ];
        }
    }

    // Si l'URL n'existe pas, on crée une nouvelle entrée
    $new_entry = [
        'url' => $url,
        'like' => 0,
        'dislike' => 0,
        'vue' => 1
    ];

    // Ajouter la nouvelle entrée au tableau
    $json_data[] = $new_entry;

    // Sauvegarder les nouvelles données dans le fichier JSON
    file_put_contents($jsonFilePath, json_encode($json_data, JSON_PRETTY_PRINT));

    // Retourner les valeurs par défaut
    return [
        'like' => 0,
        'dislike' => 0,
        'vue' => 1
    ];
}




function getTopStats($statsFile, $topVues = 1, $topLikes = 1, $topDislikes = 1)
{
    // Vérifier si le fichier existe et le charger
    if (!file_exists($statsFile)) {
        return "Le fichier n'existe pas.";
    }

    $stats = json_decode(file_get_contents($statsFile), true);

    if (!$stats || !is_array($stats)) {
        return "Erreur lors du chargement des données JSON.";
    }

    // Trier les données selon les critères
    $topVuesUrls = [];
    $topLikesUrls = [];
    $topDislikesUrls = [];

    // Tri par vues (du plus élevé au plus bas)
    usort($stats, function ($a, $b) {
        return $b['vue'] - $a['vue'];
    });
    $topVuesUrls = array_slice($stats, 0, $topVues);

    // Tri par likes (du plus élevé au plus bas)
    usort($stats, function ($a, $b) {
        return $b['like'] - $a['like'];
    });
    $topLikesUrls = array_slice($stats, 0, $topLikes);

    // Tri par dislikes (du plus élevé au plus bas)
    usort($stats, function ($a, $b) {
        return $b['dislike'] - $a['dislike'];
    });
    $topDislikesUrls = array_slice($stats, 0, $topDislikes);

    // Retourner les résultats dans un tableau
    return [
        'top_vues' => $topVuesUrls,
        'top_likes' => $topLikesUrls,
        'top_dislikes' => $topDislikesUrls
    ];
}









// Fonction pour calculer le pourcentage de ressemblance
function similarityPercentage($str1, $str2)
{
    $str1 = basename($str1, PATHINFO_DIRNAME);
    similar_text($str1, $str2, $percent);
    return $percent; // Retourne directement le pourcentage de similarité
}

function searchEngine($values, $query)
{
    $result = [];

    // Convertir la requête en minuscule pour éviter la différenciation entre majuscules et minuscules
    $lowercaseQuery = strtolower($query);

    // Parcourir chaque valeur, calculer la similarité avec la requête, et l'ajouter aux résultats
    foreach ($values as $value) {
        // Convertir chaque valeur en minuscule pour éviter la différenciation entre majuscules et minuscules
        $lowercaseValue = strtolower($value);

        // Calculer la similarité entre la valeur et la requête
        $similarity = similarityPercentage($lowercaseValue, $lowercaseQuery);

        // Ajouter le résultat avec la similarité
        $result[$value] = round($similarity, 2);
    }

    // Trier les résultats par ordre décroissant de similarité
    arsort($result);

    return $result;
}
