<div class="right">
    <div class="lastCollectionrandom">
        <h3 class="title">
            <i class='bx bx-category-alt'></i> Dernières Collection
        </h3>
        <ul>
            <?php

            $recentFolders = getRecentDirectories('public_data/', 6);

            foreach ($recentFolders as $folder) {
                echo '<a href="all?dir=' . $folder . '"><li>' . basename($folder) . '</li></a>';
            }

            ?>
        </ul>
        <a href="collections"><button>Voir plus</button></a>
    </div>

    <div class="lastadd">
        <h3 class="title">
            <i class='bx bx-plus-circle'></i> Derniers ajouts
        </h3>

        <ul>
            <?php

            $recentFiles = getRecentFiles('public_data/', 8);

            // Affichage des fichiers les plus récents
            foreach ($recentFiles as $file) {

                // Obtenir l'extension du fichier
                $extension = pathinfo($file, PATHINFO_EXTENSION);

                // Obtenir le dernier nom de répertoire
                $lastDirName = basename(pathinfo($file, PATHINFO_DIRNAME));

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

                if(in_array($extension, $videoExtensions)){
                    echo '
                    <a href="view?url=' . $file . '">
                        <li>
                            <div class="img">
                                <img src="' . videoToThumbnailURL($file) . '" alt="">
                            </div>
                            <div class="info">
                                <div class="type">' . $icon . ' ' . $extension . '</div>
                                <div class="category">' . $lastDirName . '</div>
                            </div>
                        </li>
                    </a>
                    ';
                } else{
                    echo '
                    <a href="view?url=' . $file . '">
                        <li>
                            <div class="img">
                                <img src="' . $file . '" alt="">
                            </div>
                            <div class="info">
                                <div class="type">' . $icon . ' ' . $extension . '</div>
                                <div class="category">' . $lastDirName . '</div>
                            </div>
                        </li>
                    </a>
                    ';
                }
            }
            ?>
        </ul>
    </div>
</div>