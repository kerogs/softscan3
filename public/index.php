<?php

require_once('../config.php');

$directoryToScan = 'public_data';
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

// ? check for counting
// ? count
// ? All files in var $results

$filepath = __DIR__ . "/../public/temp/fileStats.json";

if (!file_exists($filepath)) {
    logs(__DIR__ . '/../server.log', "File not found (fileStats.json)", 404, "ERROR");

    logs(__DIR__ . '/../server.log', "Trying to create (fileStats.json)", 202, "TRY");

    // Mettre à jour le fichier
    $fileTotal = 1; // Remplacez par le calcul réel
    $imageTotal = 0; // Remplacez par le calcul réel
    $svgjpgpngwebpTotal = 0; // Remplacez par le calcul réel
    $gifTotal = 0; // Remplacez par le calcul réel
    $videoTotal = 0; // Remplacez par le calcul réel

    foreach ($results as $file) {
        $fileTotal++;
        if (in_array(pathinfo($file, PATHINFO_EXTENSION), ['svg', 'jpg', 'png', 'webp'])) {
            $svgjpgpngwebpTotal++;
            $imageTotal++;
        }
        if (in_array(pathinfo($file, PATHINFO_EXTENSION), ['gif'])) {
            $gifTotal++;
            $imageTotal++;
        }
        if (in_array(pathinfo($file, PATHINFO_EXTENSION), ['mp4', 'webm', 'mov', 'avi'])) {
            $videoTotal++;
        }
    }

    // Tentative de création du fichier avec un contenu par défaut
    $defaultContent = [
        "total" => 0,
        "imageTotal" => 0,
        "svgjpgpngwebpTotal" => 0,
        "gifTotal" => 0,
        "videoTotal" => 0,
        "lastUpdate" => date('Y-m-d H:i:s') // Met à jour avec l'heure actuelle
    ];

    file_put_contents($filepath, json_encode($defaultContent, JSON_PRETTY_PRINT));

    if (file_exists($filepath)) {
        logs(__DIR__ . '/../server.log', "File created (fileStats.json)", 201, "SUCCESS");
    }
}

// ? check if lastUpdate is older than 10 minutes
if (file_exists($filepath)) {
    $fileData = json_decode(file_get_contents($filepath), true);
    $lastUpdate = isset($fileData['lastUpdate']) ? $fileData['lastUpdate'] : null;

    // Vérifiez si la dernière mise à jour est plus ancienne que 10 minutes
    if ($lastUpdate && (time() - strtotime($lastUpdate) > 600)) {
        // Mettre à jour le fichier
        $fileTotal = 0; // Remplacez par le calcul réel
        $imageTotal = 0; // Remplacez par le calcul réel
        $svgjpgpngwebpTotal = 0; // Remplacez par le calcul réel
        $gifTotal = 0; // Remplacez par le calcul réel
        $videoTotal = 0; // Remplacez par le calcul réel

        foreach ($result as $file) {
            if (!in_array(pathinfo($file, PATHINFO_EXTENSION), $ignore)) {
                $fileTotal++;
                if (in_array(pathinfo($file, PATHINFO_EXTENSION), $authorise)) {
                    $imageTotal++;
                }
                if (in_array(pathinfo($file, PATHINFO_EXTENSION), ['svg', 'jpg', 'png', 'webp'])) {
                    $svgjpgpngwebpTotal++;
                }
                if (in_array(pathinfo($file, PATHINFO_EXTENSION), ['gif'])) {
                    $gifTotal++;
                }
                if (in_array(pathinfo($file, PATHINFO_EXTENSION), ['mp4', 'webm', 'mov', 'avi'])) {
                    $videoTotal++;
                }
            }
        }

        $content = [
            "total" => $fileTotal,
            "imageTotal" => $imageTotal,
            "svgjpgpngwebpTotal" => $svgjpgpngwebpTotal,
            "gifTotal" => $gifTotal,
            "videoTotal" => $videoTotal,
            "lastUpdate" => date('Y-m-d H:i:s') // Met à jour avec l'heure actuelle
        ];

        file_put_contents($filepath, json_encode($content, JSON_PRETTY_PRINT));
        logs(__DIR__ . '/../server.log', "File updated (fileStats.json)", 200, "SUCCESS");
    } else {
        logs(__DIR__ . '/../server.log', "No need to update Stats (fileStats.json)", 200, "INFO");
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

    <main>

        <div class="categoryRecoSplide" <?= count($results) < 12 ? 'style="display: none;"' : '' ?>>
            <div class="gradientLeft"></div>
            <div id="splide" class="splide">
                <div class="splide__track">
                    <ul class="splide__list">
                        <?php
                        // Limiter les résultats pour le carrousel
                        $resultForSplideImg = array_slice($results, 0, 12);

                        foreach ($resultForSplideImg as $result) {
                            // Obtenir l'extension du fichier
                            $extension = pathinfo($result, PATHINFO_EXTENSION);

                            // Obtenir le dernier nom de répertoire
                            $lastDirName = basename(pathinfo($result, PATHINFO_DIRNAME));

                            if (in_array($extension, ["jpg", "jpeg", "png", "gif", "webp"])) {
                                $icon = "<i class='bx bxs-image-alt'></i>";
                                $search = "search?s=&c=&t=image";
                            } elseif (in_array($extension, ["mp4", "webm", "mov", "avi"])) {
                                $icon = "<i class='bx bxs-video'></i>";
                                $search = "search?s=&c=&t=video";
                            } else {
                                $icon = "<i class='bx bxs-file'></i>";
                                $search = "search?s=&c=&t=all";
                            }

                            if (in_array($extension, $videoExtensions)) {
                                echo '
                                <li class="splide__slide">
                                    <div class="card">
                                        <a href="view?url=' . htmlspecialchars($result) . '"></a>
                                        <div class="filter"></div>
                                        <img src="' . htmlspecialchars(videoToThumbnailURL($result)) . '" alt="">
                                        <div class="type">
                                            <a href="' . htmlspecialchars($search) . '"><span>' . $icon . ' ' . htmlspecialchars($extension) . '</span></a>
                                            <a href="all?dir=' . urlencode(pathinfo($result, PATHINFO_DIRNAME)) . '"><span><i class="bx bxs-collection"></i> ' . htmlspecialchars($lastDirName) . '</span></a>
                                        </div>
                                    </div>
                                </li>
                            ';
                            } else {
                                echo '
                                <li class="splide__slide">
                                    <div class="card">
                                        <a href="view?url=' . htmlspecialchars($result) . '"></a>
                                        <div class="filter"></div>
                                        <img src="' . htmlspecialchars($result) . '" alt="">
                                        <div class="type">
                                            <a href="' . htmlspecialchars($search) . '"><span>' . $icon . ' ' . htmlspecialchars($extension) . '</span></a>
                                            <a href="all?dir=' . urlencode(pathinfo($result, PATHINFO_DIRNAME)) . '"><span><i class="bx bxs-collection"></i> ' . htmlspecialchars($lastDirName) . '</span></a>
                                        </div>
                                    </div>
                                </li>
                            ';
                            }
                        }
                        ?>
                    </ul>
                </div>

                <div class="splide__progress">
                    <div class="splide__progress__bar">
                    </div>
                </div>
            </div>

            <script>
                // Initialiser Splide
                new Splide('#splide', {
                    type: 'loop',
                    perPage: 6,
                    perMove: 1,
                    autoplay: true,
                    interval: 3500,
                    pauseOnHover: false,
                    pauseOnFocus: false,
                    arrows: true,
                    pagination: false,
                    gap: 16,

                    breakpoints: {
                        800: {
                            perPage: 3
                        },
                        600: {
                            perPage: 2
                        }
                    }
                }).mount();
            </script>
        </div>

        <div class="splitArea">
            <!-- left -->
            <div class="left">
                <div class="lastcontent" <?= !isset($_SESSION['recent_urls']) ? 'style="display: none;"' : "" ?>>
                    <h2 class="title"><i class='bx bx-history'></i> Contenu vu</h2>

                    <div class="cards">
                        <?php
                        if (isset($_SESSION['recent_urls']) && !empty($_SESSION['recent_urls'])) {

                            // Parcourir chaque URL stockée dans la session (jusqu'à 4 éléments)
                            foreach ($_SESSION['recent_urls'] as $url) {
                                $extension = pathinfo($url, PATHINFO_EXTENSION);

                                if (in_array($extension, $videoExtensions)) {
                                    echo '
                                    <div class="card">
                                        <a href="view?url=' . $url . '"></a>
                                        <img src="' . videoToThumbnailURL($url) . '" alt="">
                                        <div class="type">
                                            <a href="all?dir=' . pathinfo($url, PATHINFO_DIRNAME) . '">
                                                <span><i class="bx bxs-collection"></i> ' . basename(pathinfo($url, PATHINFO_DIRNAME)) . '</span>
                                            </a>
                                        </div>
                                    </div>';
                                } else {
                                    echo '
                                    <div class="card">
                                        <a href="view?url=' . $url . '"></a>
                                        <img src="' . $url . '" alt="">
                                        <div class="type">
                                            <a href="all?dir=' . pathinfo($url, PATHINFO_DIRNAME) . '">
                                                <span><i class="bx bxs-collection"></i> ' . basename(pathinfo($url, PATHINFO_DIRNAME)) . '</span>
                                            </a>
                                        </div>
                                    </div>';
                                }
                            }
                        } else {
                            echo "<p>Aucun contenu récent trouvé.</p>";
                        }

                        ?>
                    </div>
                </div>

                <div class="collectionShow" <?= count($directories) == 0 ? 'style="display: none;"' : '' ?>>
                    <div class="titlee">
                        <h2 class="title">
                            <i class='bx bxs-collection'></i> Collections
                        </h2>
                        <a href="collections"><button>Voir tout</button></a>
                    </div>

                    <ul>
                        <?php

                        for ($i = 0; $i < 18; $i++) {
                            $randVal = $directories[$i];
                            if ($i < count($directories) - 1) {
                                echo '<a href="all?dir=public_data/' . $randVal  . '"><li><p>' . basename($randVal) . '</p></li></a>';
                            }
                        }

                        ?>
                    </ul>
                </div>

                <div class="galerie galsplit" <?= count($results) == 0 ? 'style="display: none;"' : '' ?>>
                    <div class="titlee">
                        <h2 class="title">
                            <i class='bx bx-image-alt'></i> Galerie
                        </h2>
                        <a href="photos-gifs"><button>Voir tout</button></a>
                    </div>
                    <div id="galerieShow" class="content">
                        <?php
                        // Limiter les résultats pour la galerie
                        $resultsGalerie = array_slice($results, 0, 40);

                        foreach ($resultsGalerie as $image) {

                            $extension = pathinfo($image, PATHINFO_EXTENSION);

                            if (in_array($extension, $videoExtensions)) {
                            } else {
                                echo '<div><a href="view?url=' . htmlspecialchars($image) . '"><img src="' . htmlspecialchars($image) . '" alt=""></a></div>';
                            }
                        }
                        ?>
                    </div>
                </div>

                <div class="galerie galsplit" <?= count($results) == 0 ? 'style="display: none;"' : '' ?>>
                    <div class="titlee">
                        <h2 class="title">
                            <i class='bx bx-video'></i> Vidéos
                        </h2>
                        <a href="videos"><button>Voir tout</button></a>
                    </div>
                    <div id="videoShow" class="content">
                        <?php
                        // Limiter les résultats pour la galerie
                        $resultsVideo = array_slice($results, 0, 100);

                        $iVideoCount = 0; 

                        foreach ($resultsVideo as $image) {

                            $extension = pathinfo($image, PATHINFO_EXTENSION);

                            if (in_array($extension, $videoExtensions)) {
                                // add video
                                echo '<div><a href="view?url=' . htmlspecialchars($image) . '"><img src="' . htmlspecialchars(videoToThumbnailURL($image)) . '" alt=""></a></div>';

                                $iVideoCount++;
                            }
                        }

                        if ($iVideoCount == 0) {
                            echo "<p>Aucune vidéo.</p>";
                        }
                        ?>
                    </div>
                </div>

            </div>

            <!-- right -->
            <?php require_once '../inc/right.php' ?>
        </div>
    </main>

    <!-- recommendation -->
    <!-- <div class="recoFromView">
        <div class="titlee">
            <h2 class="title">
                <i class='bx bx-repost'></i> Recommandation
            </h2>
        </div>

        <div id="recoFromViewContentSplide" class="splide">
            <div class="splide__track">
                <ul class="splide__list">
                    <li class="splide__slide"><img src="https://placehold.it/500" alt="Image 1"></li>
                    <li class="splide__slide"><img src="https://placehold.it/500" alt="Image 2"></li>
                    <li class="splide__slide"><img src="https://placehold.it/500" alt="Image 3"></li>
                    <li class="splide__slide"><img src="https://placehold.it/500" alt="Image 4"></li>
                </ul>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var splide = new Splide('#recoFromViewContentSplide', {
                    type: 'loop',
                    perPage: 3,
                    autoplay: true,
                    interval: 3000,
                    pagination: true,
                    arrows: true,
                    gap: '1rem',
                });

                splide.mount();
            });
        </script>


    </div> -->

    <?php require_once '../inc/script.php' ?>
</body>

</html>