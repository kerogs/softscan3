<?php

// ? get url file name
$url = basename($_SERVER['PHP_SELF']);
?>

<nav>
    <ul>
        <a href="/">
            <li <?= $url == 'index.php' || $url == '' ? 'class="active"' : '' ?>><i class='bx bxs-home-heart'></i> Home</li>
        </a>
        <a href="/vertiscroll">
            <li>
                <i class='bx bxs-mobile'></i> VertiScrolll
            </li>
        </a>
        <a href="/photos-gifs">
            <li><i class='bx bxs-image-alt'></i> Photos & GIFs</li>
        </a>
        <a href="/videos">
            <li><i class='bx bxs-video-recording'></i> Videos</li>
        </a>
        <a href="/all">
            <li <?= $url == 'all.php' ? 'class="active"' : '' ?>><i class='bx bxs-collection'></i> All</li>
        </a>
        <a href="/collections">
            <li <li <?= $url == 'collections.php' ? 'class="active"' : '' ?>><i class='bx bxs-collection'></i> Collections</li>
        </a>
        <a href="/top100?by=top_vues">
            <li <?= $url == 'top100.php' ? 'class="active"' : '' ?>><i class='bx bxs-trophy'></i> Top 100</li>
        </a>
        <a href="/add/create">
            <li <?= $url == 'add.php' ? 'class="active"' : '' ?>><i class='bx bxs-add-to-queue'></i> Add</li>
        </a>
        <a href="/stats">
        <li <?= $url == 'stats.php' ? 'class="active"' : '' ?>><i class='bx bx-stats'></i> Stats</li>
        </a>
    </ul>
</nav>