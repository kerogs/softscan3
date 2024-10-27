<?php

require_once('../config.php');

$directoryToScan = 'public_data';
$returnNameType = 2;
$returnDirPath = true;

if(isset($_GET['o'])) {
    if($_GET['o'] == "image") {
        $authorise = array('jpg', 'png', 'jpeg', 'webp', 'svg');
    } elseif($_GET['o'] == "gif") {
        $authorise = array('gif');
    }
} else{
    $authorise = $imageExtensions;
}
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

                <div class="onlyphotosorgifs">
                    <a href="photos-gifs" <?= !isset($_GET['o']) ? 'class="active"' : '' ?>>
                        Photo & GIF
                    </a>
                    <a href="photos-gifs?o=photo" <?= $_GET['o'] == 'photo' ? 'class="active"' : '' ?>>
                        Photo
                    </a>
                    
                    <a href="photos-gifs?o=gif" <?= $_GET['o'] == 'gif' ? 'class="active"' : '' ?>>
                        GIF
                    </a>
                </div>

                <div class="galerie galsplit">
                    <div class="titlee">
                        <h2 class="title">
                            <i class='bx bxs-image-alt'></i> Photos & GIFs
                        </h2>
                    </div>
                    <div id="seeMore" class="content">
                        <?php
                        // Limiter les résultats pour la galerie
                        $resultsGalerie = array_slice($results, 0, 40);

                        foreach ($resultsGalerie as $image) {
                            if (in_array(pathinfo($image, PATHINFO_EXTENSION), $videoExtensions)) {
                                echo '<div><a href="view?url=' . htmlspecialchars($image) . '"><img src="' . htmlspecialchars(videoToThumbnailURL($image)) . '" alt=""></a></div>';
                            } else {
                                echo '<div><a href="view?url=' . htmlspecialchars($image) . '"><img src="' . htmlspecialchars($image) . '" alt=""></a></div>';
                            }
                        }
                        ?>
                    </div>
                    <button class="seeMoreAll" onclick="seeMoreGalerie()">Voir plus</button>
                </div>

            </div>

            <script>
                let offset = 40; // Initial offset is 40 because we already loaded 40 images

                function seeMoreGalerie() {
                    // Perform an Ajax request to get more images
                    const xhr = new XMLHttpRequest();
                    xhr.open('GET', 'src/php/load_more_phot.php?<?= isset($_GET['dir']) ? 'dir=' . $_GET['dir'] . '&' : '' ?>offset=' + offset, true);
                    xhr.onload = function() {
                        if (xhr.status === 200) {
                            // Directly append the server's response to the #seeMore container
                            const container = document.getElementById('seeMore');
                            container.innerHTML += xhr.responseText;

                            // If no more images are returned (empty response), hide the button
                            if (!xhr.responseText.trim()) {
                                document.querySelector('.seeMoreAll').style.display = 'none';
                            } else {
                                // Update the offset by counting the number of newly added images
                                const newImagesCount = container.querySelectorAll('img').length;
                                offset = newImagesCount;
                            }
                        }
                    };
                    xhr.send();
                }
            </script>

            <!-- right -->
            <?php require_once '../inc/right.php' ?>
        </div>
    </main>

    <?php require_once '../inc/script.php' ?>
</body>

</html>