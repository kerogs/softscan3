<?php

require_once('../config.php');

function scanDossiers($dossier)
{
    // Initialiser un tableau pour stocker les dossiers trouvés
    $dossiersTrouves = [];

    // Ouvrir le dossier
    $dir = new RecursiveDirectoryIterator($dossier, RecursiveDirectoryIterator::SKIP_DOTS);

    // Parcourir le dossier de manière récursive
    foreach (new RecursiveIteratorIterator($dir, RecursiveIteratorIterator::SELF_FIRST) as $element) {
        // Si l'élément est un dossier, on l'ajoute à la liste
        if ($element->isDir()) {
            $dossiersTrouves[] = $element->getPathname();
        }
    }

    // Retourner la liste des dossiers trouvés
    return $dossiersTrouves;
}

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
    <link rel="stylesheet" href="./node_modules/@splidejs/splide/dist/css/splide-core.min.css">
    <script src="./node_modules/@splidejs/splide/dist/js/splide.min.js"></script>
</head>

<body>

    <?php require_once '../inc/header.php' ?>

    <?php require_once '../inc/nav.php' ?>

    <main>

        <?php


        if (isset($_GET['s']) && $_GET['s'] == "ok") {
            echo '<div class="littlePopup ok"><p>Action réussit !</p></div>';
        } elseif (isset($_GET['s']) && $_GET['s'] == "ko") {
            echo '<div class="littlePopup warning"><p>Action echouée !</p></div>';
        }


        ?>
        <div class="btnlist">
            <a href="add/files"><button <?= isset($_GET['r']) && $_GET['r'] == "files" ? 'class="active"' : '' ?>>Import files</button></a>
            <a href="add/create"><button <?= isset($_GET['r']) && $_GET['r'] == "create" ? 'class="active"' : '' ?>>Create directory</button></a>
        </div>






        <!-- CREATE -->
        <?php if ($_GET['r'] == "create") : ?>
            <div class="formAdd">
                <h1>Importer un Dossier</h1>
                <form id="uploadForm" action="action/import-directory.php" method="POST" enctype="multipart/form-data">
                    <input type="file" id="filesInput" name="files[]" multiple webkitdirectory>
                    <input type="hidden" id="filePaths" name="filePaths">
                    <br><br>
                    <input type="submit" value="Importer le dossier">
                </form>
            </div>

            <script>
                document.getElementById('uploadForm').onsubmit = function(event) {
                    // Récupérer les fichiers sélectionnés
                    var files = document.getElementById('filesInput').files;
                    var filePaths = [];

                    // Parcourir les fichiers et obtenir leurs chemins relatifs
                    for (var i = 0; i < files.length; i++) {
                        filePaths.push(files[i].webkitRelativePath);
                    }

                    // Injecter les chemins relatifs dans un champ caché
                    document.getElementById('filePaths').value = JSON.stringify(filePaths);
                };
            </script>

            <div class="formAdd">
                <h1>Créer un dossier</h1>
                <form action="action/create-directory.php" method="POST">
                    <select name="dir" id="">
                        <option value="public_data">public_data (racine)</option>
                        <?php
                        $dossierScan = scanDossiers('public_data\\');

                        foreach ($dossierScan as $dossier) {
                            echo '<option value="' . $dossier . '">' . $dossier . '</option>';
                        }
                        ?>
                    </select>
                    <input type="text" name="name" placeholder="Nom du dossier" id="">
                    <input type="submit" value="Créer le dossiers">
                </form>
            </div>

            <div class="formAdd">
                <h1>Supprimer un dossier</h1>
                <form action="action/delete-directory.php" method="POST">
                    <select name="dir" id="">
                        <?php
                        $dossierScan = scanDossiers('public_data\\');

                        foreach ($dossierScan as $dossier) {
                            echo '<option value="' . $dossier . '">' . $dossier . '</option>';
                        }
                        ?>
                    </select>
                    <input type="submit" value="Supprimer dossier (et contenu)">
                </form>
            </div>

        <?php endif; ?>














        <!-- ! FILES -->
        <?php if ($_GET['r'] == "files") : ?>

            <div class="formAdd">
                <h1>Ajouter des fichiers</h1>
                <form action="action/upload-files.php" method="POST" enctype="multipart/form-data">
                    <select name="dir" id="">
                        <option value="public_data">public_data (racine)</option>
                        <?php
                        $dossierScan = scanDossiers('public_data\\');

                        foreach ($dossierScan as $dossier) {
                            echo '<option value="' . $dossier . '">' . $dossier . '</option>';
                        }
                        ?>
                    </select>
                    <input type="file" name="files[]" id="" multiple>
                    <input type="submit" value="Ajouter fichiers">
                </form>
            </div>

        <?php endif; ?>





















    </main>

    <?php require_once '../inc/script.php' ?>
</body>

</html>