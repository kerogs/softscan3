<?php

require_once('../config.php');

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
</head>

<body>

    <header>
        <div class="title"><a href="/">Soft<span>Scan</span><span>3</span></a></div>
        <div class="search">
            <form action="search.php" method="post">
                <label for="search">
                    <input type="text" id="search" name="search" placeholder="Search...">
                    <select name="" id="">
                        <option value="all">All</option>
                        <option value="image">Image</option>
                        <option value="video">Video</option>
                        <option value="gif">GIF</option>
                        <option value="other">Image/GIF</option>
                    </select>
                    <button type="submit"><i class='bx bx-search'></i></button>
                </label>
            </form>
        </div>
        <div class="lastupdate"></div>
        <div class="githubad">
            <div class="card">
                <h2>SS3 Github</h2>
                <a href="https://github.com/kerogs/softscan3" target="_blank"></a>
                <img src="./src/img/ssg/bannergithub.png" alt="">
            </div>
        </div>
    </header>

    <?php require_once '../inc/script.php' ?>
</body>

</html>