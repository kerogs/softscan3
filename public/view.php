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

    <div class="urlPath">
        <?php

        $urlExplodeDir = explode('/', $urlGet);
        $totalPath = "";

        foreach ($urlExplodeDir as $key => $value) {
            if($value != 'public_data'){
                $totalPath .= '/'.$value;
            } else{
                $totalPath .= $value;
            }

            if ($key < count($urlExplodeDir) - 1) {
                echo '<a href="all?dir=' . $totalPath . '">' . $value . '</a> <i class="bx bx-chevron-right"></i> ';
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


                <div class="infobox">

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
                            if(in_array($extension, $videoExtensions)){
                                echo '<div><a href="view?url=' . htmlspecialchars($image) . '"><img src="' . htmlspecialchars(videoToThumbnailURL($image)) . '" alt=""></a></div>';
                            } else{
                                echo '<div><a href="view?url=' . htmlspecialchars($image) . '"><img src="' . htmlspecialchars($image) . '" alt=""></a></div>';
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