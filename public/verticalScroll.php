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

$recursive = true;
$results = [];
$directories = [];

scanDirRecursive($directoryToScan, $returnNameType, $returnDirPath, $authorise, $ignore, $recursive, $results, $directories);

if ($_GET['index'] && is_numeric($_GET['index'])) {
    $index = $_GET['index'];
} else {
    $index = 0;
}

$json_file = './temp/stats.json';
// save view number by $url into ./temp/stats.json
if (!file_exists($json_file)) {
    file_put_contents($json_file, '[]');
}

// Lire le fichier JSON et le convertir en tableau PHP
$json_data = json_decode(file_get_contents($json_file), true);

// Vérifier si l'URL existe déjà dans le tableau
$url_exists = false;
foreach ($json_data as &$entry) {
    if ($entry['url'] === $results[$index]) {
        // L'URL existe, on incrémente le nombre de vues
        $entry['vue'] += 1;
        $url_exists = true;
        break;
    }
}

// Si l'URL n'existe pas, on crée une nouvelle entrée
if (!$url_exists) {
    $new_entry = [
        'url' => $results[$index],
        'like' => 0,
        'dislike' => 0,
        'vue' => 1
    ];
    $json_data[] = $new_entry;
}

// Écrire les données mises à jour dans le fichier JSON
file_put_contents($json_file, json_encode($json_data, JSON_PRETTY_PRINT));

$stats = getUrlStats($json_file, $results[$index]);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php require_once '../inc/head.php' ?>
    <title><?= $kpf_config["seo"]["title_short"] ?></title>
    <!-- <link rel="stylesheet" href="src/css/style.css"> -->
    <link rel="stylesheet" href="src/css/vertiscroll.css">

    <!-- src -->
    <link rel="stylesheet" href="./node_modules/boxicons/css/boxicons.min.css">
    <link rel="stylesheet" href="./node_modules/@splidejs/splide/dist/css/splide-core.min.css">
    <script src="./node_modules/@splidejs/splide/dist/js/splide.min.js"></script>
</head>

<body>

    <main>

        <div class="content">
            <?php

            $urlGet = $results[$index];
            $urlExtension = pathinfo($urlGet, PATHINFO_EXTENSION);
            $urlLastDirName = basename(pathinfo($urlGet, PATHINFO_DIRNAME));
            // print_r($urlGet);

            if ($urlExtension == 'mp4' || $urlExtension == 'webm' || $urlExtension == 'mov' || $urlExtension == 'avi') {
                echo '<video id="video" src="' . $urlGet . '" conrols></video>';
            } else {
                echo '<img src="' . $urlGet . '" alt="">';
            }

            ?>
        </div>
        <div id="playbtn">
            <div class="inner">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16">
                    <g fill="none">
                        <g clip-path="url(#gravityUiPlayFill0)">
                            <path fill="currentColor" fill-rule="evenodd" d="M14.756 10.164c1.665-.962 1.665-3.366 0-4.329L6.251.918C4.585-.045 2.5 1.158 2.5 3.083v9.834c0 1.925 2.085 3.128 3.751 2.164z" clip-rule="evenodd" />
                        </g>
                        <defs>
                            <clipPath id="gravityUiPlayFill0">
                                <path fill="currentColor" d="M0 0h16v16H0z" />
                            </clipPath>
                        </defs>
                    </g>
                </svg>
            </div>
        </div>

        <div class="infoarea">


            <?php if ($index !== 0) : ?>
                <a id="arrowUp" href="vertiscroll-js?dir=<?= $_GET['dir'] ?>&recursive=true&index=<?= $index - 1 ?>">
                    <div class="btn hover arrowUp">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                            <g fill="none">
                                <path d="M24 0v24H0V0zM12.593 23.258l-.011.002l-.071.035l-.02.004l-.014-.004l-.071-.035q-.016-.005-.024.005l-.004.01l-.017.428l.005.02l.01.013l.104.074l.015.004l.012-.004l.104-.074l.012-.016l.004-.017l-.017-.427q-.004-.016-.017-.018m.265-.113l-.013.002l-.185.093l-.01.01l-.003.011l.018.43l.005.012l.008.007l.201.093q.019.005.029-.008l.004-.014l-.034-.614q-.005-.019-.02-.022m-.715.002a.02.02 0 0 0-.027.006l-.006.014l-.034.614q.001.018.017.024l.015-.002l.201-.093l.01-.008l.004-.011l.017-.43l-.003-.012l-.01-.01z" />
                                <path fill="currentColor" d="M13.06 3.283a1.5 1.5 0 0 0-2.12 0L5.281 8.939a1.5 1.5 0 0 0 2.122 2.122L10.5 7.965V19.5a1.5 1.5 0 0 0 3 0V7.965l3.096 3.096a1.5 1.5 0 1 0 2.122-2.122z" />
                            </g>
                        </svg>
                    </div>
                </a>
            <?php endif; ?>


            <?php if ($index !== (count($results))) : ?>
                <a id="arrowDown" href="vertiscroll-js?dir=<?= $_GET['dir'] ?>&recursive=true&index=<?= $index + 1 ?>">
                    <div class="btn hover arrowDown">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16">
                            <path fill="currentColor" fill-rule="evenodd" d="M8 1.25a.75.75 0 0 1 .75.75v10.19l2.72-2.72a.75.75 0 1 1 1.06 1.06l-4 4a.75.75 0 0 1-1.06 0l-4-4a.75.75 0 1 1 1.06-1.06l2.72 2.72V2A.75.75 0 0 1 8 1.25" clip-rule="evenodd" />
                        </svg>
                    </div>
                </a>
            <?php endif; ?>
            <a href="/">
                <div class="btn hover">
                    <img src="./src/img/mascott.png" alt="">
                </div>
            </a>
            <div class="group">
                <div class="btn">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path fill="currentColor" d="M12 9a3 3 0 0 0-3 3a3 3 0 0 0 3 3a3 3 0 0 0 3-3a3 3 0 0 0-3-3m0 8a5 5 0 0 1-5-5a5 5 0 0 1 5-5a5 5 0 0 1 5 5a5 5 0 0 1-5 5m0-12.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5" />
                    </svg>
                </div>
                <div class="info">
                    <p><?= $stats['vue'] + 1 ?></p>
                </div>
            </div>
            <div class="group">
                <a href="action/likedislike.php?url=<?= urlencode($urlGet) ?>&like=1&dislike=0&verticalscroll=true&index=<?= $index ?>&vertidir=<?= $_GET['dir'] ?>">
                    <div class="btn hover">
                        <svg <?php echo ($_COOKIE[$urlGet] === 'like') ? 'class="like"' : '' ?> xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                            <path fill="currentColor" d="M18 21H8V8l7-7l1.25 1.25q.175.175.288.475t.112.575v.35L15.55 8H21q.8 0 1.4.6T23 10v2q0 .175-.037.375t-.113.375l-3 7.05q-.225.5-.75.85T18 21M6 8v13H2V8z" />
                        </svg>
                    </div>
                </a>
                <div class="info">
                    <p><?= $stats['like'] ?></p>
                </div>
            </div>
            <div class="group">
                <a href="action/likedislike.php?url=<?= urlencode($urlGet) ?>&dislike=1&like=0&verticalscroll=true&index=<?= $index ?>&vertidir=<?= $_GET['dir'] ?>">
                    <div class="btn hover">
                        <svg <?php echo ($_COOKIE[$urlGet] === 'dislike') ? 'class="dislike"' : '' ?> xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                            <path fill="currentColor" d="M22 15h-3V3h3a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1m-5.293 1.293l-6.4 6.4a.5.5 0 0 1-.654.047L8.8 22.1a1.5 1.5 0 0 1-.553-1.57L9.4 16H3a2 2 0 0 1-2-2v-2.104a2 2 0 0 1 .15-.762L4.246 3.62A1 1 0 0 1 5.17 3H16a1 1 0 0 1 1 1v11.586a1 1 0 0 1-.293.707" />
                        </svg>
                    </div>
                </a>
                <div class="info">
                    <p><?= $stats['dislike'] ?></p>
                </div>
            </div>
            <a href="all?dir=<?= dirname($results[$index]) ?>">
                <div class="btn hover">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path fill="currentColor" d="M4 20q-.825 0-1.412-.587T2 18V6q0-.825.588-1.412T4 4h6l2 2h8q.825 0 1.413.588T22 8v10q0 .825-.587 1.413T20 20z" />
                    </svg>
                </div>
            </a>
            <a href="view?url=<?= $results[$index] ?>">
                <div class="btn hover">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                        <path fill="currentColor" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10s10-4.48 10-10S17.52 2 12 2m1 15h-2v-6h2zm0-8h-2V7h2z" />
                    </svg>
                </div>
            </a>
        </div>
    </main>

</body>
<script>
    // Fonction principale pour initialiser les comportements
    function initializeScripts() {
        console.log('Réinitialisation des scripts...');
        // Gestion de la vidéo
        const video = document.getElementById('video');
        const playbtn = document.getElementById('playbtn');

        if (video) {
            video.volume = 0.05; // Volume défini à 10%
            video.play();
            video.loop = true;

            function videoPlayStop() {
                if (video.paused) {
                    video.play();
                    playbtn.style.display = 'none';
                } else {
                    video.pause();
                    playbtn.style.display = 'block';
                }
            }

            video.addEventListener('click', videoPlayStop);
            if (playbtn) {
                playbtn.addEventListener('click', videoPlayStop);
            }
        }

        // Gestion des boutons
        function handleArrowClick(button) {
            button.addEventListener('click', function(event) {
                event.preventDefault();

                // Récupérer l'attribut href
                const href = button.getAttribute('href');
                console.log('Nouveau href :', href);

                // Mise à jour de l'URL (sans rechargement)
                const url = new URL(window.location);
                const params = new URLSearchParams(href.split('?')[1]); // Extraire les paramètres de href
                url.search = params.toString(); // Mettre à jour les paramètres dans l'URL
                history.pushState(null, '', url); // Mettre à jour l'URL sans recharger la page
                console.log('URL mise à jour :', url.toString());

                // Fetch du contenu et mise à jour de <main>
                try {
                    fetch(href)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('HTTP error, status = ' + response.status);
                            }
                            return response.text();
                        })
                        .then(data => {
                            console.log('Données récupérées :', data);
                            const main = document.querySelector('main');
                            if (main) {
                                main.innerHTML = data;
                                // Réinitialiser les scripts après mise à jour
                                initializeScripts();
                            }
                        })
                        .catch(error => console.error(error));
                } catch (error) {
                    console.error(error);
                }
            });
        }

        const arrowDownbtn = document.getElementById('arrowDown');
        if (arrowDownbtn) {
            handleArrowClick(arrowDownbtn);
        } else {
            console.error("Element 'arrowDown' introuvable !");
        }

        const arrowUpBtn = document.getElementById('arrowUp');
        if (arrowUpBtn) {
            handleArrowClick(arrowUpBtn);
        } else {
            console.error("Element 'arrowUp' introuvable !");
        }
    }

    // Exécute la fonction d'initialisation au chargement de la page
    document.addEventListener('DOMContentLoaded', initializeScripts);
</script>




<?php require_once '../inc/script.php' ?>

</html>