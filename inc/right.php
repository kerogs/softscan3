<div class="right">

    <div class="surprendsmoi">
        <?php

        $surprendsMoiEmoji = scandir('src/img/emoji/');
        $surprendsMoiEmoji = array_values(array_diff($surprendsMoiEmoji, array('.', '..')));
        $surprendsMoiEmojiRandKey = array_rand($surprendsMoiEmoji);
        $surprendsMoiEmojiRand = $surprendsMoiEmoji[$surprendsMoiEmojiRandKey];
        $surprendsMoiEmojiRandKey2 = array_rand($surprendsMoiEmoji);
        $surprendsMoiEmojiRand2 = $surprendsMoiEmoji[$surprendsMoiEmojiRandKey2];

        ?>
        <a href="view?url=<?= $results[rand(0, count($results) - 1)] ?>">
            <button><img src="src/img/emoji/<?= $surprendsMoiEmojiRand2 ?>" alt=""> Surprends moi <img src="src/img/emoji/<?= $surprendsMoiEmojiRand ?>" alt=""></button>
        </a>
    </div>

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

                if (in_array($extension, $videoExtensions)) {
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
                } else {
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

    <?php
    // echo '<pre>';
    // print_r(getTopStats('temp/stats.json', 3, 2, 1));
    // echo '</pre>';

    $topStats = getTopStats('temp/stats.json', 3, 3, 1);
    ?>

    <!-- top vues -->
    <div class="topStats">
        <h3 class="title">
            <i class='bx bx-trophy'></i> Top vues
        </h3>
        <ul>
            <ul>
                <?php

                if ($topStats != "Le fichier n'existe pas.") {
                    foreach ($topStats['top_vues'] as $stat) {

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

    <!-- top like -->
    <div class="topStats">
        <h3 class="title">
            <i class="bx bx-like"></i> Top Likes
        </h3>
        <ul>
            <ul>
                <?php

                if ($topStats != "Le fichier n'existe pas.") {
                    foreach ($topStats['top_likes'] as $stat) {

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

                        if ($stat['like'] > 0) {
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

    <!-- top dislike -->
    <div class="topStats">
        <h3 class="title">
            <i class="bx bx-dislike"></i> Top dislike
        </h3>
        <ul>
            <ul>
                <?php

                if ($topStats != "Le fichier n'existe pas.") {
                    foreach ($topStats['top_dislikes'] as $stat) {

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

                        if ($stat['dislike'] > 0) {
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