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

    <div class="searchRealtime">
        <div class="input-container">
            <input placeholder="Search..." class="input-field" type="text">
            <label for="input-field" class="input-label"></label>
            <span class="input-highlight"></span>
        </div>
    </div>


    <main>

        <div class="collectionShow">
            <div class="titlee">
                <h2 class="title">
                    <i class='bx bxs-collection'></i> Collections
                </h2>
            </div>

            <ul>
                <?php

                for ($i = 0; $i < count($directories); $i++) {
                    $randVal = $directories[$i];
                    echo '<a href="all?dir=public_data/' . $randVal  . '"><li><p>' . basename($randVal) . '</p></li></a>';
                }

                ?>
            </ul>
        </div>

    </main>

    <script>
        const allCollections = document.querySelectorAll('.collectionShow ul a');
        const searchInput = document.querySelector('.input-field');

        // Fonction pour filtrer les éléments
        function filterItems() {
            const filterValue = searchInput.value.toLowerCase(); // Mettre la recherche en minuscule pour une comparaison insensible à la casse

            allCollections.forEach(item => {
                const text = item.textContent.toLowerCase(); // Récupérer le texte de chaque élément
                if (text.includes(filterValue)) {
                    item.style.display = ''; // Afficher si le texte correspond
                } else {
                    item.style.display = 'none'; // Masquer si le texte ne correspond pas
                }
            });
        }

        // Ajoute un écouteur d'événement sur la saisie
        searchInput.addEventListener('input', filterItems);
    </script>

    <?php require_once '../inc/script.php' ?>
</body>

</html>