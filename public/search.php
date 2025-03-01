<?php

require_once('../config.php');

$directoryToScan = 'public_data';
$returnNameType = 2;
$returnDirPath = true;
if ($_GET['type'] == "all") {
    $authorise = ['jpg', 'gif', 'png', 'jpeg', 'webp', 'svg', 'mp4', 'webm', 'mov', 'avi', 'mkv'];
} elseif ($_GET['type'] == "image") {
    $authorise = ['jpg', 'png', 'jpeg', 'webp', 'svg'];
} elseif ($_GET['type'] == "video") {
    $authorise = ['mp4', 'webm', 'mov', 'avi', 'mkv'];
} elseif ($_GET['type'] == "gif") {
    $authorise = ['gif'];
} elseif ($_GET['type'] == "other") {
    $authorise = ['jpg', 'png', 'jpeg', 'webp', 'svg', 'gif'];
}
$ignore = [];
$recursive = true;

$results = [];
$directories = [];

scanDirRecursive($directoryToScan, $returnNameType, $returnDirPath, $authorise, $ignore, $recursive, $results, $directories);

$results;
$directories;

$_SESSION['ss3_search'] = [$_GET['search'], $_GET['type']];

$ss3_Search_ShowNowType = $_SESSION['ss3_search'][1]; // ss3_Search_ShowNowType
$ss3_Search_ShowNowSearch = $_SESSION['ss3_search'][0]; // ss3_Search_ShowNowSearch


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

                <form action="search" class="searchsearch">
                    <div class="searchsearch__left">
                        <input type="search" name="search" placeholder="Search..." id="" value="<?= $_GET['search'] ?>">

                        <div class="catList">
                            <label for="all" <?= $_GET['type'] == "all" ? 'class="active"' : '' ?>>
                                All
                                <input type="radio" name="type" value="all" id="all" <?= $_GET['type'] == "all" ? 'checked' : '' ?>>
                            </label>
                            <label for="image" <?= $_GET['type'] == "image" ? 'class="active"' : '' ?>>
                                Image
                                <input type="radio" name="type" value="image" id="image" <?= $_GET['type'] == "image" ? 'checked' : '' ?>>
                            </label>
                            <label for="video" <?= $_GET['type'] == "video" ? 'class="active"' : '' ?>>
                                Video
                                <input type="radio" name="type" value="video" id="video" <?= $_GET['type'] == "video" ? 'checked' : '' ?>>
                            </label>
                            <label for="gif" <?= $_GET['type'] == "gif" ? 'class="active"' : '' ?>>
                                GIF
                                <input type="radio" name="type" value="gif" id="gif" <?= $_GET['type'] == "gif" ? 'checked' : '' ?>>
                            </label>
                            <label for="other" <?= $_GET['type'] == "other" ? 'class="active"' : '' ?>>
                                Other
                                <input type="radio" name="type" value="other" id="other" <?= $_GET['type'] == "other" ? 'checked' : '' ?>>
                            </label>
                        </div>
                    </div>
                    <div class="searchsearch__right">
                        <button type="submit">
                            <i class='bx bx-search'></i>
                        </button>
                    </div>
                </form>

                <script>
                    const allRadio = document.querySelectorAll('input[name="type"]');

                    allRadio.forEach(radio => {
                        radio.addEventListener('change', () => {
                            document.querySelectorAll('.catList label').forEach(label => {
                                label.classList.remove('active');
                            });

                            const selectedLabel = radio.closest('label');
                            selectedLabel.classList.add('active');
                        });
                    });
                </script>


                <?php if (strlen($_GET['search']) >= 3) : ?>

                    <div class="collectionShow">
                        <div class="titlee">
                            <h2 class="title">
                                <i class='bx bxs-collection'></i> Collections
                            </h2>
                        </div>

                        <ul>
                            <?php


                            $dirSearchEngine = searchEngine($directories, $_GET['search']);
                            foreach ($dirSearchEngine as $key => $value) {
                                if ($value > 80.00) {
                                    echo '<a href="all?dir=public_data/' . $key . '"><li style="border:1px solid hsl(290, 80%, 53%)" title="Pourcentage : ' . $value . '%"><p>' . basename($key) . '</p></li></a>';
                                } elseif ($value > 65.00) {
                                    echo '<a href="all?dir=public_data/' . $key . '"><li style="border:1px solid hsl(290, 80%, 75%)" title="Pourcentage : ' . $value . '%"><p>' . basename($key) . '</p></li></a>';
                                } elseif ($value > 50.00) {
                                    echo '<a href="all?dir=public_data/' . $key . '"><li style="border:1px solid hsl(43, 80%, 53%)" title="Pourcentage : ' . $value . '%"><p>' . basename($key) . '</p></li></a>';
                                } elseif ($value > 30.00) {
                                    echo '<a href="all?dir=public_data/' . $key . '"><li style="border:1px solid hsl(43, 80%, 75%)" title="Pourcentage : ' . $value . '%"><p>' . basename($key) . '</p></li></a>';
                                } elseif ($value > 15.00) {
                                    echo '<a href="all?dir=public_data/' . $key . '"><li style="border:1px solid hsl(0, 0%, 33%)" title="Pourcentage : ' . $value . '%"><p>' . basename($key) . '</p></li></a>';
                                }
                            }

                            ?>
                        </ul>
                    </div>

                    <div class="galerie galsplit">
                        <div class="titlee">
                            <h2 class="title">
                                <i class='bx bx-image-alt'></i> Galerie
                            </h2>
                        </div>
                        <div id="galerieShow" class="content">
                            <?php
                            // Limiter les résultats pour la galerie
                            $resultsGal = array_slice(searchEngine($results, $_GET['search']), 0, 100);

                            foreach ($resultsGal as $key => $value) {

                                $extension = pathinfo($key, PATHINFO_EXTENSION);

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
                                    if ($value > 80.00) {
                                        echo '<div style="border:3px solid hsl(290, 80%, 53%);" title="' . $value . '%"><a href="view?url=' . htmlspecialchars($key) . '"><div class="type">' . $icon . ' </div><img src="' . htmlspecialchars(videoToThumbnailURL($key)) . '" alt=""></a></div>';
                                    } elseif ($value > 65.00) {
                                        echo '<div style="border:3px solid hsl(290, 80%, 75%);" title="' . $value . '%"><a href="view?url=' . htmlspecialchars($key) . '"><div class="type">' . $icon . ' </div><img src="' . htmlspecialchars(videoToThumbnailURL($key)) . '" alt=""></a></div>';
                                    } elseif ($value > 50.00) {
                                        echo '<div style="border:3px solid hsl(43, 80%, 53%);" title="' . $value . '%"><a href="view?url=' . htmlspecialchars($key) . '"><div class="type">' . $icon . ' </div><img src="' . htmlspecialchars(videoToThumbnailURL($key)) . '" alt=""></a></div>';
                                    } elseif ($value > 30.00) {
                                        echo '<div style="border:3px solid hsl(43, 80%, 75%);" title="' . $value . '%"><a href="view?url=' . htmlspecialchars($key) . '"><div class="type">' . $icon . ' </div><img src="' . htmlspecialchars(videoToThumbnailURL($key)) . '" alt=""></a></div>';
                                    } elseif ($value > 15.00) {
                                        echo '<div style="border:3px solid hsl(0, 0%, 33%);" title="' . $value . '%"><a href="view?url=' . htmlspecialchars($key) . '"><div class="type">' . $icon . ' </div><img src="' . htmlspecialchars(videoToThumbnailURL($key)) . '" alt=""></a></div>';
                                    }
                                } else {
                                    if ($value > 80.00) {
                                        echo '<div style="border:3px solid hsl(290, 80%, 53%);" title="' . $value . '%"><a href="view?url=' . htmlspecialchars($key) . '"><div class="type">' . $icon . ' </div><img src="' . htmlspecialchars($key) . '" alt=""></a></div>';
                                    } elseif ($value > 65.00) {
                                        echo '<div style="border:3px solid hsl(290, 80%, 75%);" title="' . $value . '%"><a href="view?url=' . htmlspecialchars($key) . '"><div class="type">' . $icon . ' </div><img src="' . htmlspecialchars($key) . '" alt=""></a></div>';
                                    } elseif ($value > 50.00) {
                                        echo '<div style="border:3px solid hsl(43, 80%, 53%);" title="' . $value . '%"><a href="view?url=' . htmlspecialchars($key) . '"><div class="type">' . $icon . ' </div><img src="' . htmlspecialchars($key) . '" alt=""></a></div>';
                                    } elseif ($value > 30.00) {
                                        echo '<div style="border:3px solid hsl(43, 80%, 75%);" title="' . $value . '%"><a href="view?url=' . htmlspecialchars($key) . '"><div class="type">' . $icon . ' </div><img src="' . htmlspecialchars($key) . '" alt=""></a></div>';
                                    } elseif ($value > 15.00) {
                                        echo '<div style="border:3px solid hsl(0, 0%, 33%);" title="' . $value . '%"><a href="view?url=' . htmlspecialchars($key) . '"><div class="type">' . $icon . ' </div><img src="' . htmlspecialchars($key) . '" alt=""></a></div>';
                                    }
                                }
                            }
                            ?>
                        </div>
                    </div>
                <?php elseif (strlen($_GET['search']) < 3 && strlen($_GET['search']) > 0) : ?>

                    <div class="littlePopup alert">
                        <p>La valeur de recherche doit être supérieur ou égal à 3 characters.</p>
                    </div>

                <?php endif ?>

            </div>

            <!-- right -->
            <?php require_once '../inc/right.php' ?>
        </div>
    </main>

    <?php require_once '../inc/script.php' ?>
</body>

</html>