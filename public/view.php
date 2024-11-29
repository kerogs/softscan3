<?php

require_once('../config.php');

$urlGet = $_GET['url'];
$urlExtension = pathinfo($urlGet, PATHINFO_EXTENSION);
$urlLastDirName = basename(pathinfo($urlGet, PATHINFO_DIRNAME));

$directoryToScan = pathinfo($urlGet, PATHINFO_DIRNAME);
$returnNameType = 2;
$returnDirPath = true;
$authorise = ['jpg', 'gif', 'png', 'jpeg', 'webp', 'svg', 'mp4', 'webm', 'mov', 'avi'];
$ignore = [];
$recursive = true;

$results = [];
$directories = [];

scanDirRecursive($directoryToScan, $returnNameType, $returnDirPath, $authorise, $ignore, $recursive, $results, $directories);

$index = array_search($urlGet, $results);

shuffle($results);
shuffle($directories);



if (isset($_GET['url'])) {
    $new_url = $_GET['url'];

    if (!isset($_SESSION['recent_urls'])) {
        $_SESSION['recent_urls'] = [];
    }

    if (($key = array_search($new_url, $_SESSION['recent_urls'])) !== false) {
        unset($_SESSION['recent_urls'][$key]);
    }

    array_unshift($_SESSION['recent_urls'], $new_url);

    if (count($_SESSION['recent_urls']) > 4) {
        array_pop($_SESSION['recent_urls']);
    }
}



$json_file = './temp/stats.json';
// save view number by $url into ./temp/stats.json
if (!file_exists($json_file)) {
    file_put_contents($json_file, '[]');
}

$url = isset($_GET['url']) ? $_GET['url'] : null;

if ($url && $_GET['viewCounter'] !== 'noCount') {
    // Lire le fichier JSON et le convertir en tableau PHP
    $json_data = json_decode(file_get_contents($json_file), true);

    // Vérifier si l'URL existe déjà dans le tableau
    $url_exists = false;
    foreach ($json_data as &$entry) {
        if ($entry['url'] === $url) {
            // L'URL existe, on incrémente le nombre de vues
            $entry['vue'] += 1;
            $url_exists = true;
            break;
        }
    }

    // Si l'URL n'existe pas, on crée une nouvelle entrée
    if (!$url_exists) {
        $new_entry = [
            'url' => $url,
            'like' => 0,
            'dislike' => 0,
            'vue' => 1
        ];
        $json_data[] = $new_entry;
    }

    // Écrire les données mises à jour dans le fichier JSON
    file_put_contents($json_file, json_encode($json_data, JSON_PRETTY_PRINT));
}



$stats = getUrlStats($json_file, $url);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php require_once '../inc/head.php' ?>
    <title><?= $kpf_config["seo"]["title_short"] ?></title>
    <link rel="stylesheet" href="src/css/style.css">

    <!-- src -->
    <link rel="stylesheet" href="./node_modules/boxicons/css/boxicons.min.css">
    <link rel="stylesheet" href="./node_modules/@splidejs/splide/dist/css/splide-core.min.css">
    <script src="./node_modules/@splidejs/splide/dist/js/splide.min.js"></script>
</head>

<body>

    <?php require_once '../inc/header.php' ?>

    <?php require_once '../inc/nav.php' ?>


    <?php

    if (isset($_GET['viewCounter']) == "noCount") {
        echo '<div class="littlePopup ok"><p>Avis envoyé avec succès.</p></div>';
    }

    ?>




    <div class="urlPath">
        <?php

        $urlExplodeDir = explode('/', $urlGet);
        $totalPath = "";

        foreach ($urlExplodeDir as $key => $value) {
            if ($value != 'public_data') {
                $totalPath .= '/' . $value;
            } else {
                $totalPath .= $value;
            }

            if ($key < count($urlExplodeDir) - 1) {
                echo '<a href="all?dir=' . $totalPath . '">' . $value . '</a> <i class="bx bx-chevron-right"></i> ';
                // ? save 
                $_SESSION['lastContentView_Name'] = $value;
            } else {
                echo $value;
            }
        }
        ?>
    </div>

    <main>

        <div class="ambiantBackground">
            <?php

            if ($urlExtension == 'mp4' || $urlExtension == 'webm' || $urlExtension == 'mov' || $urlExtension == 'avi') {
                echo '<video src="' . $urlGet . '" controls></video>';
            } else {
                echo '<img src="' . $urlGet . '" alt="">';
            }

            ?>

            <div class="filter"></div>
        </div>

        <div class="splitArea">
            <div class="left">
                <div class="viewNow">
                    <?php

                    if ($urlExtension == 'mp4' || $urlExtension == 'webm' || $urlExtension == 'mov' || $urlExtension == 'avi') {
                        echo '<video src="' . $urlGet . '" controls></video>';
                    } else {
                        echo '<img src="' . $urlGet . '" alt="">';
                    }

                    ?>
                </div>


                <?php

                $cookieUrlKey = str_replace('.', '_', urlencode($urlGet));

                $_SESSION['lastContentView_Cat'] = $urlLastDirName;

                ?>

                <div class="infobox">

                    <!-- Download Icon -->
                    <a href="<?= $urlGet ?>" download="<?= basename($urlGet) ?>">
                        <div>
                            <i class='bx bx-download'></i> Télécharger
                        </div>
                    </a>

                    <div>
                        <i class='bx bx-show'></i> <?= $stats['vue'] ?> vues
                    </div>

                    <!-- Like Button -->
                    <a href="action/likedislike.php?url=<?= urlencode($urlGet) ?>&like=1&dislike=0">
                        <div <?php echo ($_COOKIE[$cookieUrlKey] === 'like') ? 'class="like"' : '' ?>>
                            <i class='bx bx-like'></i> <?= $stats['like'] ?>
                        </div>
                    </a>

                    <!-- Dislike Button -->
                    <a href="action/likedislike.php?url=<?= urlencode($urlGet) ?>&dislike=1&like=0">
                        <div <?php echo ($_COOKIE[$cookieUrlKey] === 'dislike') ? 'class="dislike"' : '' ?>>
                            <i class='bx bx-dislike'></i> <?= $stats['dislike'] ?>
                        </div>
                    </a>

                    <!-- see in vertiscroll -->
                    <a href="vertiscroll?dir=<?= pathinfo($urlGet, PATHINFO_DIRNAME) ?>&index=<?= $index ?>&recursive=true">
                        <div>
                            <i class='bx bxs-mobile'></i> VertiScroll
                        </div>
                    </a>
                </div>


                <div class="galerie galsplit">
                    <div class="titlee">
                        <h2 class="title">
                            <i class='bx bx-image-alt'></i> <span class="titlespan">En découvrir plus de</span> <span><?= $urlLastDirName ?></span>
                        </h2>
                        <a href="all?dir=<?= pathinfo($urlGet, PATHINFO_DIRNAME) ?>"><button>Voir tout</button></a>
                    </div>
                    <div id="galerieShow" class="content">
                        <?php
                        // Limiter les résultats pour la galerie
                        $resultsGalerie = array_slice($results, 0, 40);

                        foreach ($resultsGalerie as $image) {

                            $extension = pathinfo($image, PATHINFO_EXTENSION);

                            switch ($extension) {
                                case in_array($extension, $videoExtensions):
                                    $icon = '<i class="bx bxs-video-recording"></i> ' . $extension;
                                    break;
                                case in_array($extension, $imageExtensions):
                                    $icon = '<i class="bx bxs-image"></i> ' . $extension;
                                    break;
                                default:
                                    $icon = '<i class="bx bxs-file"></i> ' . $extension;
                                    break;
                            }

                            if (in_array($extension, $videoExtensions)) {
                                echo '<div><a href="view?url=' . htmlspecialchars($image) . '"><div class="type">' . $icon . ' </div><img src="' . htmlspecialchars(videoToThumbnailURL($image)) . '" alt=""></a></div>';
                            } else {
                                echo '<div><a href="view?url=' . htmlspecialchars($image) . '"><div class="type">' . $icon . ' </div><img src="' . htmlspecialchars($image) . '" alt=""></a></div>';
                            }
                        }
                        ?>
                    </div>
                </div>



            </div>
            <div class="right">
                <?php require_once '../inc/right.php' ?>
            </div>
        </div>



    </main>

    <?php require_once '../inc/script.php' ?>
</body>

</html>