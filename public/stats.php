<?php

require_once('../config.php');

function scanMap($dir)
{
    $result = [
        'stats' => [
            'JPG' => 0,
            'JPEG' => 0,
            'PNG' => 0,
            'WEBP' => 0,
            'SVG' => 0,
            'GIF' => 0,
            'MP4' => 0,
            'MOV' => 0,
            'AVI' => 0,
            'WEBM' => 0,
            'MKV' => 0
        ],
        'resume' => [
            'total' => 0,
            'total_video' => 0,
            'total_img' => 0,
            'total_gif' => 0
        ],
        'tree' => []
    ];

    // Liste des extensions à ignorer
    $ignoreExtensions = ['gitignore', 'git', 'mp3'];

    // Extensions des fichiers vidéo et image pour faciliter le comptage
    $imageExtensions = ['jpg', 'jpeg', 'png', 'webp', 'svg'];
    $videoExtensions = ['mp4', 'mov', 'avi', 'mkv', 'webm', 'mkv'];

    // Parcourir le répertoire de manière récursive
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );

    // Calculer la longueur du chemin de base
    $baseLength = strlen($dir) + 1; // +1 pour ignorer le séparateur de répertoire

    foreach ($iterator as $file) {
        $fileName = $file->getBasename();

        // Ignorer les fichiers/dossiers avec les extensions spécifiées
        $ext = strtolower($file->getExtension());
        if (in_array($ext, $ignoreExtensions) || strpos($fileName, '.git') !== false) {
            continue;
        }

        if ($file->isFile()) {
            // Compter les formats d'image dans 'stats'
            if (in_array($ext, $imageExtensions)) {
                $result['stats'][strtoupper($ext)]++;
                $result['resume']['total_img']++;
            }
            // Compter les vidéos dans 'stats'
            if (in_array($ext, $videoExtensions)) {
                $result['stats'][strtoupper($ext)]++;
                $result['resume']['total_video']++;
            }
            // Compter les GIFs dans 'stats'
            if ($ext === 'gif') {
                $result['stats']['GIF']++;
                $result['resume']['total_gif']++;
            }

            // Compter le total de fichiers traités
            $result['resume']['total']++;

            // Obtenir le chemin relatif par rapport à $dir
            $relativePath = substr($file->getRealPath(), $baseLength);
            $pathParts = explode(DIRECTORY_SEPARATOR, $relativePath);

            // Extraire le nom du fichier sans l'extension
            $fileBaseName = pathinfo($file->getBasename(), PATHINFO_FILENAME);

            // Ajouter le fichier à l'arborescence avec les détails
            $currentTree = &$result['tree'];

            foreach ($pathParts as $index => $part) {
                // Si c'est le dernier élément du chemin, il s'agit du fichier
                if ($index === count($pathParts) - 1) {
                    $currentTree[$fileBaseName . '.' . $ext] = [
                        'extension' => $ext,
                        'name' => $fileBaseName,
                        'folder_parent' => $pathParts[count($pathParts) - 2] ?? ''
                    ];
                } else {
                    if (!isset($currentTree[$part])) {
                        $currentTree[$part] = [];
                    }
                    $currentTree = &$currentTree[$part];
                }
            }
        }
    }

    return $result;
}


// data save map
$data = scanMap(__DIR__ . "/public_data");
file_put_contents(__DIR__ . '/../backend/map.json', json_encode($data, JSON_PRETTY_PRINT));

/**
 * Sauvegarde l'historique du résumé dans un fichier JSON avec la date du jour.
 *
 * @param array $resume Les données du résumé à enregistrer
 * @return void
 */
function saveHistory($resume)
{
    // Chemin du fichier d'historique
    $historyFile = __DIR__ . '/../backend/map_history.json';

    // Si le fichier existe, on charge son contenu
    if (file_exists($historyFile)) {
        $historyData = json_decode(file_get_contents($historyFile), true);
    } else {
        $historyData = [];
    }

    // Obtenir la date du jour au format AAAA/MM/JJ
    $today = date('Y/m/d');

    // Si l'entrée pour aujourd'hui existe, on la remplace, sinon on l'ajoute
    $historyData[$today] = [
        'total' => $resume['total'],
        'total_video' => $resume['total_video'],
        'total_img' => $resume['total_img'],
        'total_gif' => $resume['total_gif']
    ];

    // Sauvegarder l'historique mis à jour dans le fichier JSON
    file_put_contents($historyFile, json_encode($historyData, JSON_PRETTY_PRINT));
}

saveHistory($data['resume']);

$mapHistory = json_decode(file_get_contents(__DIR__ . '/../backend/map_history.json'), true);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <base href="/">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php require_once '../inc/head.php' ?>
    <title><?= $kpf_config["seo"]["title_short"] ?></title>
    <link rel="stylesheet" href="src/css/style.css">

    <!-- src -->
    <link rel="stylesheet" href="./node_modules/boxicons/css/boxicons.min.css">
    <!-- <link rel="stylesheet" href="./node_modules/@splidejs/splide/dist/css/splide-core.min.css">
    <script src="./node_modules/@splidejs/splide/dist/js/splide.min.js"></script> -->

    <!-- <script src="./node_modules/apexcharts/dist/apexcharts.min.js"></script> -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
</head>

<style>
    .apexcharts-menu.apexcharts-menu-open {
        color: #000;
    }
</style>

<body>

    <?php require_once '../inc/header.php' ?>

    <?php require_once '../inc/nav.php' ?>

    <main>
        <div class="statsServer">

            <div id="serverHistory"></div>

            <?php

            $serverHistory = [
                "nombreFichier" => [],
                "date" => []
            ];

            foreach ($mapHistory as $key => $value) {
                $serverHistory["nombreFichier"][] = $value["total"];
                $serverHistory["date"][] = $key;
            };

            ?>

            <script>
                var options = {
                    title: {
                        text: 'Nombre de fichier total',
                    },
                    theme: {
                        shadeTo: "dark",
                    },
                    tooltip: {
                        // change colors
                        theme: "dark",
                    },
                    chart: {
                        type: 'line',
                        height: "500px",
                        // background:"#000",
                        foreColor: "#fff",
                    },
                    markers: {
                        size: 6
                    },
                    stroke: {
                        curve: 'smooth',
                    },
                    series: [{
                        name: 'Fichier total',
                        data: <?= json_encode($serverHistory["nombreFichier"]) ?>
                    }],
                    xaxis: {
                        categories: <?= json_encode($serverHistory["date"]) ?>
                    },
                    colors: ["#c727e7"]
                }

                var chart = new ApexCharts(document.querySelector("#serverHistory"), options);

                chart.render();
            </script>






            <div class="split">
                <div>
                    <?php
                    $filteredStats = array_filter($data['stats'], function ($value) {
                        return $value > 0;
                    });

                    // Formater les données pour ApexCharts (chaque extension devient une "branche" avec x=extension et y=valeur)
                    $formattedStats = [];
                    foreach ($filteredStats as $ext => $count) {
                        $formattedStats[] = ['x' => $ext, 'y' => $count];
                    }
                    ?>

                    <div id="statsByExt"></div>

                    <script>
                        var options = {
                            title: {
                                text: 'Nombre de fichier total',
                            },
                            tooltip: {
                                // change colors
                                theme: "dark",
                            },
                            series: [{
                                data: <?php echo json_encode($formattedStats); ?>
                            }],
                            legend: {
                                show: false
                            },
                            chart: {
                                height: 500,
                                type: 'treemap',
                                foreColor: "#fff",
                            },
                            title: {
                                text: 'Fichier par extension',
                            },
                            colors: ["#c727e7", "#f1d283"]
                        };

                        var chart = new ApexCharts(document.querySelector("#statsByExt"), options);
                        chart.render();
                    </script>
                </div>
                <div>
                    <div id="statsByCat"></div>
                    <?php

                    $statsByCat = [
                        "total_video" => $data["resume"]["total_video"],
                        "total_img" => $data["resume"]["total_img"],
                        "total_gif" => $data["resume"]["total_gif"]
                    ];

                    ?>

                    <script>
                        var options = {
                            title: {
                                text: 'Nombre de fichier total',
                            },
                            theme: {
                                shadeTo: "dark",
                            },
                            tooltip: {
                                // change colors
                                theme: "dark",
                            },
                            series: [{
                                name: 'Fichiers',
                                data: [
                                    <?php echo $statsByCat["total_video"]; ?>,
                                    <?php echo $statsByCat["total_img"]; ?>,
                                    <?php echo $statsByCat["total_gif"]; ?>
                                ]
                            }],
                            chart: {
                                type: 'bar',
                                height: 500,
                                foreColor: "#fff",
                            },
                            plotOptions: {
                                bar: {
                                    borderRadius: 4,
                                    borderRadiusApplication: 'end',
                                    horizontal: true,
                                }
                            },
                            dataLabels: {
                                enabled: false
                            },
                            xaxis: {
                                categories: ['Videos', 'Images', 'GIFs'],
                            },
                            title: {
                                text: 'Fichier par catégories',
                            },
                            colors: ["#c727e7"]
                        };

                        var chart = new ApexCharts(document.querySelector("#statsByCat"), options);
                        chart.render();
                    </script>
                </div>
            </div>

            <div id="serverHistoryByCat"></div>

            <?php
            // On crée un tableau pour stocker les données des vidéos, images et GIF
            $serverHistory = [
                "total_video" => [],
                "total_img" => [],
                "total_gif" => [],
                "date" => []
            ];

            // On parcourt les données de $mapHistory et on remplit le tableau avec les valeurs correspondantes
            foreach ($mapHistory as $key => $value) {
                $serverHistory["total_video"][] = $value["total_video"];
                $serverHistory["total_img"][] = $value["total_img"];
                $serverHistory["total_gif"][] = $value["total_gif"];
                $serverHistory["date"][] = $key;
            }
            ?>

            <script>
                var options = {
                    title: {
                        text: 'Nombre de fichiers par catégorie (Vidéo, Image, GIF)',
                    },
                    tooltip: {
                        theme: "dark",
                    },
                    chart: {
                        type: 'line',
                        height: "500px",
                        foreColor: "#fff",
                    },
                    markers: {
                        size: 6
                    },
                    stroke: {
                        curve: 'smooth',
                    },
                    series: [{
                            name: 'Vidéo',
                            data: <?= json_encode($serverHistory["total_video"]) ?>
                        },
                        {
                            name: 'Image',
                            data: <?= json_encode($serverHistory["total_img"]) ?>
                        },
                        {
                            name: 'GIF',
                            data: <?= json_encode($serverHistory["total_gif"]) ?>
                        }
                    ],
                    xaxis: {
                        categories: <?= json_encode($serverHistory["date"]) ?>
                    },
                    colors: ["#f1d283", "#c727e7", "#FFF"], // Changer les couleurs pour chaque catégorie
                    legend: {
                        show: true,
                        position: 'top'
                    },
                    grid: {
                        borderColor: '#e7e7e7',
                    }
                };

                var chart = new ApexCharts(document.querySelector("#serverHistoryByCat"), options);
                chart.render();
            </script>

        </div>

    </main>

    <?php require_once '../inc/script.php' ?>
</body>

</html>