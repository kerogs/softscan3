<?php

require_once('../config.php');

if ($_GET['dir'] == true) {
    $directoryToScan = $_GET['dir'];
} else {
    $directoryToScan = 'public_data';
}
$returnNameType = 2;
$returnDirPath = true;
$authorise = ['jpg', 'gif', 'png', 'jpeg', 'webp', 'svg', 'mp4', 'webm', 'mov', 'avi'];
$ignore = [];

if($_GET['desrec'] == "true") {
    $recursive = false;
} else {
    $recursive = true;
}
$results = [];
$directories = [];

scanDirRecursive($directoryToScan, $returnNameType, $returnDirPath, $authorise, $ignore, $recursive, $results, $directories);

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

        <?php

            if(count($directories) > 0 && $_GET['dir'] == true && $_GET['desrec'] != "true" ){
                echo '
                <div class="collectionShow">
                <div class="titlee">
                    <h2 class="title">
                        <i class="bx bxs-collection"></i> Collections suivit
                    </h2>
                    <a href="collections"><button>Voir tout</button></a>
                </div>

                <ul>';

                for ($i = 0; $i < count($directories); $i++) {
                    $val = $directories[$i];
                    echo '<a href="all?dir='.$_GET[('dir')].'/'. $val  . '"><li><p>' . basename($val) . '</p></li></a>';
                }

                echo '
                    </ul>
                </div>
                ';

            }
        ?>

        <div class="galerie allgal">
            <div class="titlee">
                <h2 class="title">
                    <i class='bx bx-image-alt'></i> Galerie
                </h2>
                <?php
                
                    if(count($directories) > 0){
                        if($_GET['desrec'] == "true"){
                            echo '<a href="all?dir=' . $_GET['dir'] . '&desrec=false"><button>Activer Récursive</button></a>';
                        } else {
                            echo '<a href="all?dir=' . $_GET['dir'] . '&desrec=true"><button>Désactiver Récursive</button></a>';
                        }
                    }
                
                ?>
                <!-- <a href="all?dir=<?=$_GET['dir']?>&recursive=false"><button>Désactiver Récursive</button></a> -->
            </div>
            <div id="seeMore" class="content">
                <?php
                // Limiter les résultats pour la galerie
                $resultsGalerie = array_slice($results, 0, 40);

                foreach ($resultsGalerie as $image) {
                    echo '<div><a href="view?url=' . htmlspecialchars($image) . '"><img src="' . htmlspecialchars($image) . '" alt=""></a></div>';
                }
                ?>
            </div>
            <button class="seeMoreAll" onclick="seeMoreGalerie()">Voir plus</button>
        </div>

    </main>

    <?php require_once '../inc/script.php' ?>

    <script>
        let offset = 40; // Initial offset is 40 because we already loaded 40 images

        function seeMoreGalerie() {
            // Perform an Ajax request to get more images
            const xhr = new XMLHttpRequest();
            xhr.open('GET', 'src/php/load_more.php?<?= isset($_GET['dir']) ? 'dir=' . $_GET['dir'] . '&' : '' ?>offset=' + offset, true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    const images = JSON.parse(xhr.responseText);

                    // If there are images returned from the server
                    if (images.length > 0) {
                        const container = document.getElementById('seeMore');
                        images.forEach(function(image) {
                            const div = document.createElement('div');
                            div.innerHTML = '<a href="view?url=' + image + '"><img src="' + image + '" alt=""></a>';
                            container.appendChild(div);
                        });

                        // Increase the offset by the number of images loaded
                        offset += images.length;
                    } else {
                        // If no more images, hide the button
                        document.querySelector('.seeMoreAll').style.display = 'none';
                    }
                }
            };
            xhr.send();
        }
    </script>
</body>

</html>