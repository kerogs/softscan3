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

        <div class="splitArea">
            <!-- left -->
            <div class="left">

                <div class="btnlist">
                    <a href="top100?by=top_vues"><button <?= isset($_GET['by']) && $_GET['by'] == 'top_vues' ? 'class="active"' : '' ?>><i class='bx bx-trophy'></i> <span>Top vues</span></button></a>
                    <a href="top100?by=top_likes"><button <?= isset($_GET['by']) && $_GET['by'] == 'top_likes' ? 'class="active"' : '' ?>> <i class="bx bx-like"></i> <span>Top Likes</span></button></a>
                    <a href="top100?by=top_dislikes"><button <?= isset($_GET['by']) && $_GET['by'] == 'top_dislikes' ? 'class="active"' : ''; ?>><i class="bx bx-dislike"></i> <span>Top dislikes</span></button></a>
                </div>


                <?php
                $topStats = getTopStats('temp/stats.json', 100, 100, 100);
                ?>


                <div class="topStats">
                    <ul>
                        <ul>
                            <?php

                            if ($topStats != "Le fichier n'existe pas.") {
                                foreach ($topStats[$_GET['by']] as $stat) {

                                    $extension = pathinfo($stat['url'], PATHINFO_EXTENSION);
                                    $lastDirName = basename(pathinfo($stat['url'], PATHINFO_DIRNAME));

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

                                    if ($stat['vue'] > 0) {
                                        if (in_array($extension, $videoExtensions)) {
                                            echo '
                                <a href="view?url=' . $stat['url'] . '">
                                    <li>
                                        <div class="img"><img src="' . videoToThumbnailURL($stat['url']) . '" alt=""></div>
                                        <div class="information">
                                            <div class="name"><span>' . $icon . '</span> <span>-</span> <span>' . $lastDirName . '</span></div>
                                            <div class="info">
                                                <div class="vues"><i class="bx bx-show"></i> ' . $stat['vue'] . '</div>
                                                <div class="like"><i class="bx bx-like"></i> ' . $stat['like'] . '</div>
                                                <div class="dislike"><i class="bx bx-dislike"></i> ' . $stat['dislike'] . '</div>
                                            </div>
                                        </div>
                                    </li>
                                </a>
                            ';
                                        } else {
                                            echo '
                                    <a href="view?url=' . $stat['url'] . '">
                                        <li>
                                            <div class="img"><img src="' . $stat['url'] . '" alt=""></div>
                                            <div class="information">
                                                <div class="name"><span>' . $icon . '</span> <span>-</span> <span>' . $lastDirName . '</span></div>
                                                <div class="info">
                                                    <div class="vues"><i class="bx bx-show"></i> ' . $stat['vue'] . '</div>
                                                    <div class="like"><i class="bx bx-like"></i> ' . $stat['like'] . '</div>
                                                    <div class="dislike"><i class="bx bx-dislike"></i> ' . $stat['dislike'] . '</div>
                                                </div>
                                            </div>
                                        </li>
                                    </a>
                                ';
                                        }
                                    } else {
                                        echo '<div class="noData">No data</div>';
                                    }
                                }
                            } else {
                                echo '<div class="noData">No data</div>';
                            }

                            ?>
                        </ul>
                    </ul>
                </div>

            </div>

            <!-- right -->
            <?php require_once '../inc/right.php' ?>
        </div>
    </main>

    <?php require_once '../inc/script.php' ?>
</body>

</html>